<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class SuperAdminReset extends Command
{
    protected $signature = 'superadmin:reset';
    protected $description = 'Reset super admin password and role (admin@kitabasan.com / password)';

    public function handle(): int
    {
        $user = User::where('email', 'admin@kitabasan.com')->first();

        if (!$user) {
            $this->error('Super admin user not found. Run: php artisan db:seed --class=RoleSeeder');
            return 1;
        }

        $user->update(['password' => 'password', 'status' => 'active', 'name' => 'Admin User']);
        $user->syncRoles(['admin']);

        $this->info('Super admin reset successfully.');
        $this->line('');
        $this->line('  <comment>Email:</comment>    admin@kitabasan.com');
        $this->line('  <comment>Password:</comment> password');
        $this->line('');

        return 0;
    }
}
