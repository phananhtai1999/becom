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
		];
		$role = Role::where('name', 'user')->first();
		$roleadmin = Role::where('name', 'admin')->first();
		if ($role && $roleadmin) {
			foreach ($users as $user) {
				if (User::where('email', $user['email'])->count()) {
					continue;
				}
				$user = User::factory()->create([
					'email' => $user['email'],
					'password' => Hash::make($user['password']),
				]);
				$current_role = $user['role'] === 'admin' ? $roleadmin : $role;
				$user->roles()->sync([$current_role->uuid]);
			}
			User::factory(10)->create()->each(function ($user) use ($role) {
				$user->roles()->sync([$role->uuid]);

			});

		}

	}
}
