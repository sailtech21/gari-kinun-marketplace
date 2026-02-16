<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Define available permissions
        $allPermissions = [
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'ads.view',
            'ads.create',
            'ads.edit',
            'ads.delete',
            'ads.approve',
            'reports.view',
            'reports.manage',
            'reports.resolve',
            'payments.view',
            'payments.manage',
            'payments.refund',
            'settings.view',
            'settings.edit',
            'content.view',
            'content.edit',
            'content.delete',
            'analytics.view',
            'analytics.export',
        ];

        // Create default roles
        $roles = [
            [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'description' => 'Full system access with all permissions',
                'permissions' => json_encode($allPermissions),
                'is_active' => true,
                'is_system' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Moderator',
                'slug' => 'moderator',
                'description' => 'Content moderation and user management',
                'permissions' => json_encode([
                    'users.view',
                    'users.edit',
                    'ads.view',
                    'ads.edit',
                    'ads.approve',
                    'reports.view',
                    'reports.manage',
                    'reports.resolve',
                    'content.view',
                    'content.edit',
                    'analytics.view',
                ]),
                'is_active' => true,
                'is_system' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Support',
                'slug' => 'support',
                'description' => 'Customer support and ticket management',
                'permissions' => json_encode([
                    'users.view',
                    'ads.view',
                    'reports.view',
                    'reports.manage',
                    'content.view',
                ]),
                'is_active' => true,
                'is_system' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Finance Admin',
                'slug' => 'finance-admin',
                'description' => 'Financial operations and payment management',
                'permissions' => json_encode([
                    'users.view',
                    'payments.view',
                    'payments.manage',
                    'payments.refund',
                    'analytics.view',
                    'analytics.export',
                ]),
                'is_active' => true,
                'is_system' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($roles as $role) {
            // Only insert if role doesn't exist
            $exists = DB::table('admin_roles')->where('slug', $role['slug'])->exists();
            if (!$exists) {
                DB::table('admin_roles')->insert($role);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Delete system roles
        DB::table('admin_roles')->whereIn('slug', [
            'super-admin',
            'moderator',
            'support',
            'finance-admin'
        ])->delete();
    }
};
