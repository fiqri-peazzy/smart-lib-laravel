<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ========================================
        // PERMISSIONS
        // ========================================

        $permissions = [
            // Books Management
            'view_books',
            'create_books',
            'edit_books',
            'delete_books',

            // Digital Collections
            'view_digital_collections',
            'upload_digital_collections',
            'download_digital_collections',
            'delete_digital_collections',

            // Loans Management
            'view_loans',
            'create_loans',
            'process_loans',
            'return_loans',
            'extend_loans',
            'view_own_loans',

            // Bookings
            'create_bookings',
            'view_bookings',
            'cancel_bookings',

            // Members Management
            'view_members',
            'create_members',
            'edit_members',
            'delete_members',
            'suspend_members',

            // Fines Management
            'view_fines',
            'process_fines',
            'waive_fines',

            // Analytics & Reports
            'view_analytics',
            'export_reports',

            // System Settings
            'manage_settings',
            'manage_roles',

            // Notifications
            'send_notifications',
            'view_notifications',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // ========================================
        // ROLES & ASSIGN PERMISSIONS
        // ========================================

        // 1. ADMIN - Full access
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        // 2. STAFF PUSTAKAWAN - Operational access
        $staffRole = Role::create(['name' => 'staff']);
        $staffRole->givePermissionTo([
            'view_books',
            'create_books',
            'edit_books',
            'view_digital_collections',
            'upload_digital_collections',
            'view_loans',
            'create_loans',
            'process_loans',
            'return_loans',
            'extend_loans',
            'view_bookings',
            'view_members',
            'create_members',
            'edit_members',
            'view_fines',
            'process_fines',
            'view_analytics',
            'send_notifications',
        ]);

        // 3. DOSEN - Extended user privileges
        $dosenRole = Role::create(['name' => 'dosen']);
        $dosenRole->givePermissionTo([
            'view_books',
            'view_digital_collections',
            'download_digital_collections',
            'view_own_loans',
            'create_loans', // Self-service (optional)
            'extend_loans',
            'create_bookings',
            'view_bookings',
            'cancel_bookings',
            'view_notifications',
        ]);

        // 4. MAHASISWA - Basic user privileges
        $mahasiswaRole = Role::create(['name' => 'mahasiswa']);
        $mahasiswaRole->givePermissionTo([
            'view_books',
            'view_digital_collections',
            'download_digital_collections',
            'view_own_loans',
            'create_loans', // Self-service (optional)
            'extend_loans',
            'create_bookings',
            'view_bookings',
            'cancel_bookings',
            'view_notifications',
        ]);
    }
}
