<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

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

        $guard = 'web';
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => $guard]);
        }

        // Create roles (guard_name must match for hasRole/assignRole to work)
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => $guard]);
        $teacherRole = Role::firstOrCreate(['name' => 'teacher', 'guard_name' => $guard]);
        $studentRole = Role::firstOrCreate(['name' => 'student', 'guard_name' => $guard]);

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

        // Create default super admin user (admin@kitabasan.com)
        $admin = User::firstOrCreate(
            ['email' => 'admin@kitabasan.com'],
            [
                'name' => 'Admin User',
                'mobile' => '03001234567',
                'password' => 'password', // cast 'hashed' will hash it
                'status' => 'active',
            ]
        );
        // Always reset password and status so credentials are: admin@kitabasan.com / password
        $admin->update(['password' => 'password', 'status' => 'active', 'name' => 'Admin User']);
        $admin->syncRoles(['admin']);

        // Create default teacher user (teacher@kitabasan.com / password)
        $teacher = User::firstOrCreate(
            ['email' => 'teacher@kitabasan.com'],
            ['name' => 'Teacher User', 'mobile' => '03001234568', 'password' => 'password', 'status' => 'active']
        );
        $teacher->update(['password' => 'password', 'status' => 'active']);
        $teacher->syncRoles(['teacher']);

        // Create default student user (student@kitabasan.com / password)
        $student = User::firstOrCreate(
            ['email' => 'student@kitabasan.com'],
            ['name' => 'Student User', 'mobile' => '03001234569', 'password' => 'password', 'status' => 'active']
        );
        $student->update(['password' => 'password', 'status' => 'active']);
        $student->syncRoles(['student']);

        // Clear permission cache so role checks work immediately
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
