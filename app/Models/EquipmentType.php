<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EquipmentType
 *
 * @property int $id
 * @property string $name
 * @property string $mask
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\EquipmentTypeFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|EquipmentType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EquipmentType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EquipmentType query()
 * @method static \Illuminate\Database\Eloquent\Builder|EquipmentType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EquipmentType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EquipmentType whereMask($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EquipmentType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EquipmentType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class EquipmentType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'mask',
    ];
}
