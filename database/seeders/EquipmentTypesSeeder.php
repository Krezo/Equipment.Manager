<?php

namespace Database\Seeders;

use App\Models\EquipmentType;
use Illuminate\Database\Seeder;

class EquipmentTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        EquipmentType::create([
            'name' => 'TP-Link TL-WR74',
            'mask' => 'XXAAAAAXAA'
        ]);
        EquipmentType::create([
            'name' => 'D-Link DIR-300',
            'mask' => 'NXXAAXZXaa'
        ]);
        EquipmentType::create([
            'name' => 'D-Link DIR-300 S',
            'mask' => 'NXXAAXZXXX'
        ]);
    }
}
