<?php

namespace Crater\Services;

use Crater\Models\CompanySetting;

/**
 * SerialNumberFormatter
 * @package Crater\Services;
 */

class SerialNumberFormatter
{
    const VALID_PLACEHOLDERS = ['SEQUENCE', 'DATEFORMAT', 'SERIES', 'RANDSEQUENCE', 'DELIMITER'];

    private $model;

    /**
     * @var string
     */
    public $nextSequenceNumber;

    /**
     * @param $model
     * @return $this
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @return string
     */
    public function getNextNumber()
    {
        $modelName = strtolower(class_basename($this->model));
        $settingKey = $modelName . '_format';
        
        $format = CompanySetting::getSetting(
            $settingKey, 
            request()->header('company')
        );

        $this->nextSequenceNumber ? 
            $this->nextSequenceNumber : $this->setNextSequenceNumber();

        $serialNumber = $this->generateSerialNumber(
            $format, 
            $this->nextSequenceNumber
        );

        return $serialNumber;
    }

    /**
     * @return $this
     */
    public function setNextSequenceNumber()
    {
        $last = $this->model::orderBy('sequence_number', 'desc')
            ->take(1)
            ->first();

        $this->nextSequenceNumber = ($last) ? $last->sequence_number+1 : 1;

        return $this;
    }

    /**
     * @return string
     */
    private function generateSerialNumber(string $format, int $sequenceNumber)
    {
        $regex = "/{{([A-Z]{1,})(?::)?([A-Za-z0-9]{1,4}|.{1})?}}/";
        $serialNumber = '';
        
        preg_match_all($regex, $format, $placeholders);
        array_shift($placeholders);
        
        /** @var array */
        $mappedPlaceholders = array_map(
            null, 
            current($placeholders), 
            end($placeholders)
        );

        foreach($mappedPlaceholders as $placeholder) {
            $name = current($placeholder);
            $value = end($placeholder);

            if (in_array($name, self::VALID_PLACEHOLDERS))
                switch ($name) {
                    case "SEQUENCE":
                        $serialNumber .= str_pad($sequenceNumber, $value, 0, STR_PAD_LEFT);
                        break;
                    case "DATEFORMAT":
                        $serialNumber .= date($value);
                        break;
                    case "RANDSEQUENCE":
                        $value = (!$value) ? 6 : $value;
                        $serialNumber .= substr(bin2hex(random_bytes($value)), 0, $value);
                        break;
                    default:
                        $serialNumber .= $value;
                }
        }
        
        return $serialNumber;
    }
}