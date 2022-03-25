<?php

namespace App\Http\Controllers;

use App\Http\Resources\EquipmentTypeResource;
use App\Models\EquipmentType;
use Illuminate\Http\Request;

class EquipmentTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return EquipmentTypeResource::collection(EquipmentType::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return new EquipmentTypeResource(EquipmentType::create($request->validate([
            'name' => ['required', 'string'],
            'mask' => ['required', 'string', 'max:20'],
        ])));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return new EquipmentType(EquipmentType::findOrFail($id));
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
        return new EquipmentTypeResource(EquipmentType::findOrFail($id)->update(
            $request->validate([
                'name' => ['required', 'string'],
                'mask' => ['required', 'string', 'max:20'],
            ])
        ));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        EquipmentType::findOrFail($id)->delete();
    }
}
