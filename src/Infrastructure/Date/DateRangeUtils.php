<?php
namespace Infrastructure\Date;

class DateRangeUtils
{

    /**
     * 2014Y year
     * 2014HY2 half year
     * 2014Q3 quarter
     * 2014M1 month
     * 2014CW17 calendar week
     * 2014-01-05 date
     * 2011Y>2013Y from year to year
     * 2011HY1>2013HY3 from half year to half year
     * 2011Q1>2012Q2 from quarter to quarter
     * 2011M1>2012M2 from month to month
     * 2011CW1>2011CW15 from calendar week to calendar week
     * 2011-01-01>2012-01-01 from date to date
     *
     * @return array
     */
    public static function explodeDateRange($dateRangeShortcut)
    {
        $elementMatchExpr = '([0-9]{4})(Y|HY|Q|M|CW|-)([0-9]{1,2}(-[0-9]{2})?)?';

        if (preg_match("/^>?$elementMatchExpr>?($elementMatchExpr)?$/i", $dateRangeShortcut, $matches)) {
            $type = $matches[2];
            $yearTo = $yearFrom = $matches[1];
            $modifierTo = $modifierFrom = isset($matches[3])?$matches[3]:null;

            $isOnlyTo = $dateRangeShortcut[0] === '>';                  //>2013-01-01
            $isOnlyFrom = substr($dateRangeShortcut, -1, 1) === '>';    //2012-01-01>
            if ($isOnlyFrom && $isOnlyTo) {
                throw new \InvalidArgumentException("Can not be only-from and only-to range.");
            }
            $isFromToExpression = count($matches) > 5;                  //2011-01-01>2012-01-01

            if ($isFromToExpression) {
                $yearTo = $matches[6];
                $modifierTo = isset($matches[8])?$matches[8]:null;
            }

            switch ($type) {
                case 'Y':
                    $range = self::getYearRange($yearFrom, $yearTo);
                    break;
                case 'HY':
                    $range = self::getHalfYearRange($yearFrom, $modifierFrom, $yearTo, $modifierTo);
                    break;
                case 'Q':
                    $range = self::getQuarterRange($yearFrom, $modifierFrom, $yearTo, $modifierTo);
                    break;
                case 'M':
                    $range = self::getMonthRange($yearFrom, $modifierFrom, $yearTo, $modifierTo);
                    break;
                case 'CW':
                    $range = self::getCalendarWeekRange($yearFrom, $modifierFrom, $yearTo, $modifierTo);
                    break;
                case '-':
                    $range = self::getDateRange($yearFrom, $modifierFrom, $yearTo, $modifierTo);
                    break;
                default:
                    throw new \InvalidArgumentException("Unknown type $type");
            }

            if ($isOnlyTo) {
                unset($range['from']);
            } elseif ($isOnlyFrom) {
                unset($range['to']);
            }

            return $range;
        }

        throw new \InvalidArgumentException("dateRangeShortcut was invalid: " . $dateRangeShortcut);
    }

    private static function validateDate($date, $format = 'Y-m-d')
    {
        $d = \DateTime::createFromFormat($format, $date);

        return $d && $d->format($format) == $date;
    }

    private static function getDateRange($yearFrom, $dayFrom, $yearTo, $dayTo)
    {
        $dateFrom = $yearFrom . '-' . $dayFrom;
        if (!self::validateDate($dateFrom)) {
            throw new \InvalidArgumentException("dateFrom is invalid!");
        }
        $dateTo = $yearTo . '-' . $dayTo;
        if (!self::validateDate($dateFrom)) {
            throw new \InvalidArgumentException("dateFrom is invalid!");
        }

        return ['from' => $dateFrom, 'to' => $dateTo];
    }

    private static function getYearRange($yearFrom, $yearTo)
    {
        return ['from' => $yearFrom . '-01-01', 'to' => $yearTo . '-12-31'];
    }

    private static function getHalfYearRange($yearFrom, $halfYearFrom, $yearTo, $halfYearTo)
    {
        if (empty($halfYearFrom) || $halfYearFrom > 2) {
            throw new \InvalidArgumentException("Invalid halfYearFrom " . $halfYearFrom);
        }
        if (empty($halfYearTo) || $halfYearTo > 2) {
            throw new \InvalidArgumentException("Invalid halfYearTo " . $halfYearTo);
        }

        $firstDayHalfYear = date("Y-m-d", strtotime($yearFrom . '-' . (($halfYearFrom * 6) - 5) . '-01'));
        $lastDayHalfYear = date("Y-m-t", strtotime($yearTo . '-' . ($halfYearTo * 6) . '-01'));

        return ['from' => $firstDayHalfYear, 'to' => $lastDayHalfYear];
    }

    private static function getQuarterRange($yearFrom, $quarterFrom, $yearTo, $quarterTo)
    {
        if (empty($quarterFrom) || $quarterFrom > 4) {
            throw new \InvalidArgumentException("Invalid quarterFrom " . $quarterFrom);
        }
        if (empty($quarterTo) || $quarterTo > 4) {
            throw new \InvalidArgumentException("Invalid quarterTo " . $quarterTo);
        }

        $firstDayQuarter = date("Y-m-d", strtotime($yearFrom . '-' . (($quarterFrom * 3) - 2) . '-01'));
        $lastDayQuarter = date("Y-m-t", strtotime($yearTo . '-' . ($quarterTo * 3) . '-01'));

        return ['from' => $firstDayQuarter, 'to' => $lastDayQuarter];
    }

    private static function getMonthRange($yearFrom, $monthFrom, $yearTo, $monthTo)
    {
        if (empty($monthFrom) || $monthFrom > 12) {
            throw new \InvalidArgumentException("Invalid monthFrom " . $monthFrom);
        }
        if (empty($monthTo) || $monthTo > 12) {
            throw new \InvalidArgumentException("Invalid monthTo " . $monthTo);
        }

        $firstDayMonth = date("Y-m-d", strtotime($yearFrom . '-' . $monthFrom . '-01'));
        $lastDayMonth = date("Y-m-t", strtotime($yearTo . '-' . $monthTo . '-01'));

        return ['from' => $firstDayMonth, 'to' => $lastDayMonth];
    }

    private static function getCalendarWeekRange($yearFrom, $calendarWeekFrom, $yearTo, $calendarWeekTo)
    {
        if (empty($calendarWeekFrom) || $calendarWeekFrom > 54) {
            throw new \InvalidArgumentException("Invalid calendarWeekFrom " . $calendarWeekFrom);
        }
        if (empty($calendarWeekTo) || $calendarWeekTo > 54) {
            throw new \InvalidArgumentException("Invalid calendarWeekTo " . $calendarWeekTo);
        }
        $calendarWeekFrom = str_pad($calendarWeekFrom, 2 ,'0', STR_PAD_LEFT);
        $calendarWeekTo = str_pad($calendarWeekTo, 2 ,'0', STR_PAD_LEFT);

        $firstDayWeek = date("Y-m-d", strtotime("{$yearFrom}-W{$calendarWeekFrom}-1"));
        $lastDayWeek = date("Y-m-d", strtotime("{$yearTo}-W{$calendarWeekTo}-7"));

        return ['from' => $firstDayWeek, 'to' => $lastDayWeek];
    }

}
