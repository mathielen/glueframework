<?php
namespace Infrastructure\Validation;

class Utils
{

    public static function isMysqlDate($subject)
    {
        return preg_match('^([1-9]|[12][0-9]|3[01])[- /.]([1-9]|1[012])[- /.](19|20)\d\d$', $subject);
    }

}