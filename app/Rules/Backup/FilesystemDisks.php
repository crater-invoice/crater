<?php

namespace Crater\Rules\Backup;

use Illuminate\Contracts\Validation\Rule;

class FilesystemDisks implements Rule
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
        $configuredFileSystemDisks = config('filesystem.disks');

        return in_array($value, $configuredFileSystemDisks);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'This disk is not configured as a filesystem disk.';
    }
}
