<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\CourseEnrollment;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Spatie\Permission\Models\Role;
use App\Mail\StudentOfferMail;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = User::role('student');

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('mobile', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by enrollment status
        if ($request->has('enrollment_status') && $request->enrollment_status) {
            if ($request->enrollment_status === 'enrolled') {
                $query->whereHas('enrollments');
            } elseif ($request->enrollment_status === 'not_enrolled') {
                $query->whereDoesntHave('enrollments');
            }
        }

        $students = $query->withCount(['enrollments', 'payments', 'lessonProgress'])
            ->with('enrollments.book')
            ->latest()
            ->paginate(15);

        // Statistics
        $stats = [
            'total' => User::role('student')->count(),
            'active' => User::role('student')->where('status', 'active')->count(),
            'inactive' => User::role('student')->where('status', 'inactive')->count(),
            'suspended' => User::role('student')->where('status', 'suspended')->count(),
            'enrolled' => User::role('student')->whereHas('enrollments')->count(),
            'not_enrolled' => User::role('student')->whereDoesntHave('enrollments')->count(),
        ];

        return view('admin.students.index', compact('students', 'stats'));
    }

    public function create()
    {
        return view('admin.students.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'mobile' => 'nullable|string|unique:users,mobile',
            'password' => 'required|string|min:8|confirmed',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $student = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => trim($request->first_name . ' ' . $request->last_name), // Generate name for backwards compatibility
            'email' => $request->email,
            'mobile' => $request->mobile,
            'password' => Hash::make($request->password),
            'status' => $request->status,
            'email_verified_at' => now(),
        ]);

        $student->assignRole('student');

        return redirect()->route('admin.students.index')
            ->with('success', 'Student created successfully.');
    }

    public function show($student)
    {
        $student = User::role('student')->findOrFail($student);

        $student->load([
            'roles',
            'enrollments.book',
            'enrollments' => function($query) {
                $query->latest()->limit(10);
            },
            'payments.book' => function($query) {
                $query->latest()->limit(10);
            },
            'deviceBindings',
            'lessonProgress.lesson',
        ]);

        // Statistics for this student
        $studentStats = [
            'total_enrollments' => $student->enrollments()->count(),
            'completed_courses' => $student->enrollments()->where('status', 'completed')->count(),
            'total_payments' => $student->payments()->sum('amount'),
            'total_lessons_completed' => $student->lessonProgress()->where('is_completed', true)->count(),
            'devices_count' => $student->deviceBindings()->count(),
        ];

        return view('admin.students.show', compact('student', 'studentStats'));
    }

    public function edit($student)
    {
        $student = User::role('student')->findOrFail($student);
        return view('admin.students.edit', compact('student'));
    }

    public function update(Request $request, $student)
    {
        $student = User::role('student')->findOrFail($student);

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $student->id,
            'mobile' => 'nullable|string|unique:users,mobile,' . $student->id,
            'password' => 'nullable|string|min:8|confirmed',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $data = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => trim($request->first_name . ' ' . $request->last_name), // Generate name for backwards compatibility
            'email' => $request->email,
            'mobile' => $request->mobile,
            'status' => $request->status,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $student->update($data);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student updated successfully.');
    }

    public function destroy($student)
    {
        $student = User::role('student')->findOrFail($student);
        $student->delete();

        return redirect()->route('admin.students.index')
            ->with('success', 'Student deleted successfully.');
    }

    /**
     * Send email offers to selected students
     */
    public function bulkSendEmail(Request $request)
    {
        $request->validate([
            'student_ids' => 'nullable|array',
            'student_ids.*' => 'exists:users,id',
            'send_to_all' => 'nullable|boolean',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        try {
            // Validate email configuration before sending
            $this->validateEmailConfiguration();

            // Apply system email configuration
            $this->applyEmailConfiguration();

            // Get students to send to
            $sendToAll = filter_var($request->send_to_all, FILTER_VALIDATE_BOOLEAN);
            if ($sendToAll) {
                $students = User::role('student')->whereNotNull('email')->get();
            } else {
                $studentIds = $request->student_ids ?? [];
                if (empty($studentIds)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No students selected',
                    ], 400);
                }
                $students = User::role('student')
                    ->whereIn('id', $studentIds)
                    ->whereNotNull('email')
                    ->get();
            }

            $sentCount = 0;
            $failedCount = 0;
            $failedEmails = [];
            $totalStudents = $students->count();

            if ($totalStudents === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No students found with email addresses to send to.',
                    'sent_count' => 0,
                    'failed_count' => 0,
                ], 400);
            }

            foreach ($students as $student) {
                try {
                    // Validate student email
                    if (empty($student->email) || !filter_var($student->email, FILTER_VALIDATE_EMAIL)) {
                        throw new \Exception('Invalid email address: ' . ($student->email ?? 'empty'));
                    }

                    // Personalize message
                    $personalizedMessage = str_replace('{name}', $student->name ?? 'Student', $request->message);

                    // Apply configuration for each email (in case settings changed)
                    $this->applyEmailConfiguration();

                    // Send email using Mailable class with system configuration
                    Mail::to($student->email, $student->name ?? 'Student')
                        ->send(new StudentOfferMail(
                            $request->subject ?? 'Notification',
                            $personalizedMessage,
                            $student->name ?? ''
                        ));

                    $sentCount++;
                } catch (\Exception $e) {
                    // Safely extract error message - handle cases where getMessage() returns an object
                    $errorMessage = $this->extractErrorMessage($e);

                    // Log detailed error information
                    $logData = [
                        'student_id' => $student->id,
                        'email' => $student->email,
                        'error_message' => $errorMessage,
                        'exception_class' => get_class($e),
                        'exception_code' => $e->getCode(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                    ];

                    // Add previous exception if available
                    if ($e->getPrevious()) {
                        $logData['previous_exception'] = [
                            'class' => get_class($e->getPrevious()),
                            'message' => $e->getPrevious()->getMessage(),
                        ];
                    }

                    Log::error('Failed to send email to student', $logData);
                    $failedCount++;

                    // Get user-friendly error and ensure it's a clean string
                    $friendlyError = $this->getUserFriendlyErrorMessage($errorMessage);

                    // Final safety check - ensure error is a simple string
                    $cleanError = $this->ensureString($friendlyError);

                    $failedEmails[] = [
                        'email' => (string) ($student->email ?? ''),
                        'name' => (string) ($student->name ?? 'Unknown'),
                        'error' => $cleanError,
                    ];
                }
            }

            // Determine success status
            $success = $sentCount > 0;
            $message = $this->buildEmailResultMessage($sentCount, $failedCount, $totalStudents);

            // Ensure all error messages in failed_emails are clean strings (sanitize for JSON)
            $sanitizedFailedEmails = array_map(function($item) {
                return [
                    'email' => (string) ($item['email'] ?? ''),
                    'name' => (string) ($item['name'] ?? 'Unknown'),
                    'error' => $this->ensureString($item['error'] ?? 'Unknown error'),
                ];
            }, $failedEmails);

            return response()->json([
                'success' => $success,
                'sent_count' => $sentCount,
                'failed_count' => $failedCount,
                'total_count' => $totalStudents,
                'message' => $message,
                'failed_emails' => $sanitizedFailedEmails,
            ]);

        } catch (\Exception $e) {
            $errorMsg = $this->extractErrorMessage($e);

            Log::error('Bulk email send error: ' . $errorMsg, [
                'trace' => $e->getTraceAsString(),
                'exception_class' => get_class($e),
            ]);
            $friendlyMsg = $this->getUserFriendlyErrorMessage($errorMsg);
            $cleanMsg = $this->ensureString($friendlyMsg);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while sending emails: ' . $cleanMsg,
                'sent_count' => 0,
                'failed_count' => 0,
            ], 500);
        }
    }

    /**
     * Send SMS offers to selected students
     */
    public function bulkSendSMS(Request $request)
    {
        $request->validate([
            'student_ids' => 'nullable|array',
            'student_ids.*' => 'exists:users,id',
            'send_to_all' => 'nullable|boolean',
            'message' => 'required|string|max:160',
        ]);

        try {
            // Get students to send to
            $sendToAll = filter_var($request->send_to_all, FILTER_VALIDATE_BOOLEAN);
            if ($sendToAll) {
                $students = User::role('student')
                    ->whereNotNull('mobile')
                    ->where('mobile', '!=', '')
                    ->get();
            } else {
                $studentIds = $request->student_ids ?? [];
                if (empty($studentIds)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No students selected',
                    ], 400);
                }
                $students = User::role('student')
                    ->whereIn('id', $studentIds)
                    ->whereNotNull('mobile')
                    ->where('mobile', '!=', '')
                    ->get();
            }

            $sentCount = 0;
            $failedCount = 0;
            $failedMobiles = [];
            $totalStudents = $students->count();

            if ($totalStudents === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No students found with mobile numbers to send SMS to.',
                    'sent_count' => 0,
                    'failed_count' => 0,
                ], 400);
            }

            foreach ($students as $student) {
                try {
                    // Personalize message
                    $personalizedMessage = str_replace('{name}', $student->name, $request->message);

                    // Send SMS using SMS service
                    $smsService = new \App\Services\SMSService();
                    $result = $smsService->send($student->mobile, $personalizedMessage);

                    if ($result) {
                        $sentCount++;
                    } else {
                        $failedCount++;
                        $failedMobiles[] = [
                            'mobile' => $student->mobile,
                            'name' => $student->name,
                            'error' => 'SMS sending failed. Please check SMS service configuration.',
                        ];
                    }
                } catch (\Exception $e) {
                    // Safely extract error message
                    $errorMessage = $this->extractErrorMessage($e);

                    Log::error('Failed to send SMS to student: ' . $student->id, [
                        'error' => $errorMessage,
                        'mobile' => $student->mobile,
                        'exception_class' => get_class($e),
                    ]);
                    $failedCount++;
                    $failedMobiles[] = [
                        'mobile' => $student->mobile ?? '',
                        'name' => $student->name ?? 'Unknown',
                        'error' => 'SMS service error: ' . substr($errorMessage, 0, 100),
                    ];
                }
            }

            // Determine success status
            $success = $sentCount > 0;
            $message = $this->buildSMSResultMessage($sentCount, $failedCount, $totalStudents);

            // Ensure all error messages in failed_mobiles are clean strings (sanitize for JSON)
            $sanitizedFailedMobiles = array_map(function($item) {
                return [
                    'mobile' => (string) ($item['mobile'] ?? ''),
                    'name' => (string) ($item['name'] ?? 'Unknown'),
                    'error' => $this->ensureString($item['error'] ?? 'Unknown error'),
                ];
            }, $failedMobiles);

            return response()->json([
                'success' => $success,
                'sent_count' => $sentCount,
                'failed_count' => $failedCount,
                'total_count' => $totalStudents,
                'message' => $message,
                'failed_mobiles' => $sanitizedFailedMobiles,
            ]);

        } catch (\Exception $e) {
            $errorMsg = $this->extractErrorMessage($e);

            Log::error('Bulk SMS send error: ' . $errorMsg, [
                'trace' => $e->getTraceAsString(),
                'exception_class' => get_class($e),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while sending SMS: ' . $errorMsg,
                'sent_count' => 0,
                'failed_count' => 0,
            ], 500);
        }
    }

    /**
     * Build SMS result message
     */
    private function buildSMSResultMessage(int $sentCount, int $failedCount, int $totalCount): string
    {
        if ($sentCount === 0 && $failedCount > 0) {
            return "Failed to send all {$totalCount} SMS. Please check your SMS service configuration.";
        } elseif ($sentCount > 0 && $failedCount === 0) {
            return "Successfully sent {$sentCount} SMS to all selected students.";
        } elseif ($sentCount > 0 && $failedCount > 0) {
            return "Successfully sent {$sentCount} SMS, but {$failedCount} SMS failed. Please check the error details.";
        } else {
            return "No SMS were sent. Please check your SMS service configuration.";
        }
    }

    /**
     * Build email result message
     */
    private function buildEmailResultMessage(int $sentCount, int $failedCount, int $totalCount): string
    {
        if ($sentCount === 0 && $failedCount > 0) {
            return "Failed to send all {$totalCount} email(s). Please check your email configuration in Settings.";
        } elseif ($sentCount > 0 && $failedCount === 0) {
            return "Successfully sent {$sentCount} email(s) to all selected students.";
        } elseif ($sentCount > 0 && $failedCount > 0) {
            return "Successfully sent {$sentCount} email(s), but {$failedCount} email(s) failed. Please check the error details.";
        } else {
            return "No emails were sent. Please check your email configuration.";
        }
    }

    /**
     * Safely extract error message from exception
     */
    private function extractErrorMessage(\Exception $e): string
    {
        try {
            // Try to get message from exception
            $message = $e->getMessage();

            // If message is an object, try to convert it
            if (is_object($message)) {
                // Check if it's a Message object from Mail
                if ($message instanceof \Illuminate\Mail\Message) {
                    return 'Email sending failed. Please check your email configuration in Admin Settings.';
                }

                // Try to convert to string
                if (method_exists($message, '__toString')) {
                    $message = (string) $message;
                } else {
                    $message = 'Email sending failed. Please check your email configuration.';
                }
            }

            // Ensure it's a string
            $message = (string) $message;

            // Check previous exception for more details
            $previous = $e->getPrevious();
            if ($previous) {
                try {
                    $previousMessage = $previous->getMessage();
                    if (is_string($previousMessage) && !empty($previousMessage) && !is_object($previousMessage)) {
                        // Use previous message if it's more specific
                        if (strlen($previousMessage) > 10) {
                            $message = $previousMessage;
                        }
                    }
                } catch (\Exception $ex) {
                    // Ignore errors in previous exception handling
                }
            }

            return $message;
        } catch (\Exception $ex) {
            // Fallback if anything goes wrong
            return 'Email sending failed. Please check your email configuration in Admin Settings.';
        }
    }

    /**
     * Ensure value is a clean string (no objects, no complex types)
     */
    private function ensureString($value): string
    {
        // If it's already a string, return it
        if (is_string($value)) {
            // Remove any error messages about htmlspecialchars or type errors
            if (strpos($value, 'htmlspecialchars') !== false ||
                strpos($value, 'must be of type string') !== false ||
                strpos($value, 'Illuminate\\Mail\\Message') !== false) {
                return 'Email sending failed. Please check your email configuration in Admin Settings.';
            }
            return $value;
        }

        // If it's an object, return generic message
        if (is_object($value)) {
            return 'Email sending failed. Please check your email configuration in Admin Settings.';
        }

        // Try to convert to string
        try {
            $stringValue = (string) $value;
            // Check if conversion resulted in object description
            if (strpos($stringValue, 'object') !== false || strpos($stringValue, 'Message') !== false) {
                return 'Email sending failed. Please check your email configuration in Admin Settings.';
            }
            return $stringValue;
        } catch (\Exception $e) {
            return 'Email sending failed. Please check your email configuration in Admin Settings.';
        }
    }

    /**
     * Get user-friendly error message
     */
    private function getUserFriendlyErrorMessage($errorMessage): string
    {
        // Ensure error message is a string - handle all edge cases including Mail Message objects
        if (!is_string($errorMessage)) {
            if (is_object($errorMessage)) {
                // Check if it's a Mail Message object (common issue with Laravel Mail exceptions)
                if ($errorMessage instanceof \Illuminate\Mail\Message ||
                    strpos(get_class($errorMessage), 'Message') !== false) {
                    return 'Email sending failed. Please check your email configuration in Admin Settings.';
                }

                // Try to convert to string
                if (method_exists($errorMessage, '__toString')) {
                    try {
                        $errorMessage = (string) $errorMessage;
                    } catch (\Exception $e) {
                        return 'Email sending failed. Please check your email configuration.';
                    }
                } else {
                    return 'Email sending failed. Please check your email configuration.';
                }
            } else {
                $errorMessage = (string) $errorMessage;
            }
        }

        // Final safety check - ensure it's still a string
        if (!is_string($errorMessage)) {
            return 'Email sending failed. Please check your email configuration.';
        }

        // Common error messages and their user-friendly versions
        $errorMap = [
            'Connection could not be established' => 'Email server connection failed. Please check your SMTP host and port settings.',
            'Connection timed out' => 'Email server connection timed out. Please check your SMTP host and port.',
            'stream_socket_client(): unable to connect' => 'Cannot connect to email server. Please check your SMTP host and port.',
            'stream_socket_client' => 'Cannot connect to email server. Please check your SMTP host and port settings.',
            'Authentication failed' => 'Email authentication failed. Please check your email username and password. For Gmail, use an App Password.',
            'Could not authenticate' => 'Email authentication failed. Please check your email credentials. For Gmail, use an App Password.',
            '535-5.7.8' => 'Gmail authentication failed. Please use an App Password instead of your regular password.',
            'Invalid address' => 'Invalid email address format.',
            'Could not instantiate mailer' => 'Email configuration error. Please check your email settings.',
            'SSL' => 'SSL/TLS connection error. Please check your encryption settings (TLS for port 587, SSL for port 465).',
            'TLS' => 'TLS connection error. Please check your encryption settings.',
            'SMTP connect() failed' => 'SMTP connection failed. Please check your SMTP host, port, and encryption settings.',
            'Username and Password not accepted' => 'Email authentication failed. Please check your username and password. For Gmail, use an App Password.',
        ];

        foreach ($errorMap as $key => $message) {
            if (stripos($errorMessage, $key) !== false) {
                return $message;
            }
        }

        // Return the actual error message if it's helpful, otherwise generic message
        if (strlen($errorMessage) < 200 && !stripos($errorMessage, 'stack trace')) {
            return 'Error: ' . substr($errorMessage, 0, 150);
        }

        return 'Email sending failed. Please check your email configuration in Admin Settings.';
    }

    /**
     * Validate email configuration
     */
    private function validateEmailConfiguration(): void
    {
        $mailHost = SystemSetting::getValue('mail_host');
        $mailPort = SystemSetting::getValue('mail_port');
        $mailUsername = SystemSetting::getValue('mail_username');
        $mailPassword = SystemSetting::getValue('mail_password');
        $mailEncryption = SystemSetting::getValue('mail_encryption');

        if (empty($mailHost)) {
            throw new \Exception('Mail host is not configured. Please set it in Admin Settings → Email Settings.');
        }

        if (empty($mailPort)) {
            throw new \Exception('Mail port is not configured. Please set it in Admin Settings → Email Settings.');
        }

        if (empty($mailUsername)) {
            throw new \Exception('Mail username is not configured. Please set it in Admin Settings → Email Settings.');
        }

        if (empty($mailPassword)) {
            throw new \Exception('Mail password is not configured. Please set it in Admin Settings → Email Settings.');
        }

        // For Gmail, check if username is full email address
        if (stripos($mailHost, 'gmail.com') !== false && stripos($mailUsername, '@') === false) {
            Log::warning('Gmail username should be full email address', [
                'username' => $mailUsername,
                'host' => $mailHost,
            ]);
        }
    }

    /**
     * Apply email configuration from SystemSetting
     */
    private function applyEmailConfiguration(): void
    {
        // Clear mail config cache
        app()['mail.manager']->forgetMailers();

        // Get email settings from SystemSetting
        $mailDriver = SystemSetting::getValue('mail_driver', config('mail.default'));
        $mailHost = SystemSetting::getValue('mail_host', config('mail.mailers.smtp.host'));
        $mailPort = SystemSetting::getValue('mail_port', config('mail.mailers.smtp.port'));
        $mailUsername = SystemSetting::getValue('mail_username', config('mail.mailers.smtp.username'));
        $mailPassword = SystemSetting::getValue('mail_password', config('mail.mailers.smtp.password'));
        $mailEncryption = SystemSetting::getValue('mail_encryption', 'tls');
        $mailFromAddress = SystemSetting::getValue('mail_from_address', SystemSetting::getValue('site_email', config('mail.from.address')));
        $mailFromName = SystemSetting::getValue('mail_from_name', SystemSetting::getValue('site_name', config('mail.from.name')));

        // Apply mail driver
        if ($mailDriver) {
            Config::set('mail.default', $mailDriver);
        }

        // Apply SMTP configuration - always set values even if empty to override defaults
        Config::set('mail.mailers.smtp.host', $mailHost ?: config('mail.mailers.smtp.host', '127.0.0.1'));
        Config::set('mail.mailers.smtp.port', $mailPort ?: config('mail.mailers.smtp.port', 2525));
        Config::set('mail.mailers.smtp.username', $mailUsername ?: config('mail.mailers.smtp.username'));
        Config::set('mail.mailers.smtp.password', $mailPassword ?: config('mail.mailers.smtp.password'));
        // Set encryption - default to 'tls' if not set
        $encryption = $mailEncryption ?: 'tls';
        Config::set('mail.mailers.smtp.encryption', $encryption);

        // Apply from address and name
        Config::set('mail.from.address', $mailFromAddress ?: config('mail.from.address'));
        Config::set('mail.from.name', $mailFromName ?: config('mail.from.name'));

        // Log configuration for debugging (without password)
        Log::info('Email configuration applied', [
            'driver' => $mailDriver,
            'host' => $mailHost,
            'port' => $mailPort,
            'username' => $mailUsername,
            'encryption' => $mailEncryption,
            'from_address' => $mailFromAddress,
            'from_name' => $mailFromName,
        ]);
    }
}
