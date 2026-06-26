<?php

namespace Database\Seeders;

use App\Models\PostalCode;
use Illuminate\Database\Seeder;

class PostalCodeSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['81200', 'Centro', 'Colonia', 'Ahome', 'Sinaloa', 'Los Mochis', 'Urbano', '25', '001'],
            ['81200', 'Bienestar', 'Colonia', 'Ahome', 'Sinaloa', 'Los Mochis', 'Urbano', '25', '001'],
            ['81200', 'Scally', 'Colonia', 'Ahome', 'Sinaloa', 'Los Mochis', 'Urbano', '25', '001'],
            ['81000', 'Centro', 'Colonia', 'Guasave', 'Sinaloa', 'Guasave', 'Urbano', '25', '011'],
            ['81000', 'Del Bosque', 'Colonia', 'Guasave', 'Sinaloa', 'Guasave', 'Urbano', '25', '011'],
            ['80000', 'Centro', 'Colonia', 'Culiacan', 'Sinaloa', 'Culiacan Rosales', 'Urbano', '25', '006'],
            ['06000', 'Centro', 'Colonia', 'Cuauhtemoc', 'Ciudad de Mexico', 'Ciudad de Mexico', 'Urbano', '09', '015'],
            ['06600', 'Juarez', 'Colonia', 'Cuauhtemoc', 'Ciudad de Mexico', 'Ciudad de Mexico', 'Urbano', '09', '015'],
            ['64000', 'Centro', 'Colonia', 'Monterrey', 'Nuevo Leon', 'Monterrey', 'Urbano', '19', '039'],
            ['44100', 'Guadalajara Centro', 'Colonia', 'Guadalajara', 'Jalisco', 'Guadalajara', 'Urbano', '14', '039'],
        ];

        foreach ($rows as [$postalCode, $settlement, $type, $municipality, $state, $city, $zone, $stateCode, $municipalityCode]) {
            PostalCode::updateOrCreate(
                [
                    'postal_code' => $postalCode,
                    'settlement' => $settlement,
                    'municipality' => $municipality,
                    'state' => $state,
                ],
                [
                    'settlement_type' => $type,
                    'city' => $city,
                    'zone' => $zone,
                    'state_code' => $stateCode,
                    'municipality_code' => $municipalityCode,
                ]
            );
        }
    }
}
