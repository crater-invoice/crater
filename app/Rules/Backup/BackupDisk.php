<?php

namespace Crater\Rules\Backup;

use Illuminate\Contracts\Validation\Rule;

class BackupDisk implements Rule
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
        $configuredBackupDisks = config('backup.backup.destination.disks');

        return in_array($value, $configuredBackupDisks);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'This disk is not configured as a backup disk.';
    }
}
