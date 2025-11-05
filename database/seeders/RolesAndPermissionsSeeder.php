<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Puhasta permission cache
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Õigused
        $permissions = [
            'posts.create',
            'posts.update.own',
            'posts.update.any',
            'posts.delete.own',
            'posts.delete.any',
            'posts.publish',
            'comments.create',
            'comments.moderate',
            'users.manage',
            'settings.manage',
            'categories.manage',
            'tags.manage',
        ];

        foreach($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Rollid: Admin, Moderator, Author
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $moderator = Role::firstOrCreate(['name' => 'Moderator']);
        $author = Role::firstOrCreate(['name' => 'Author']);

        // Õigused rollidele
        $admin->syncPermission(Permission::all());

        $moderator->syncPermission([
            'posts.create',
            'posts.update.own',
            'posts.update.any',
            'posts.delete.own',
            'posts.delete.any',
            'posts.publish',
            'comments.create',
            'comments.moderate',
            'categories.manage',
            'tags.manage',
        ]);

        $author->syncPermission([
            'posts.create',
            'posts.update.own',
            'posts.delete.own',
            'comments.create',
        ]);

        // Näidis kontod Admin, Moderator ja Author
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin', 'password' => Hash::make('password')]
        );
        $adminUser->assignRole($admin); // Lisab õigused

        $modUser = User::firstOrCreate(
            ['email' => 'moderator@example.com'],
            ['name' => 'Moderator', 'password' => Hash::make('password')]
        );
        $modUser->assignRole($moderator); // Lisab õigused

        $authorUser = User::firstOrCreate(
            ['email' => 'author@example.com'],
            ['name' => 'Author', 'password' => Hash::make('password')]
        );
        $authorUser->assignRole($author); // Lisab õigused

        // Uuenda cache
        app()[PermissionRegistrar::class]->forgetCachedPermission();


    }

}
