<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Space;

class SpaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Space::firstOrCreate(['name' => 'Casarão'], ['name' => 'Casarão']);
        Space::firstOrCreate(['name' => 'Fachada do Anexo'], ['name' => 'Fachada do Anexo']);
        Space::firstOrCreate(['name' => 'Painel de LED'], ['name' => 'Painel de LED']);
        Space::firstOrCreate(['name' => 'Praça do MIS'], ['name' => 'Praça do MIS']);
        Space::firstOrCreate(['name' => 'Varanda do Casarão'], ['name' => 'Varanda do Casarão']);
        Space::firstOrCreate(['name' => 'Auditório'], ['name' => 'Auditório']);
        Space::firstOrCreate(['name' => 'Vídeo Wall Recepção do Anexo'], ['name' => 'Vídeo Wall Recepção do Anexo']);
        Space::firstOrCreate(['name' => 'Vídeo Wall Elevadores'], ['name' => 'Vídeo Wall Elevadores']);
        Space::firstOrCreate(['name' => 'Sala Multiuso (-1)'], ['name' => 'Sala Multiuso (-1)']);
        Space::firstOrCreate(['name' => 'Sala Multiuso (-2)'], ['name' => 'Sala Multiuso (-2)']);
        Space::firstOrCreate(['name' => 'Anexo (+2)'], ['name' => 'Anexo (+2)']);
        Space::firstOrCreate(['name' => 'Estúdio de Foto e Vídeo'], ['name' => 'Estúdio de Foto e Vídeo']);
        Space::firstOrCreate(['name' => 'Área Externa da Biblioteca'], ['name' => 'Área Externa da Biblioteca']);
    }
}
