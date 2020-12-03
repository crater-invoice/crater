<?php

namespace Crater\Rules\Backup;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class PathToZip implements Rule
{
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
        return Str::endsWith($value, '.zip');
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The given value must be a path to a zip file.';
    }
}
