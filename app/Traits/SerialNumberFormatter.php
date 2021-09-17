<?php

namespace Crater\Traits;

/**
 * Trait SerialNumberFormatter
 * @package Crater\Traits;
 */

trait SerialNumberFormatter
{
    static $validPlaceholders = ['SEQUENCE', 'DATEFORMAT', 'SERIES', 'RANDSEQUENCE', 'DELIMITER'];

    /**
     * @return string
     */
    public function generateSerialNumber(string $format, int $sequenceNumber)
    {
        $regex = "/{{([A-Z]{1,})(?::)?([A-Za-z0-9]{1,4}|.{1})?}}/";
        $serialNumber = '';
        
        preg_match_all($regex, $format, $placeholders);
        array_shift($placeholders);
        
        $mappedPlaceholders = array_map(null, current($placeholders), end($placeholders));

        foreach($mappedPlaceholders as $placeholder) {
            $name = current($placeholder);
            $value = end($placeholder);

            if (in_array($name, self::$validPlaceholders))
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