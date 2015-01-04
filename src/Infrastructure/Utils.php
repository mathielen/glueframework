<?php
namespace Infrastructure;

class Utils
{

    public static function joinWithKey($separator, $keyvalue_seperator, array $array)
    {
        $retVal = '';

        foreach ($array as $cKey=>$cValue) {
            $retVal .= $cKey.$keyvalue_seperator.$cValue.$separator;
        }
        $retVal = substr($retVal, 0, strlen($retVal)-strlen($separator));

        return $retVal;
    }

    public static function jsonDecode($json)
    {
        return json_decode($json);
    }

    public static function jsonEncode($value)
    {
        return json_encode($value);
    }

    public static function getDocBlockParameters($propertyComment)
    {
        if (preg_match_all('/@([^\n^\r]+)/', $propertyComment, $matches)) {
            $matches = $matches[1];

            return $matches;
        }

        return false;
    }

    public static function getVariableHint($propertyComment)
    {
        if (preg_match('/@var\s+([^\s]+)/', $propertyComment, $matches)) {

            list(, $type) = $matches;

            return $type;
        }

        return false;
    }

    public static function postToObject($postVariables)
    {
        $request = new \stdClass();
        foreach ($postVariables as $key=>$value) {
            if (empty($value)) {
                continue;
            }

            $v = json_decode(utf8_encode($value));
            if (!empty($v)) {
                $value = $v;
            }
            $request->$key = $value;
        }

        return $request;
    }

    /**
     * @return bool
     */
    public static function isWindows()
    {
        return (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
    }

    /**
     * @return bool
     */
    public static function isCli()
    {
        return php_sapi_name() == "cli";
    }

    /**
     * @return string
     */
    public static function whoAmI()
    {
        if (self::isWindows()) {
            $user = getenv("username");
        } else {
            $processUser = posix_getpwuid(posix_geteuid());
            $user = $processUser['name'];
        }

        return $user;
    }

}
