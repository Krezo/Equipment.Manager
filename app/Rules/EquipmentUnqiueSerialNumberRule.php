<?php

namespace App\Rules;

use App\Models\Equipment;
use Illuminate\Contracts\Validation\Rule;

class EquipmentUnqiueSerialNumberRule implements Rule
{
    protected string $serialNumber;
    protected int $ignoreId;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->serialNumber = $value;
        $findedEquipmet = Equipment::where('serial_number', $value)->first();
        if ($findedEquipmet) {
            if (isset($this->ignoreId)) {
                if ($findedEquipmet->id === $this->ignoreId) return true;
            }
            return false;
        }
        return true;
    }

    /**
     * Установка ид для исключения савпадения модели с собой
     *
     * @param  mixed $ignoreId
     * @return void
     */
    public function setIngnoreId(int $ignoreId)
    {
        $this->ignoreId = $ignoreId;
        return $this;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "Серийный номер $this->serialNumber уже существует в базе";
    }
}
