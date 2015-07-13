<?php
namespace Infrastructure;

class DataStructureUtils
{

    public static function arrayFieldToProperty(array $array, $fieldName, &$property)
    {
        //no data avail
        if (!array_key_exists($fieldName, $array)) { //dont use isset - we want to apply 0's
            return false;
        }

        //no change
        if ($property == $array[$fieldName]) {
            return false;
        }

        $property = $array[$fieldName];

        return true;
    }

    public static function limitedString($string, $maxlen = 60)
    {
        //we have to use mb_* because $string is utf-8
        return mb_strlen($string) >= $maxlen ? mb_substr($string, 0, $maxlen - 3) . '...' : $string;
    }

}
