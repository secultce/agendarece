<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Configuration;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role  = Role::whereTag('administrator')->first();
        $email = 'admin@schedulemanager.com.br';
        $user  = User::firstOrCreate(['email' => $email], [
            'role_id'  => $role->id,
            'name'     => $role->name,
            'email'    => $email,
            'password' => Hash::make('BNBnPm8IPD')
        ]);

        Configuration::firstOrCreate(['user_id' => $user->id], ['user_id' => $user->id]);
    }
}
