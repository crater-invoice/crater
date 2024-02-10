<?php

namespace InvoiceShelf\Rules;

use Illuminate\Contracts\Validation\Rule;

class Base64Mime implements Rule
{
    private $attribute;

    private $extensions;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(array $extensions)
    {
        $this->extensions = $extensions;
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
        $this->attribute = $attribute;

        try {
            $decoded = json_decode(trim($value));
            $name = !empty($decoded->name) ? $decoded->name : '';
            $data = !empty($decoded->data) ? $decoded->data : '';
        } catch (\Exception $e) {
            return false;
        }

        $extension = pathinfo($name, PATHINFO_EXTENSION);
        if(!in_array($extension, $this->extensions)) {
            return false;
        }

        $pattern = '/^data:\w+\/[\w\+]+;base64,[\w\+\=\/]+$/';

        if (! preg_match($pattern, $data)) {
            return false;
        }

        $data = explode(',', $data);

        if (! isset($data[1]) || empty($data[1])) {
            return false;
        }

        try {
            $data = base64_decode($data[1]);
            $f = finfo_open();
            $result = finfo_buffer($f, $data, FILEINFO_EXTENSION);

            if ($result === '???') {
                return false;
            }

            if (strpos($result, '/')) {
                foreach (explode('/', $result) as $ext) {
                    if (in_array($ext, $this->extensions)) {
                        return true;
                    }
                }
            } else {
                if (in_array($result, $this->extensions)) {
                    return true;
                }
            }
        } catch (\Exception $e) {
            return false;
        }

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The '.$this->attribute.' must be a json with file of type: '.implode(', ', $this->extensions).' encoded in base64.';
    }
}
