<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Schedule;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::whereHas('role', function ($query) {
            $query->where('tag', 'administrator');
        })->first();

        Schedule::firstOrCreate(['user_id' => $user->id], [
            'user_id' => $user->id,
            'name'    => "Programações do MIS",
            'private' => false
        ]);
    }
}
