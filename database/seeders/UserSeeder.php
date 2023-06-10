<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder {
	/**
	 * Seed the application's database.
	 *
	 * @return void
	 */
	public function run() {

        $users = [
            [
                'email' => 'admin@sendemail.techupcorp',
                'password' => '123@123',
                'role' => 'admin',

            ],
            [
                'email' => 'user1@sendemail.techupcorp',
                'password' => '111@222',
                'role' => 'user',
            ],
            [
                'email' => 'root@sendemail.techupcorp',
                'password' => '111@222',
                'role' => 'root',
            ],
            [
                'email' => 'ronn@techupzone.com',
                'password' => '111@222',
                'role' => 'root',
            ]
        ];
        $role = Role::where('name', 'user')->first();
        $roleadmin = Role::where('name', 'admin')->first();
        $roleRoot = Role::where('name', 'root')->first();
        if ($role && $roleadmin && $roleRoot) {
            foreach ($users as $item) {
                if($item['role'] == 'admin'){
                    $current_role = $roleadmin;
                }elseif ($item['role'] == 'root'){
                    $current_role = $roleRoot;
                }else{
                    $current_role = $role;
                }

                if ($user = User::where('email', $item['email'])->first()) {
                    $user->roles()->sync([$current_role->uuid]);
                    continue;
                }
                $user = User::factory()->create([
                    'email' => $item['email'],
                    'password' => Hash::make($item['password']),
                    'can_add_smtp_account' => true
                ]);
                $user->roles()->sync([$current_role->uuid]);
            }
//            User::factory(10)->create()->each(function ($user) use ($role) {
//                $user->roles()->sync([$role->uuid]);
//            });

        }

	}
}
