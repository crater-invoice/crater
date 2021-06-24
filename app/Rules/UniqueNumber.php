<?php

namespace Crater\Rules;

use Illuminate\Contracts\Validation\Rule;

class UniqueNumber implements Rule
{
    public $id;

    public $class;

    public $type;

    /**
     * Create a new rule instance.
     * @param  string  $class
     * @param  int  $id
     * @return void
     */
    public function __construct(string $class = null, int $id = null)
    {
        $this->class = $class;
        $this->id = $id;
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
        if ($value && count(explode("-", $value)) > 2) {
            $number = explode("-", $value);
            $uniqueNumber = $number[0].'-'.sprintf('%06d', intval($number[1]));
        } else {
            $uniqueNumber = $value;
        }

        $this->type = $attribute;

        if ($this->id && $this->class::where('id', $this->id)->where($attribute, $uniqueNumber)->first()) {
            return true;
        }

        if ($this->class::where($attribute, $uniqueNumber)->first()) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        $type = str_replace('_', ' ', $this->type);

        return "{$type} is already used.";
    }
}
