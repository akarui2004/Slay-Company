<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Role;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use function Laravel\Prompts\error;
use function Laravel\Prompts\form;
use function Laravel\Prompts\info;

class AddUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a new user to the application with specified role.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        info('Starting user creation process...');

        $userDetail = form()
            ->text('What is your first name?', required: true, name: 'first_name')
            ->text('What is your last name?', required: false, name: 'last_name')
            ->text('What is your email address?',
                required: true,
                name: 'email',
                validate: function ($value) {
                    if (!$value) return null;
                    return User::where('email', $value)->exists()
                            ? 'The email address is already associated with another user. Please use a different email.'
                            : null;
                }
            )
            ->text('What is your mobile number?',
                required: false,
                name: 'mobile',
                validate: function ($value) {
                    if (!$value) return null;

                    return User::where('mobile', $value)->exists()
                            ? 'The mobile number is already associated with another user. Please use a different mobile number.'
                            : null;
                }
            )
            ->text('What is your address?', required: false, name: 'address')
            ->submit();

        $credential = form()
            ->password('Set a password for the user:', required: true, name: 'password')
            ->submit();

        $role = form()
            ->select('Select a role for the user:', [
                'none' => 'None',
                'super.admin' => 'Super Admin',
                'admin' => 'Admin',
            ], default: 'None', name: 'role')
            ->submit();

        DB::beginTransaction();

        try {
            $this->createUser($userDetail, $credential, $role);
            DB::commit();
            info('User created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            error('An error occurred while creating the user: ' . $e->getMessage());
        }
    }

    private function createUser(array $userDetail, array $credential, array $role): void
    {
        $user = User::create([
            'first_name' => $userDetail['first_name'],
            'last_name' => $userDetail['last_name'] ?? null,
            'email' => $userDetail['email'],
            'mobile' => $userDetail['mobile'] ?? null,
            'address' => $userDetail['address'] ?? null,
            'password' => $credential['password'],
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $this->assignRoleToUser($user, $role['role']);
    }

    private function assignRoleToUser(User $user, string $roleCode): void
    {
        if ($roleCode === 'none') return; // No role to assign

        $role = Role::where('code', $roleCode)->first();
        if (!$role) {
            throw new \Exception("Role with code '{$roleCode}' does not exist.");
        }
        $user->roles()->attach($role);
    }
}
