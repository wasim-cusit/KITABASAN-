<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotificationSetting;
use App\Models\ThemeSetting;
use App\Models\SystemSetting;
use App\Models\PaymentMethod;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $themeSettings = ThemeSetting::getGrouped();
        $systemSettings = SystemSetting::getGrouped();
        $paymentMethods = PaymentMethod::orderBy('order')->get();
        $languages = Language::orderBy('order')->get();
        $adminNotification = AdminNotificationSetting::firstOrCreate(
            ['user_id' => Auth::id()],
            [
                'email_new_students' => true,
                'email_new_teachers' => true,
                'email_new_courses' => true,
                'email_course_updates' => true,
                'email_device_bindings' => true,
                'email_device_reset_requests' => true,
            ]
        );

        return view('admin.settings.index', compact('themeSettings', 'systemSettings', 'paymentMethods', 'languages', 'adminNotification'));
    }

    public function update(Request $request)
    {
        // Update theme settings
        if ($request->has('theme_settings')) {
            $validated = $request->validate([
                'theme_settings' => 'array',
                'theme_settings.*' => 'nullable',
            ]);

            foreach ($validated['theme_settings'] ?? [] as $key => $value) {
                $setting = ThemeSetting::where('key', $key)->first();

                if ($setting) {
                    // Handle file uploads for image types
                    if ($setting->type === 'image') {
                        if ($request->hasFile("theme_settings.{$key}")) {
                            $file = $request->file("theme_settings.{$key}");

                            // Delete old file if exists
                            if ($setting->value && Storage::disk('public')->exists($setting->value)) {
                                Storage::disk('public')->delete($setting->value);
                            }

                            // Store new file
                            $path = $file->store('theme', 'public');
                            $value = $path;
                        } else {
                            // If no new file uploaded, keep the existing value
                            continue; // Skip updating this setting
                        }
                    }

                    // Handle JSON type
                    if ($setting->type === 'json' && is_array($value)) {
                        $value = json_encode($value);
                    }

                    // Handle empty values - preserve empty strings for text/textarea, convert others to null
                    if ($value === '' && !in_array($setting->type, ['text', 'textarea', 'color'])) {
                        $value = null;
                    }

                    // For color type, ensure it has a default if empty
                    if ($setting->type === 'color' && empty($value)) {
                        $value = '#3B82F6'; // Default blue color
                    }

                    // Update the value (null is acceptable for nullable fields)
                    ThemeSetting::setValue($key, $value);
                }
            }

            ThemeSetting::clearCache();
        }

        // Update system settings
        if ($request->has('system_settings')) {
            $validated = $request->validate([
                'system_settings' => 'array',
                'system_settings.*' => 'nullable',
            ]);

            foreach ($validated['system_settings'] ?? [] as $key => $value) {
                $setting = SystemSetting::where('key', $key)->first();

                if ($setting) {
                    // Special handling for password fields - if value is empty, don't update (keep existing)
                    if ($setting->type === 'password' && empty($value)) {
                        continue; // Skip password fields if empty (preserve existing password)
                    }

                    // Handle JSON type
                    if ($setting->type === 'json' && is_array($value)) {
                        $value = json_encode($value);
                    }

                    // Convert empty strings to null for nullable types
                    if ($value === '' && in_array($setting->type, ['number', 'email', 'url'])) {
                        $value = null;
                    }

                    // For number type, ensure it's a valid number or null
                    if ($setting->type === 'number' && $value !== null && !is_numeric($value)) {
                        continue; // Skip invalid numbers
                    }

                    // For text/textarea fields, preserve empty strings; for other types, use null if empty
                    if ($value === '' && !in_array($setting->type, ['text', 'textarea', 'password'])) {
                        $value = null;
                    }

                    // Update the value (preserve null for nullable fields, empty string for text/textarea)
                    SystemSetting::setValue($key, $value);
                } else {
                    // Setting doesn't exist, create it if it's a valid setting key
                    // This handles cases where a new setting is added to the form but not yet in DB
                    $allowedKeys = [
                        'site_name', 'site_email', 'site_url', 'default_currency', 'timezone',
                        'date_format', 'site_description', 'mail_driver', 'mail_host', 'mail_port',
                        'mail_username', 'mail_password', 'mail_encryption', 'mail_from_address',
                        'mail_from_name', 'youtube_api_key', 'bunny_api_key', 'bunny_library_id',
                        'bunny_cdn_hostname', 'max_video_upload_size', 'allowed_video_formats'
                    ];

                    if (in_array($key, $allowedKeys)) {
                        // Create the setting with default type/group
                        $group = 'general';
                        $type = 'text';

                        if (strpos($key, 'mail_') === 0) {
                            $group = 'email';
                            $type = in_array($key, ['mail_password']) ? 'password' : 'text';
                        } elseif (in_array($key, ['youtube_api_key', 'bunny_api_key'])) {
                            $group = 'video';
                            $type = 'password';
                        } elseif (in_array($key, ['bunny_library_id', 'bunny_cdn_hostname', 'allowed_video_formats'])) {
                            $group = 'video';
                            $type = 'text';
                        } elseif ($key === 'max_video_upload_size') {
                            $group = 'video';
                            $type = 'number';
                        } elseif (in_array($key, ['site_email', 'mail_from_address'])) {
                            $type = 'email';
                        } elseif ($key === 'site_url') {
                            $type = 'url';
                        } elseif ($key === 'site_description') {
                            $type = 'textarea';
                        } elseif (in_array($key, ['mail_port', 'max_video_upload_size'])) {
                            $type = 'number';
                        }

                        SystemSetting::create([
                            'key' => $key,
                            'name' => ucwords(str_replace('_', ' ', $key)),
                            'value' => $value ?? '',
                            'type' => $type,
                            'group' => $group,
                            'is_active' => true,
                            'order' => 0,
                        ]);
                    }
                }
            }

            SystemSetting::clearCache();
        }

        // Update admin notification preferences
        if ($request->has('notification_settings')) {
            $an = AdminNotificationSetting::firstOrCreate(
                ['user_id' => Auth::id()],
                [
                    'email_new_students' => true,
                    'email_new_teachers' => true,
                    'email_new_courses' => true,
                    'email_course_updates' => true,
                    'email_device_bindings' => true,
                    'email_device_reset_requests' => true,
                ]
            );
            $an->email_new_students = $request->boolean('notification_settings.email_new_students');
            $an->email_new_teachers = $request->boolean('notification_settings.email_new_teachers');
            $an->email_new_courses = $request->boolean('notification_settings.email_new_courses');
            $an->email_course_updates = $request->boolean('notification_settings.email_course_updates');
            $an->email_device_bindings = $request->boolean('notification_settings.email_device_bindings');
            $an->email_device_reset_requests = $request->boolean('notification_settings.email_device_reset_requests');
            $an->save();
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings updated successfully.');
    }

    public function updateSetting(Request $request, $key)
    {
        $request->validate([
            'value' => 'required',
        ]);

        $setting = ThemeSetting::where('key', $key)->firstOrFail();

        $value = $request->value;

        // Handle file uploads for image types
        if ($setting->type === 'image' && $request->hasFile('value')) {
            $file = $request->file('value');

            // Delete old file if exists
            if ($setting->value) {
                Storage::disk('public')->delete($setting->value);
            }

            // Store new file
            $path = $file->store('theme', 'public');
            $value = $path;
        }

        // Handle JSON type
        if ($setting->type === 'json' && is_array($value)) {
            $value = json_encode($value);
        }

        ThemeSetting::setValue($key, $value);

        return response()->json([
            'success' => true,
            'message' => 'Setting updated successfully.',
        ]);
    }
}
