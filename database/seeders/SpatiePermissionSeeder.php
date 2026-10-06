<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SpatiePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        /*
        |--------------------------------------------------------------------------
        | Basic permissions
        |--------------------------------------------------------------------------
        */

        $basicPermissions = [
            'view dashboard',
            'view profile',
            'edit profile',
            'change password',
        ];

        foreach ($basicPermissions as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Normal User Role
        |--------------------------------------------------------------------------
        */

        $normalUser = Role::firstOrCreate([
            'name' => 'normal_user',
            'guard_name' => 'web',
        ]);

        $normalUser->syncPermissions($basicPermissions);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}