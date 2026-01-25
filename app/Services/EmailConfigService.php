<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class EmailConfigService
{
    /**
     * Apply email configuration from system settings. Call before sending mail
     * when using dynamic admin-configured SMTP (e.g. forgot password, account created, enrollments).
     */
    public static function apply(): void
    {
        try {
            SystemSetting::clearCache();
            if (app()->bound('mail.manager')) {
                app()['mail.manager']->forgetMailers();
            }

            $mailDriver = self::get('mail_driver', config('mail.default', 'log'));
            $mailHost = self::get('mail_host', config('mail.mailers.smtp.host'));
            $mailPort = (int) self::get('mail_port', config('mail.mailers.smtp.port', 2525));
            $mailUsername = self::get('mail_username', config('mail.mailers.smtp.username'));
            $mailPassword = self::get('mail_password', config('mail.mailers.smtp.password'));
            $mailEncryption = self::get('mail_encryption', 'tls');
            $mailFromAddress = self::get('mail_from_address', SystemSetting::getValue('site_email') ?: config('mail.from.address'));
            $mailFromName = self::get('mail_from_name', SystemSetting::getValue('site_name') ?: config('mail.from.name'));

            Config::set('mail.default', $mailDriver ?: 'log');
            Config::set('mail.mailers.smtp.host', $mailHost ?: '127.0.0.1');
            Config::set('mail.mailers.smtp.port', $mailPort ?: 2525);
            Config::set('mail.mailers.smtp.username', $mailUsername);
            Config::set('mail.mailers.smtp.password', $mailPassword);
            Config::set('mail.mailers.smtp.encryption', $mailEncryption ?: 'tls');
            Config::set('mail.from.address', $mailFromAddress ?: config('mail.from.address', 'noreply@example.com'));
            Config::set('mail.from.name', $mailFromName ?: config('mail.from.name', 'KITAB ASAN'));

            Log::info('EmailConfigService: config applied', [
                'driver' => $mailDriver,
                'host' => $mailHost,
                'port' => $mailPort,
            ]);
        } catch (\Throwable $e) {
            Log::error('EmailConfigService: failed to apply config', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Get raw value from SystemSetting by key (bypass cache for live admin changes).
     */
    protected static function get(string $key, $default = null)
    {
        $s = SystemSetting::where('key', $key)->where('is_active', true)->first();
        return $s ? $s->value : $default;
    }

    /**
     * Whether mail is configured enough to send (driver and host for smtp).
     */
    public static function isConfigured(): bool
    {
        $driver = config('mail.default', 'log');
        if ($driver === 'log') {
            return false;
        }
        if ($driver === 'smtp' && empty(config('mail.mailers.smtp.host'))) {
            return false;
        }
        return true;
    }
}
