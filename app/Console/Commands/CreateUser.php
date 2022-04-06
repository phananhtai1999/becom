<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new User';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $userName = $this->ask(__('What is your user name?'));
        $firstName = $this->ask(__('What is your first name?'));
        $lastName = $this->ask(__('What is your last name?'));
        $email = $this->ask(__('What is your email?'));
        $password = $this->secret(__('What is your password?'));

        $roleOptions = [];
        $roles = Role::all();
        foreach ($roles as $role) {
            $roleOptions[] = $role->slug;
        }
        $roleSlugs = $this->choice(__('What is your role?'), $roleOptions, null, null, true);

        $hasErrors = false;

        if (empty($email)) {
            $this->error(__('The email is required!'));
            $hasErrors = true;
        } else {
            if (User::where('email', $email)->first()) {
                $this->error(__('The email was be taken!'));
                $hasErrors = true;
            }
        }

        if (empty($password)) {
            $this->error(__('The password is required!'));
            $hasErrors = true;
        }

        if (!$hasErrors) {
            try {
                $model = User::create([
                    'username' => $userName,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'password' => Hash::make($password),
                ]);

                $roleIds = [];
                foreach ($roleSlugs as $roleSlug) {
                    $roleIds[] = Role::where('slug', $roleSlug)->first()->getKey();
                }

                $model->roles()->sync(
                    array_merge($roleIds, [config('user.default_role_uuid')])
                );

                $this->info(__('Create a new user successfully'));
            } catch (QueryException $exception) {
                $this->error(__('Create user failed!: Database Query Error'));
            } catch (\Exception $exception) {
                $this->error(__('Create user failed!: ') . $exception->getMessage());
            }
        }
    }
}
