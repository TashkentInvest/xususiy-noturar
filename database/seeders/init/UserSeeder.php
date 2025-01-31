<?php

namespace Database\Seeders\init;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Existing code to create initial users and roles
        $superuser = User::create([
            "name" => "Super Admin",
            "email" => "superadmin@example.com",
            "password" => bcrypt("teamdevs")
        ]);

        $this->createPermissionsAndRoles();

        $superuser->assignRole('Super Admin');
        $permissions = Permission::all();
        $superuser->syncPermissions($permissions);

        // Ensure 'Employee' role exists
        Role::firstOrCreate(['name' => 'Employee', 'guard_name' => 'web']);

        // Define the list of emails and passwords
    }

    /**
     * Create permissions and roles if they do not exist.
     */
    private function createPermissionsAndRoles()
    {
        // Permissions
        $permissions = [
            "permission.show",
            "permission.edit",
            "permission.add",
            "permission.delete",
            "roles.show",
            "roles.edit",
            "roles.add",
            "roles.delete"
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Roles
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Employee', 'guard_name' => 'web']);
        // Add other roles as needed
    }
}
