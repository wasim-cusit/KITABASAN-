<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insert default system settings
        $defaultSettings = [
            // General Settings
            ['key' => 'site_name', 'name' => 'Site Name', 'value' => config('app.name'), 'type' => 'text', 'group' => 'general', 'order' => 1],
            ['key' => 'site_email', 'name' => 'Site Email', 'value' => config('mail.from.address'), 'type' => 'email', 'group' => 'general', 'order' => 2],
            ['key' => 'site_url', 'name' => 'Site URL', 'value' => config('app.url'), 'type' => 'url', 'group' => 'general', 'order' => 3],
            ['key' => 'default_currency', 'name' => 'Default Currency', 'value' => 'PKR', 'type' => 'text', 'group' => 'general', 'order' => 4],
            ['key' => 'timezone', 'name' => 'Timezone', 'value' => 'Asia/Karachi', 'type' => 'text', 'group' => 'general', 'order' => 5],
            ['key' => 'date_format', 'name' => 'Date Format', 'value' => 'Y-m-d', 'type' => 'text', 'group' => 'general', 'order' => 6],
            ['key' => 'site_description', 'name' => 'Site Description', 'value' => '', 'type' => 'textarea', 'group' => 'general', 'order' => 7],
            
            // Email Settings
            ['key' => 'mail_driver', 'name' => 'Mail Driver', 'value' => 'smtp', 'type' => 'text', 'group' => 'email', 'order' => 1],
            ['key' => 'mail_host', 'name' => 'Mail Host', 'value' => config('mail.mailers.smtp.host'), 'type' => 'text', 'group' => 'email', 'order' => 2],
            ['key' => 'mail_port', 'name' => 'Mail Port', 'value' => (string)config('mail.mailers.smtp.port'), 'type' => 'number', 'group' => 'email', 'order' => 3],
            ['key' => 'mail_username', 'name' => 'Mail Username', 'value' => config('mail.mailers.smtp.username'), 'type' => 'text', 'group' => 'email', 'order' => 4],
            ['key' => 'mail_password', 'name' => 'Mail Password', 'value' => config('mail.mailers.smtp.password'), 'type' => 'password', 'group' => 'email', 'order' => 5],
            ['key' => 'mail_encryption', 'name' => 'Mail Encryption', 'value' => config('mail.mailers.smtp.encryption'), 'type' => 'text', 'group' => 'email', 'order' => 6],
            ['key' => 'mail_from_address', 'name' => 'From Email Address', 'value' => config('mail.from.address'), 'type' => 'email', 'group' => 'email', 'order' => 7],
            ['key' => 'mail_from_name', 'name' => 'From Name', 'value' => config('mail.from.name'), 'type' => 'text', 'group' => 'email', 'order' => 8],
            
            // Video Settings
            ['key' => 'youtube_api_key', 'name' => 'YouTube API Key', 'value' => config('services.youtube.api_key'), 'type' => 'password', 'group' => 'video', 'order' => 1],
            ['key' => 'bunny_api_key', 'name' => 'Bunny Stream API Key', 'value' => config('services.bunny.api_key'), 'type' => 'password', 'group' => 'video', 'order' => 2],
            ['key' => 'bunny_library_id', 'name' => 'Bunny Stream Library ID', 'value' => config('services.bunny.library_id'), 'type' => 'text', 'group' => 'video', 'order' => 3],
            ['key' => 'bunny_cdn_hostname', 'name' => 'Bunny CDN Hostname', 'value' => config('services.bunny.cdn_hostname'), 'type' => 'text', 'group' => 'video', 'order' => 4],
            ['key' => 'max_video_upload_size', 'name' => 'Max Video Upload Size (MB)', 'value' => '100', 'type' => 'number', 'group' => 'video', 'order' => 5],
            ['key' => 'allowed_video_formats', 'name' => 'Allowed Video Formats', 'value' => 'mp4,avi,mov,wmv,flv', 'type' => 'text', 'group' => 'video', 'order' => 6],
        ];

        foreach ($defaultSettings as $setting) {
            DB::table('system_settings')->insert(array_merge($setting, [
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('system_settings')->whereIn('key', [
            'site_name', 'site_email', 'site_url', 'default_currency', 'timezone', 'date_format', 'site_description',
            'mail_driver', 'mail_host', 'mail_port', 'mail_username', 'mail_password', 'mail_encryption', 'mail_from_address', 'mail_from_name',
            'youtube_api_key', 'bunny_api_key', 'bunny_library_id', 'bunny_cdn_hostname', 'max_video_upload_size', 'allowed_video_formats'
        ])->delete();
    }
};
