<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::firstOrCreate(['tag' => 'administrator'], ['name' => 'Administrador', 'tag' => 'administrator']);
        Role::firstOrCreate(['tag' => 'scheduler'], ['name' => 'Responsável pelas Programações', 'tag' => 'scheduler']);
        Role::firstOrCreate(['tag' => 'user'], ['name' => 'Usuário Comum', 'tag' => 'user']);
    }
}
