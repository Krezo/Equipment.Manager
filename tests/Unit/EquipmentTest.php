<?php

namespace Tests\Unit;

use App\Models\Equipment;
use Illuminate\Support\Str;
use App\Models\EquipmentType;
use Database\Seeders\EquipmentTypesSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase as TestsTestCase;
use Faker\Generator as Faker;

class EquipmentsTest extends TestsTestCase
{
    use DatabaseMigrations;

    public function testIsValidMask()
    {
        $this->seed(EquipmentTypesSeeder::class);

        $testMask1 = "/XXAAANaXZXaNN/";
        $testMask2 = "/XAAAXaANZAaXNA/";
        $testMask3 = "/aXAAXNaAXNXANNZAN/";

        $validTest1Eqiupment = new Equipment([
            'serial_number' => 'A0ZBC9a0@0b56'
        ]);
        $validTest2Eqiupment = new Equipment([
            'serial_number' => 'AABC0aD1_Gb00A'
        ]);
        $validTest3Eqiupment = new Equipment([
            'serial_number' => 'hXAB06fD00FA54-T8'
        ]);

        $this->assertTrue($validTest1Eqiupment->isValidMask($testMask1));
        $this->assertFalse($validTest1Eqiupment->isValidMask($testMask2));
        $this->assertFalse($validTest1Eqiupment->isValidMask($testMask3));

        $this->assertFalse($validTest2Eqiupment->isValidMask($testMask1));
        $this->assertTrue($validTest2Eqiupment->isValidMask($testMask2));
        $this->assertFalse($validTest2Eqiupment->isValidMask($testMask3));

        $this->assertFalse($validTest3Eqiupment->isValidMask($testMask1));
        $this->assertFalse($validTest3Eqiupment->isValidMask($testMask2));
        $this->assertTrue($validTest3Eqiupment->isValidMask($testMask3));

        // Test with existing equipment type
        $createdEqType = new EquipmentType([
            'name' => 'TEST_EQ',
            'mask' => 'NAXZaaAZXN'
        ]);
        $createdEqType->save();
        $ValidEq = new Equipment([
            'equipment_type_id' => $createdEqType->id,
            'serial_number' => '0A0@aaF@Z9',
            'remark' => 'TEST REMARK'
        ]);
        $InvalidEq = new Equipment([
            'equipment_type_id' => $createdEqType->id,
            'serial_number' => '0A0@AaF@Z9',
            'remark' => 'TEST REMARK'
        ]);
        $this->assertTrue($ValidEq->isValidMask());
        $this->assertFalse($InvalidEq->isValidMask());
    }
    public function testEquipmentStore()
    {
        // Добавялем тип оборудования с масками
        $equipmentType1 = EquipmentType::create([
            'id' => 1,
            'name' => 'test_mask_1',
            'mask' => "/XXAAANaXZXaNN/"
        ]);
        $equipmentType2 = EquipmentType::create([
            'id' => 2,
            'name' => 'test_mask_2',
            'mask' => "/XAAAXaANZAaXNA/"
        ]);
        $equipmentType3 = EquipmentType::create([
            'id' => 3,
            'name' => 'test_mask_3',
            'mask' => "/aXAAXNaAXNXANNZAN/"
        ]);

        // Simple tests
        $validTest1EqiupmentData = [
            // 'equipment_type_id' => $equipmentType1->id,
            'serial_number' => 'A0ZBC9a0@0b56',
            'remark' => null
        ];
        $validTest2EqiupmentData = [
            // 'equipment_type_id' => $equipmentType2->id,
            'serial_number' => 'AABC0aD1_Gb00A',
            'remark' => 'test_remark_2'
        ];
        $validTest3EqiupmentData = [
            // 'equipment_type_id' => $equipmentType3->id,
            'serial_number' => 'hXAB06fD00FA54-T8',
            'remark' => 'test_remark_3'
        ];
        $validTest1ArraySNEqiupmentData = [
            'equipment_type_id' => $equipmentType1->id,
            'serial_number' => [
                'T1ZBC9a0@0b56',
                'A9ZGF9a0_0c11',
                'A6ZHT1a0-0r72'
            ],
            'remark' => null
        ];

        $validTest1ArraySNDublicateEqiupmentData = [
            'equipment_type_id' => $equipmentType1->id,
            'serial_number' => [
                'T1ZBT1a7@0b56',
                'T1ZBT1a7@0b56',
                'G6ZFH6y0-0r72'
            ],
            'remark' => null
        ];




        // Проверка на обязательные поля
        $this->postJson('/api/equipment', [])->assertStatus(422);
        $this->postJson('/api/equipment', [
            'serial_number' => '1'
        ])->assertStatus(422);
        $this->postJson('/api/equipment', [
            'equipment_type_id' => '1'
        ])->assertStatus(422);
        $this->postJson('/api/equipment', [
            'serial_number' => 'A0ZTC7g0@0b56',
            'equipment_type_id' => 1
        ])->assertStatus(201);

        $this->postJson('/api/equipment', array_merge([
            'equipment_type_id' => $equipmentType1->id
        ], $validTest1EqiupmentData))
            ->assertStatus(201);
        $this->postJson('/api/equipment', array_merge([
            'equipment_type_id' => $equipmentType1->id
        ], $validTest2EqiupmentData))
            ->assertStatus(422);
        $this->postJson('/api/equipment', array_merge([
            'equipment_type_id' => $equipmentType1->id
        ], $validTest3EqiupmentData))
            ->assertStatus(422);

        $this->postJson('/api/equipment', array_merge([
            'equipment_type_id' => $equipmentType2->id
        ], $validTest1EqiupmentData))
            ->assertStatus(422);
        $this->postJson('/api/equipment', array_merge([
            'equipment_type_id' => $equipmentType2->id
        ], $validTest2EqiupmentData))
            ->assertStatus(201);
        $this->postJson('/api/equipment', array_merge([
            'equipment_type_id' => $equipmentType2->id
        ], $validTest3EqiupmentData))
            ->assertStatus(422);

        $this->postJson('/api/equipment', array_merge([
            'equipment_type_id' => $equipmentType3->id
        ], $validTest1EqiupmentData))
            ->assertStatus(422);
        $this->postJson('/api/equipment', array_merge([
            'equipment_type_id' => $equipmentType3->id
        ], $validTest2EqiupmentData))
            ->assertStatus(422);
        $this->postJson('/api/equipment', array_merge([
            'equipment_type_id' => $equipmentType3->id
        ], $validTest3EqiupmentData))
            ->assertStatus(201);

        $this->postJson('/api/equipment', $validTest1ArraySNEqiupmentData)
            ->assertStatus(201);
        $this->postJson('/api/equipment', $validTest1ArraySNDublicateEqiupmentData)
            ->assertStatus(201);
    }
}
