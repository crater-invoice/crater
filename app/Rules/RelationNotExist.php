<?php

namespace Crater\Rules;

use Illuminate\Contracts\Validation\Rule;

class RelationNotExist implements Rule
{
    public $class;

    public $relation;

    /**
     * Create a new rule instance.
     * @param  string  $class
     * @param  string  $relation
     * @return void
     */
    public function __construct(string $class = null, string $relation = null)
    {
        $this->class = $class;
        $this->relation = $relation;
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
        $relation = $this->relation;

        if ($this->class::find($value)->$relation()->exists()) {
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
        return "Relation {$this->relation} exists.";
    }
}
