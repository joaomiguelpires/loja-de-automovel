<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Marca;
use App\Models\Categoria;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Seed Marcas
        $marcas = [
            ['nome' => 'Toyota', 'pais_origem' => 'Japão'],
            ['nome' => 'Honda', 'pais_origem' => 'Japão'],
            ['nome' => 'Ford', 'pais_origem' => 'Estados Unidos'],
            ['nome' => 'Chevrolet', 'pais_origem' => 'Estados Unidos'],
            ['nome' => 'Volkswagen', 'pais_origem' => 'Alemanha'],
            ['nome' => 'BMW', 'pais_origem' => 'Alemanha'],
            ['nome' => 'Mercedes-Benz', 'pais_origem' => 'Alemanha'],
            ['nome' => 'Fiat', 'pais_origem' => 'Itália'],
            ['nome' => 'Renault', 'pais_origem' => 'França'],
            ['nome' => 'Hyundai', 'pais_origem' => 'Coreia do Sul'],
        ];

        foreach ($marcas as $marca) {
            Marca::create($marca);
        }

        // Seed Categorias
        $categorias = [
            ['nome' => 'Sedan'],
            ['nome' => 'Hatchback'],
            ['nome' => 'SUV'],
            ['nome' => 'Pickup'],
            ['nome' => 'Coupé'],
            ['nome' => 'Convertible'],
            ['nome' => 'Wagon'],
            ['nome' => 'Minivan'],
        ];

        foreach ($categorias as $categoria) {
            Categoria::create($categoria);
        }
    }
}



