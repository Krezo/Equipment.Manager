<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function equipmentType()
    {
        return $this->belongsTo(EquipmentType::class);
    }
}
