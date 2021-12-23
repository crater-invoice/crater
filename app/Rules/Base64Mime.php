<?php

namespace Crater\Rules;

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
            $data = json_decode($value)->data;
        } catch (\Exception $e) {
            return False;
        }

        $pattern = '/^data:\w+\/[\w\+]+;base64,[\w\+\=\/]+$/';

        if(!preg_match($pattern, $data)) {
            return False;
        }

        $data = explode(',', $data);

        if(!isset($data[1]) || empty($data[1])) {
            return False;
        }

        try {
            $data = base64_decode($data[1]);
            $f = finfo_open();
            $result = finfo_buffer($f, $data, FILEINFO_EXTENSION);

            if($result === '???')
                return False;

            if(strpos($result, '/')) {
                foreach(explode('/', $result) as $ext) {
                    if(in_array($ext, $this->extensions))
                        return True;
                }
            } else {
                if(in_array($result, $this->extensions))
                    return True;
            }
        } catch (\Exception $e) {
            return False;
        }
        
        return False;

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The ' . $this->attribute . ' must be a json with file of type: ' . implode(', ', $this->extensions) . ' encoded in base64.';
    }
}
