<?php

namespace App\Console\Commands;

use App\Models\SystemSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class VerifyEmailConfiguration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:verify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify email configuration settings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking Email Configuration...');
        $this->newLine();

        $requiredSettings = [
            'mail_driver' => 'Mail Driver',
            'mail_host' => 'Mail Host',
            'mail_port' => 'Mail Port',
            'mail_username' => 'Mail Username',
            'mail_password' => 'Mail Password',
            'mail_encryption' => 'Mail Encryption',
            'mail_from_address' => 'From Email Address',
            'mail_from_name' => 'From Name',
        ];

        $allConfigured = true;
        $issues = [];

        foreach ($requiredSettings as $key => $name) {
            $value = SystemSetting::getValue($key);

            if (empty($value)) {
                $this->error("✗ {$name} ({$key}): NOT CONFIGURED");
                $allConfigured = false;
                $issues[] = $name;
            } else {
                // Mask password
                $displayValue = $key === 'mail_password' ? str_repeat('*', min(strlen($value), 16)) : $value;
                $this->info("✓ {$name}: {$displayValue}");
            }
        }

        $this->newLine();

        // Check Gmail specific requirements
        $mailHost = SystemSetting::getValue('mail_host');
        $mailUsername = SystemSetting::getValue('mail_username');

        if (stripos($mailHost ?? '', 'gmail.com') !== false) {
            if (stripos($mailUsername ?? '', '@') === false) {
                $this->warn("⚠ Gmail detected: Username should be full email address (e.g., user@gmail.com)");
                $this->warn("  Current username: {$mailUsername}");
                $this->warn("  For Gmail, you must use an App Password, not your regular password.");
                $allConfigured = false;
            }
        }

        // Test configuration application
        $this->newLine();
        $this->info('Testing Configuration Application...');

        try {
            // Apply configuration
            $mailDriver = SystemSetting::getValue('mail_driver', config('mail.default'));
            $mailHost = SystemSetting::getValue('mail_host', config('mail.mailers.smtp.host'));
            $mailPort = SystemSetting::getValue('mail_port', config('mail.mailers.smtp.port'));
            $mailUsername = SystemSetting::getValue('mail_username', config('mail.mailers.smtp.username'));
            $mailPassword = SystemSetting::getValue('mail_password', config('mail.mailers.smtp.password'));
            $mailEncryption = SystemSetting::getValue('mail_encryption', 'tls');

            Config::set('mail.default', $mailDriver);
            Config::set('mail.mailers.smtp.host', $mailHost);
            Config::set('mail.mailers.smtp.port', $mailPort);
            Config::set('mail.mailers.smtp.username', $mailUsername);
            Config::set('mail.mailers.smtp.password', $mailPassword);
            Config::set('mail.mailers.smtp.encryption', $mailEncryption);

            $this->info('✓ Configuration can be applied successfully');
        } catch (\Exception $e) {
            $this->error('✗ Configuration application failed: ' . $e->getMessage());
            $allConfigured = false;
        }

        $this->newLine();

        if ($allConfigured && empty($issues)) {
            $this->info('✓ All email settings are configured correctly!');
            $this->info('  You can now send emails from the admin panel.');
            return 0;
        } else {
            $this->error('✗ Some email settings are missing or incorrect.');
            $this->warn('  Please configure the following in Admin Settings → Email Settings:');
            foreach ($issues as $issue) {
                $this->warn("    - {$issue}");
            }
            return 1;
        }
    }
}
