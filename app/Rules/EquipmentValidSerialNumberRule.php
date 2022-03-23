<?php

namespace App\Rules;

use App\Models\Equipment;
use App\Models\EquipmentType;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Rules\Exists;

use function PHPUnit\Framework\returnSelf;

class EquipmentValidSerialNumberRule implements Rule, DataAwareRule
{
    /**
     * All of the data under validation.
     *
     * @var array
     */
    protected $data = [];

    protected string $messageGivenMask = "";
    protected bool $isEqupmentTypeNotExist = false;
    protected string $serialNumber;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
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
        try {
            if (!array_key_exists('equipment_type_id', $this->data)) {
                $this->isEqupmentTypeNotExist = true;
                return false;
            }
            $eqType = EquipmentType::findOrFail($this->data['equipment_type_id']);
            $eq = new Equipment();
            $eq->serial_number = $value;
            $this->messageGivenMask = $eqType->mask;
            return $eq->isValidMask($eqType->mask);
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        if ($this->isEqupmentTypeNotExist) {
            return "equipment_type_id not exist in validation data";
        }
        $message = "Серийный номер '$this->serialNumber' не соответсвует маске '$this->messageGivenMask'";
        return $message;
    }

    /**
     * Set the data under validation.
     *
     * @param  array  $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }
}
