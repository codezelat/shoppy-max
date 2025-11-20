<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
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
            'view users',
            'create users',
            'edit users',
            'delete users',
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            'view permissions',
            'assign permissions',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles
        $superAdminRole = Role::create(['name' => 'super admin']);
        $adminRole = Role::create(['name' => 'admin']);
        $userRole = Role::create(['name' => 'user']);

        // Assign all permissions to super admin
        $superAdminRole->givePermissionTo(Permission::all());

        // Assign some permissions to admin
        $adminRole->givePermissionTo([
            'view users',
            'create users',
            'edit users',
            'view roles',
            'view permissions',
        ]);

        // Create super admin user
        // NOTE: For security, change this password immediately after first login in production
        // You can use: php artisan tinker
        // Then: User::where('email', 'admin@shoppy-max.com')->first()->update(['password' => Hash::make('your-secure-password')])
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@shoppy-max.com',
            'password' => Hash::make('password'), // CHANGE THIS IN PRODUCTION!
        ]);

        $superAdmin->assignRole('super admin');
        
        // Display warning in console
        $this->command->warn('⚠️  WARNING: Default super admin created with password "password"');
        $this->command->warn('   Please change this password immediately in production!');
        $this->command->info('   Login at: /login with email: admin@shoppy-max.com');
    }
}
