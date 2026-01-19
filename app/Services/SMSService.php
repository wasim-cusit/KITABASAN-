<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SMSService
{
    /**
     * Send SMS to a mobile number
     * 
     * @param string $mobile Mobile number (with country code, e.g., +923001234567)
     * @param string $message SMS message (max 160 characters)
     * @return bool Success status
     */
    public function send(string $mobile, string $message): bool
    {
        try {
            // Clean mobile number (remove spaces, dashes, etc.)
            $mobile = preg_replace('/[^0-9+]/', '', $mobile);
            
            // Ensure mobile number starts with country code
            if (!str_starts_with($mobile, '+')) {
                // If it starts with 0, replace with country code (Pakistan: +92)
                if (str_starts_with($mobile, '0')) {
                    $mobile = '+92' . substr($mobile, 1);
                } else {
                    // Assume it's already without country code, add +92 for Pakistan
                    $mobile = '+92' . $mobile;
                }
            }

            // Validate mobile number
            if (strlen($mobile) < 10) {
                Log::error('Invalid mobile number: ' . $mobile);
                return false;
            }

            // Truncate message to 160 characters
            $message = substr($message, 0, 160);

            // TODO: Implement actual SMS gateway integration
            // Examples: Twilio, Nexmo/Vonage, SMS Gateway API, etc.
            
            // For now, we'll log the SMS (you can integrate with actual SMS gateway)
            Log::info('SMS sent', [
                'mobile' => $mobile,
                'message' => $message,
                'length' => strlen($message),
            ]);

            // Example: Twilio integration (uncomment and configure)
            /*
            $accountSid = config('services.twilio.account_sid');
            $authToken = config('services.twilio.auth_token');
            $fromNumber = config('services.twilio.from_number');

            $response = Http::withBasicAuth($accountSid, $authToken)
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$accountSid}/Messages.json", [
                    'From' => $fromNumber,
                    'To' => $mobile,
                    'Body' => $message,
                ]);

            if ($response->successful()) {
                return true;
            } else {
                Log::error('SMS gateway error', ['response' => $response->body()]);
                return false;
            }
            */

            // Example: Generic SMS Gateway API (uncomment and configure)
            /*
            $apiKey = config('services.sms.api_key');
            $apiUrl = config('services.sms.api_url');

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
            ])->post($apiUrl, [
                'to' => $mobile,
                'message' => $message,
            ]);

            if ($response->successful()) {
                return true;
            } else {
                Log::error('SMS gateway error', ['response' => $response->body()]);
                return false;
            }
            */

            // For development/testing: return true (SMS logged but not actually sent)
            return true;

        } catch (\Exception $e) {
            Log::error('SMS service error: ' . $e->getMessage(), [
                'mobile' => $mobile,
                'message' => $message,
            ]);
            return false;
        }
    }

    /**
     * Send bulk SMS to multiple numbers
     * 
     * @param array $mobiles Array of mobile numbers
     * @param string $message SMS message
     * @return array ['sent' => count, 'failed' => count]
     */
    public function sendBulk(array $mobiles, string $message): array
    {
        $sent = 0;
        $failed = 0;

        foreach ($mobiles as $mobile) {
            if ($this->send($mobile, $message)) {
                $sent++;
            } else {
                $failed++;
            }
        }

        return [
            'sent' => $sent,
            'failed' => $failed,
        ];
    }
}
