<?php

namespace App\Http\Controllers;

use App\Http\Resources\EquipmentResource;
use App\Models\Equipment;
use App\Rules\EquipmentUnqiueSerialNumberRule;
use App\Rules\EquipmentValidSerialNumberRule;
use App\Rules\StringOrArrayRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class EquipmentController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $searchEquipment = Equipment::with('equipmentType');
        $searchValidatedData = $request->validate([
            'serial_number' => ["bail", "string", "max:20"],
        ]);
        if (isset($searchValidatedData['serial_number'])) {
            $searchEquipment->where('serial_number', 'like', "%" . $searchValidatedData['serial_number'] . "%");
        }
        return EquipmentResource::collection(
            $searchEquipment->paginate($request->get('per_page') ?: 30)->withQueryString()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Первичная валидация
        $equipmentInstance = new Equipment($request->validate([
            "equipment_type_id" => ["bail", "required", "integer", "exists:equipment_types,id"],
            "remark" => ["nullable", "string"],
        ]));
        // Определем массовое заполнение
        $serialNumberRequestData = $request->validate([
            "serial_number" => ["bail", "required", new StringOrArrayRule]
        ]);
        if (is_array($serialNumberRequestData["serial_number"])) {
            return response()->json($equipmentInstance->bulkStoreSerialNumbers($serialNumberRequestData["serial_number"]), 201);
        }
        $equipmentInstance->serial_number = $request->validate([
            'serial_number' => ["bail", "string", "max:20", 'unique:equipments', new EquipmentValidSerialNumberRule]
        ])['serial_number']->save();
        return response()->json($equipmentInstance, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return new EquipmentResource(Equipment::with('equipmentType')->findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // $findedEquipment нужен для передачи в правило EquipmentUnqiueSerialNumberRule
        $findedEquipment = Equipment::findOrFail($id);
        $findedEquipment->update($request->validate([
            "equipment_type_id" => ["bail", "integer", "exists:equipment_types,id"],
            "serial_number" => ["bail", "string", "max:20",  Rule::unique('equipments')->ignore($findedEquipment->id), new EquipmentValidSerialNumberRule],
            "remark" => ["nullable", "string"]
        ]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Equipment::findOrFail($id)->delete();
    }
}
