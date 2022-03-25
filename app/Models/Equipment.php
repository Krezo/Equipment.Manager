<?php

namespace App\Models;

use App\Rules\EquipmentValidSerialNumberRule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Equipment
 *
 * @mixin Builder
 */
class Equipment extends Model
{
    use HasFactory;

    protected $table = "equipments";
    protected $fillable = [
        'equipment_type_id',
        'serial_number',
        'remark'
    ];

    protected $casts = [
        'equipment_type_id' => 'integer',
    ];

    /**
     * Validate serial number by mask
     *
     * @return bool
     */
    public function isValidMask(string $mask = ""): bool
    {
        $_mask = $mask ? $mask : $this->equipmentType->mask;
        $maskRules = [
            'N' => "\d",
            'A' => "[A-Z]",
            'a' => "[a-z]",
            'X' => "[A-Z0-9]",
            'Z' => "[-_@]"
        ];
        $maskRegexpString = implode("", array_map(function ($maskChar) use ($maskRules) {
            if (array_key_exists($maskChar, $maskRules)) return $maskRules[$maskChar];
        }, str_split($_mask)));
        $maskRegexpString = "/^$maskRegexpString$/";
        return !!preg_match($maskRegexpString, $this->serial_number);
    }


    /**
     * bulkStoreSerialNumbers
     *
     * @param  array $serialNumbers 
     * @param  bool $duplicateSerialNumber Detete duplicates in $serianNumber
     * @param  bool $withErrorBug save ValidationException erros in bag and return it
     * @return Equipment[]
     * @throws ValidationException
     */
    public function bulkStoreSerialNumbers(array $serialNumbers, bool $duplicateSerialNumber = true, bool $withErrorBug = true)
    {
        $validatedSerialNumbers = [];

        $errorBug = [];
        foreach ($duplicateSerialNumber ? array_unique($serialNumbers) : $serialNumbers as $i => $serialNumber) {
            try {
                $validatedSerialNumberData = Validator::make([
                    "serial_number_$i" => $serialNumber,
                    'equipment_type_id' => $this->equipment_type_id,
                ], [
                    "serial_number_$i" => ["bail", "string", "max:20", 'unique:equipments,serial_number', new EquipmentValidSerialNumberRule]
                ])->validate();
                $validatedSerialNumbers[] = $validatedSerialNumberData["serial_number_$i"];
            } catch (ValidationException $th) {
                if ($withErrorBug) {
                    $errorBug = array_merge($errorBug, $th->errors());
                } else throw $th;
            }
        }
        if (count($errorBug) > 0) throw ValidationException::withMessages($errorBug);
        return array_map(function ($serialNumber) {
            return Equipment::create([
                'equipment_type_id' => $this->equipment_type_id,
                'serial_number' => $serialNumber,
                'remark' => $this->remark
            ]);
        }, $validatedSerialNumbers);
    }


    /**
     * equipmentType relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function equipmentType()
    {
        return $this->belongsTo(EquipmentType::class);
    }
}
