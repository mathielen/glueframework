<?php
namespace Infrastructure\Validation;

class Utils
{

    /**
     * returns true if valid mysql date (Y-m-d)
     *
     * @param unknown $subject
     * @return number
     */
    public static function isMysqlDate($subject)
    {
        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $subject);
    }

    /**
     * returns true if integer (as string or as real int type)
     *
     * @param unknown $input
     * @return boolean
     */
    public static function isInteger($input)
    {
        return(ctype_digit(strval($input)));
    }

}