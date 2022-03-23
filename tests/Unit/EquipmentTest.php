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

        // Simple tests
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
}
