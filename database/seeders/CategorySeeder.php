<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::firstOrCreate(['name' => 'Programas Fixos'], ['name' => 'Programas Fixos', 'color' => '#b6d7a8']);
        Category::firstOrCreate(['name' => 'Demanda Externa'], ['name' => 'Demanda Externa', 'color' => '#8e7cc3']);
        Category::firstOrCreate(['name' => 'Palestras / Aulas'], ['name' => 'Palestras / Aulas', 'color' => '#ffe599']);
        Category::firstOrCreate(['name' => 'Mostra Audiovisual'], ['name' => 'Mostra Audiovisual', 'color' => '#ea9999']);
        Category::firstOrCreate(['name' => 'Feiras'], ['name' => 'Feiras', 'color' => '#bf9000']);
        Category::firstOrCreate(['name' => 'Música'], ['name' => 'Música', 'color' => '#00ffff']);
        Category::firstOrCreate(['name' => 'Multiplas Linguagens'], ['name' => 'Multiplas Linguagens', 'color' => '#38761d']);
        Category::firstOrCreate(['name' => 'Artes Visuais'], ['name' => 'Artes Visuais', 'color' => '#0b5394']);
        Category::firstOrCreate(['name' => 'Cursos'], ['name' => 'Cursos', 'color' => '#c27ba0']);
        Category::firstOrCreate(['name' => 'Projeções'], ['name' => 'Projeções', 'color' => '#ff9900']);
        Category::firstOrCreate(['name' => 'Programação Interna'], ['name' => 'Programação Interna', 'color' => '#cfe2f3']);
        Category::firstOrCreate(['name' => 'Formações'], ['name' => 'Formações', 'color' => '#cc4125']);
        Category::firstOrCreate(['name' => 'Evento não Confirmado'], ['name' => 'Evento não Confirmado']);
    }
}
