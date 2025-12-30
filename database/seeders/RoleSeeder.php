<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Admin permissions
            'view admin dashboard',
            'manage users',
            'manage courses',
            'approve courses',
            'manage payments',
            'view reports',
            'manage devices',
            'manage settings',
            
            // Teacher permissions
            'view teacher dashboard',
            'create courses',
            'edit own courses',
            'manage lessons',
            'view student progress',
            'use chatbot',
            
            // Student permissions
            'view student dashboard',
            'enroll courses',
            'watch videos',
            'view progress',
            'use chatbot',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $teacherRole = Role::firstOrCreate(['name' => 'teacher']);
        $studentRole = Role::firstOrCreate(['name' => 'student']);

        // Assign permissions to roles
        $adminRole->givePermissionTo(Permission::all());
        
        $teacherRole->givePermissionTo([
            'view teacher dashboard',
            'create courses',
            'edit own courses',
            'manage lessons',
            'view student progress',
            'use chatbot',
        ]);
        
        $studentRole->givePermissionTo([
            'view student dashboard',
            'enroll courses',
            'watch videos',
            'view progress',
            'use chatbot',
        ]);

        // Create default admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@kitabasan.com'],
            [
                'name' => 'Admin User',
                'mobile' => '03001234567',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        // Create default teacher user
        $teacher = User::firstOrCreate(
            ['email' => 'teacher@kitabasan.com'],
            [
                'name' => 'Teacher User',
                'mobile' => '03001234568',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );
        if (!$teacher->hasRole('teacher')) {
            $teacher->assignRole('teacher');
        }

        // Create default student user
        $student = User::firstOrCreate(
            ['email' => 'student@kitabasan.com'],
            [
                'name' => 'Student User',
                'mobile' => '03001234569',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );
        if (!$student->hasRole('student')) {
            $student->assignRole('student');
        }
    }
}
