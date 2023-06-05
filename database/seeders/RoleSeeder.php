<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            [
                'name' => 'admin'
            ],
            [
                'name' => 'editor'
            ],
            [
                'name' => 'user'
            ],
            [
                'name' => 'root'
            ]
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate($role);
        }
    }
}
