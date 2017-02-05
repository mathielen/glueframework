<?php

namespace Infrastructure\Intl;

use Infrastructure\Exception\NotImplementedException;

class RegionBundleExtension
{
    private static $CONTINENTS = array(
        'AS' => 'Asia',
        'AN' => 'Antarctica',
        'AF' => 'Africa',
        'SA' => 'South America',
        'EU' => 'Europe',
        'OC' => 'Oceania',
        'NA' => 'North America',
    );

    private static $COUNTRY_CONTINENTS = array(
        'AF' => 'AS',
        'AX' => 'EU',
        'AL' => 'EU',
        'DZ' => 'AF',
        'AS' => 'OC',
        'AD' => 'EU',
        'AO' => 'AF',
        'AI' => 'NA',
        'AQ' => 'AN',
        'AG' => 'NA',
        'AR' => 'SA',
        'AM' => 'AS',
        'AW' => 'NA',
        'AU' => 'OC',
        'AT' => 'EU',
        'AZ' => 'AS',
        'BS' => 'NA',
        'BH' => 'AS',
        'BD' => 'AS',
        'BB' => 'NA',
        'BY' => 'EU',
        'BE' => 'EU',
        'BZ' => 'NA',
        'BJ' => 'AF',
        'BM' => 'NA',
        'BT' => 'AS',
        'BO' => 'SA',
        'BA' => 'EU',
        'BW' => 'AF',
        'BV' => 'AN',
        'BR' => 'SA',
        'IO' => 'AS',
        'BN' => 'AS',
        'BG' => 'EU',
        'BF' => 'AF',
        'BI' => 'AF',
        'KH' => 'AS',
        'CM' => 'AF',
        'CA' => 'NA',
        'CV' => 'AF',
        'KY' => 'NA',
        'CF' => 'AF',
        'TD' => 'AF',
        'CL' => 'SA',
        'CN' => 'AS',
        'CX' => 'AS',
        'CC' => 'AS',
        'CO' => 'SA',
        'KM' => 'AF',
        'CD' => 'AF',
        'CG' => 'AF',
        'CK' => 'OC',
        'CR' => 'NA',
        'CI' => 'AF',
        'HR' => 'EU',
        'CU' => 'NA',
        'CY' => 'AS',
        'CZ' => 'EU',
        'DK' => 'EU',
        'DJ' => 'AF',
        'DM' => 'NA',
        'DO' => 'NA',
        'EC' => 'SA',
        'EG' => 'AF',
        'SV' => 'NA',
        'GQ' => 'AF',
        'ER' => 'AF',
        'EE' => 'EU',
        'ET' => 'AF',
        'FO' => 'EU',
        'FK' => 'SA',
        'FJ' => 'OC',
        'FI' => 'EU',
        'FR' => 'EU',
        'GF' => 'SA',
        'PF' => 'OC',
        'TF' => 'AN',
        'GA' => 'AF',
        'GM' => 'AF',
        'GE' => 'AS',
        'DE' => 'EU',
        'GH' => 'AF',
        'GI' => 'EU',
        'GR' => 'EU',
        'GL' => 'NA',
        'GD' => 'NA',
        'GP' => 'NA',
        'GU' => 'OC',
        'GT' => 'NA',
        'GG' => 'EU',
        'GN' => 'AF',
        'GW' => 'AF',
        'GY' => 'SA',
        'HT' => 'NA',
        'HM' => 'AN',
        'VA' => 'EU',
        'HN' => 'NA',
        'HK' => 'AS',
        'HU' => 'EU',
        'IS' => 'EU',
        'IN' => 'AS',
        'ID' => 'AS',
        'IR' => 'AS',
        'IQ' => 'AS',
        'IE' => 'EU',
        'IM' => 'EU',
        'IL' => 'AS',
        'IT' => 'EU',
        'JM' => 'NA',
        'JP' => 'AS',
        'JE' => 'EU',
        'JO' => 'AS',
        'KZ' => 'AS',
        'KE' => 'AF',
        'KI' => 'OC',
        'KP' => 'AS',
        'KR' => 'AS',
        'KW' => 'AS',
        'KG' => 'AS',
        'LA' => 'AS',
        'LV' => 'EU',
        'LB' => 'AS',
        'LS' => 'AF',
        'LR' => 'AF',
        'LY' => 'AF',
        'LI' => 'EU',
        'LT' => 'EU',
        'LU' => 'EU',
        'MO' => 'AS',
        'MK' => 'EU',
        'MG' => 'AF',
        'MW' => 'AF',
        'MY' => 'AS',
        'MV' => 'AS',
        'ML' => 'AF',
        'MT' => 'EU',
        'MH' => 'OC',
        'MQ' => 'NA',
        'MR' => 'AF',
        'MU' => 'AF',
        'YT' => 'AF',
        'MX' => 'NA',
        'FM' => 'OC',
        'MD' => 'EU',
        'MC' => 'EU',
        'MN' => 'AS',
        'ME' => 'EU',
        'MS' => 'NA',
        'MA' => 'AF',
        'MZ' => 'AF',
        'MM' => 'AS',
        'NA' => 'AF',
        'NR' => 'OC',
        'NP' => 'AS',
        'AN' => 'NA',
        'NL' => 'EU',
        'NC' => 'OC',
        'NZ' => 'OC',
        'NI' => 'NA',
        'NE' => 'AF',
        'NG' => 'AF',
        'NU' => 'OC',
        'NF' => 'OC',
        'MP' => 'OC',
        'NO' => 'EU',
        'OM' => 'AS',
        'PK' => 'AS',
        'PW' => 'OC',
        'PS' => 'AS',
        'PA' => 'NA',
        'PG' => 'OC',
        'PY' => 'SA',
        'PE' => 'SA',
        'PH' => 'AS',
        'PN' => 'OC',
        'PL' => 'EU',
        'PT' => 'EU',
        'PR' => 'NA',
        'QA' => 'AS',
        'RE' => 'AF',
        'RO' => 'EU',
        'RU' => 'EU',
        'RW' => 'AF',
        'SH' => 'AF',
        'KN' => 'NA',
        'LC' => 'NA',
        'PM' => 'NA',
        'VC' => 'NA',
        'WS' => 'OC',
        'SM' => 'EU',
        'ST' => 'AF',
        'SA' => 'AS',
        'SN' => 'AF',
        'RS' => 'EU',
        'SC' => 'AF',
        'SL' => 'AF',
        'SG' => 'AS',
        'SK' => 'EU',
        'SI' => 'EU',
        'SB' => 'OC',
        'SO' => 'AF',
        'ZA' => 'AF',
        'GS' => 'AN',
        'ES' => 'EU',
        'LK' => 'AS',
        'SD' => 'AF',
        'SR' => 'SA',
        'SJ' => 'EU',
        'SZ' => 'AF',
        'SE' => 'EU',
        'CH' => 'EU',
        'SY' => 'AS',
        'TW' => 'AS',
        'TJ' => 'AS',
        'TZ' => 'AF',
        'TH' => 'AS',
        'TL' => 'AS',
        'TG' => 'AF',
        'TK' => 'OC',
        'TO' => 'OC',
        'TT' => 'NA',
        'TN' => 'AF',
        'TR' => 'AS',
        'TM' => 'AS',
        'TC' => 'NA',
        'TV' => 'OC',
        'UG' => 'AF',
        'UA' => 'EU',
        'AE' => 'AS',
        'GB' => 'EU',
        'UM' => 'OC',
        'US' => 'NA',
        'UY' => 'SA',
        'UZ' => 'AS',
        'VU' => 'OC',
        'VE' => 'SA',
        'VN' => 'AS',
        'VG' => 'NA',
        'VI' => 'NA',
        'WF' => 'OC',
        'EH' => 'AF',
        'YE' => 'AS',
        'ZM' => 'AF',
        'ZW' => 'AF',
    );

    public static function getCountriesByContinent($continent)
    {
        if (!array_key_exists($continent, self::$CONTINENTS)) {
            throw new \InvalidArgumentException("Continent $continent could not be found");
        }

        return array_keys(self::$COUNTRY_CONTINENTS, $continent);
    }

    public static function getContinent($country)
    {
        if (!array_key_exists($country, self::$COUNTRY_CONTINENTS)) {
            throw new \InvalidArgumentException("Country $country could not be found");
        }

        return self::$COUNTRY_CONTINENTS[$country];
    }

    public static function getContinentName($country)
    {
        $continent = self::getContinent($country);

        return self::$CONTINENTS[$continent];
    }
    
    public static function getState($country, $zip)
    {
        if ($country !== 'DE') {
            throw new NotImplementedException("Only germany is implemented");
        }

        $plzArray = [];

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '01001';
        $plzArray[$currPos][2] = '01936';
        $plzArray[$currPos][3] = 'de-sn';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '01941';
        $plzArray[$currPos][2] = '01998';
        $plzArray[$currPos][3] = 'de-bb';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '02601';
        $plzArray[$currPos][2] = '02999';
        $plzArray[$currPos][3] = 'de-sn';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '03001';
        $plzArray[$currPos][2] = '03253';
        $plzArray[$currPos][3] = 'de-bb';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '04001';
        $plzArray[$currPos][2] = '04579';
        $plzArray[$currPos][3] = 'de-sn';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '04581';
        $plzArray[$currPos][2] = '04639';
        $plzArray[$currPos][3] = 'de-th';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '04641';
        $plzArray[$currPos][2] = '04889';
        $plzArray[$currPos][3] = 'de-sn';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '04891';
        $plzArray[$currPos][2] = '04938';
        $plzArray[$currPos][3] = 'de-bb';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '06001';
        $plzArray[$currPos][2] = '06548';
        $plzArray[$currPos][3] = 'de-st';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '06551';
        $plzArray[$currPos][2] = '06578';
        $plzArray[$currPos][3] = 'de-th';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '06601';
        $plzArray[$currPos][2] = '06928';
        $plzArray[$currPos][3] = 'de-st';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '07301';
        $plzArray[$currPos][2] = '07919';
        $plzArray[$currPos][3] = 'de-th';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '07919';
        $plzArray[$currPos][2] = '07919';
        $plzArray[$currPos][3] = 'de-sn';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '07919';
        $plzArray[$currPos][2] = '07919';
        $plzArray[$currPos][3] = 'de-th';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '07919';
        $plzArray[$currPos][2] = '07919';
        $plzArray[$currPos][3] = 'de-sn';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '07920';
        $plzArray[$currPos][2] = '07950';
        $plzArray[$currPos][3] = 'de-th';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '07951';
        $plzArray[$currPos][2] = '07951';
        $plzArray[$currPos][3] = 'de-sn';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '07952';
        $plzArray[$currPos][2] = '07952';
        $plzArray[$currPos][3] = 'de-th';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '07952';
        $plzArray[$currPos][2] = '07952';
        $plzArray[$currPos][3] = 'de-sn';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '07953';
        $plzArray[$currPos][2] = '07980';
        $plzArray[$currPos][3] = 'de-th';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '07982';
        $plzArray[$currPos][2] = '07982';
        $plzArray[$currPos][3] = 'de-sn';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '07985';
        $plzArray[$currPos][2] = '07985';
        $plzArray[$currPos][3] = 'de-th';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '07985';
        $plzArray[$currPos][2] = '07985';
        $plzArray[$currPos][3] = 'de-sn';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '07985';
        $plzArray[$currPos][2] = '07989';
        $plzArray[$currPos][3] = 'de-th';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '08001';
        $plzArray[$currPos][2] = '09669';
        $plzArray[$currPos][3] = 'de-sn';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '10001';
        $plzArray[$currPos][2] = '14330';
        $plzArray[$currPos][3] = 'de-be';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '14401';
        $plzArray[$currPos][2] = '14715';
        $plzArray[$currPos][3] = 'de-bb';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '14715';
        $plzArray[$currPos][2] = '14715';
        $plzArray[$currPos][3] = 'de-st';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '14723';
        $plzArray[$currPos][2] = '16949';
        $plzArray[$currPos][3] = 'de-bb';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '17001';
        $plzArray[$currPos][2] = '17256';
        $plzArray[$currPos][3] = 'de-mv';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '17258';
        $plzArray[$currPos][2] = '17258';
        $plzArray[$currPos][3] = 'de-bb';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '17258';
        $plzArray[$currPos][2] = '17259';
        $plzArray[$currPos][3] = 'de-mv';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '17261';
        $plzArray[$currPos][2] = '17291';
        $plzArray[$currPos][3] = 'de-bb';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '17301';
        $plzArray[$currPos][2] = '17309';
        $plzArray[$currPos][3] = 'de-mv';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '17309';
        $plzArray[$currPos][2] = '17309';
        $plzArray[$currPos][3] = 'de-bb';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '17309';
        $plzArray[$currPos][2] = '17321';
        $plzArray[$currPos][3] = 'de-mv';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '17321';
        $plzArray[$currPos][2] = '17321';
        $plzArray[$currPos][3] = 'de-bb';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '17321';
        $plzArray[$currPos][2] = '17322';
        $plzArray[$currPos][3] = 'de-mv';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '17326';
        $plzArray[$currPos][2] = '17326';
        $plzArray[$currPos][3] = 'de-bb';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '17328';
        $plzArray[$currPos][2] = '17331';
        $plzArray[$currPos][3] = 'de-mv';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '17335';
        $plzArray[$currPos][2] = '17335';
        $plzArray[$currPos][3] = 'de-bb';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '17335';
        $plzArray[$currPos][2] = '17335';
        $plzArray[$currPos][3] = 'de-mv';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '17337';
        $plzArray[$currPos][2] = '17337';
        $plzArray[$currPos][3] = 'de-bb';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '17337';
        $plzArray[$currPos][2] = '19260';
        $plzArray[$currPos][3] = 'de-mv';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '19271';
        $plzArray[$currPos][2] = '19273';
        $plzArray[$currPos][3] = 'de-ni';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '19273';
        $plzArray[$currPos][2] = '19273';
        $plzArray[$currPos][3] = 'de-mv';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '19273';
        $plzArray[$currPos][2] = '19306';
        $plzArray[$currPos][3] = 'de-mv';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '19307';
        $plzArray[$currPos][2] = '19357';
        $plzArray[$currPos][3] = 'de-bb';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '19357';
        $plzArray[$currPos][2] = '19417';
        $plzArray[$currPos][3] = 'de-mv';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '20001';
        $plzArray[$currPos][2] = '21037';
        $plzArray[$currPos][3] = 'de-hh';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '21039';
        $plzArray[$currPos][2] = '21039';
        $plzArray[$currPos][3] = 'de-sh';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '21039';
        $plzArray[$currPos][2] = '21170';
        $plzArray[$currPos][3] = 'de-hh';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '21202';
        $plzArray[$currPos][2] = '21449';
        $plzArray[$currPos][3] = 'de-ni';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '21451';
        $plzArray[$currPos][2] = '21521';
        $plzArray[$currPos][3] = 'de-sh';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '21522';
        $plzArray[$currPos][2] = '21522';
        $plzArray[$currPos][3] = 'de-ni';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '21524';
        $plzArray[$currPos][2] = '21529';
        $plzArray[$currPos][3] = 'de-sh';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '21601';
        $plzArray[$currPos][2] = '21789';
        $plzArray[$currPos][3] = 'de-ni';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '22001';
        $plzArray[$currPos][2] = '22113';
        $plzArray[$currPos][3] = 'de-hh';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '22113';
        $plzArray[$currPos][2] = '22113';
        $plzArray[$currPos][3] = 'de-sh';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '22115';
        $plzArray[$currPos][2] = '22143';
        $plzArray[$currPos][3] = 'de-hh';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '22145';
        $plzArray[$currPos][2] = '22145';
        $plzArray[$currPos][3] = 'de-sh';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '22145';
        $plzArray[$currPos][2] = '22145';
        $plzArray[$currPos][3] = 'de-hh';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '22145';
        $plzArray[$currPos][2] = '22145';
        $plzArray[$currPos][3] = 'de-sh';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '22147';
        $plzArray[$currPos][2] = '22786';
        $plzArray[$currPos][3] = 'de-hh';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '22801';
        $plzArray[$currPos][2] = '23919';
        $plzArray[$currPos][3] = 'de-sh';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '23921';
        $plzArray[$currPos][2] = '23999';
        $plzArray[$currPos][3] = 'de-mv';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '24001';
        $plzArray[$currPos][2] = '25999';
        $plzArray[$currPos][3] = 'de-sh';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '26001';
        $plzArray[$currPos][2] = '27478';
        $plzArray[$currPos][3] = 'de-ni';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '27483';
        $plzArray[$currPos][2] = '27498';
        $plzArray[$currPos][3] = 'de-sh';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '27499';
        $plzArray[$currPos][2] = '27499';
        $plzArray[$currPos][3] = 'de-hh';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '27501';
        $plzArray[$currPos][2] = '27580';
        $plzArray[$currPos][3] = 'de-hb';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '27607';
        $plzArray[$currPos][2] = '27809';
        $plzArray[$currPos][3] = 'de-ni';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '28001';
        $plzArray[$currPos][2] = '28779';
        $plzArray[$currPos][3] = 'de-hb';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '28784';
        $plzArray[$currPos][2] = '29399';
        $plzArray[$currPos][3] = 'de-ni';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '29401';
        $plzArray[$currPos][2] = '29416';
        $plzArray[$currPos][3] = 'de-st';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '29431';
        $plzArray[$currPos][2] = '31868';
        $plzArray[$currPos][3] = 'de-ni';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '32001';
        $plzArray[$currPos][2] = '33829';
        $plzArray[$currPos][3] = 'de-nw';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '34001';
        $plzArray[$currPos][2] = '34329';
        $plzArray[$currPos][3] = 'de-he';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '34331';
        $plzArray[$currPos][2] = '34353';
        $plzArray[$currPos][3] = 'de-ni';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '34355';
        $plzArray[$currPos][2] = '34355';
        $plzArray[$currPos][3] = 'de-he';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '34355';
        $plzArray[$currPos][2] = '34355';
        $plzArray[$currPos][3] = 'de-ni';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '34356';
        $plzArray[$currPos][2] = '34399';
        $plzArray[$currPos][3] = 'de-he';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '34401';
        $plzArray[$currPos][2] = '34439';
        $plzArray[$currPos][3] = 'de-nw';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '34441';
        $plzArray[$currPos][2] = '36399';
        $plzArray[$currPos][3] = 'de-he';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '36401';
        $plzArray[$currPos][2] = '36469';
        $plzArray[$currPos][3] = 'de-th';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '37001';
        $plzArray[$currPos][2] = '37194';
        $plzArray[$currPos][3] = 'de-ni';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '37194';
        $plzArray[$currPos][2] = '37195';
        $plzArray[$currPos][3] = 'de-he';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '37197';
        $plzArray[$currPos][2] = '37199';
        $plzArray[$currPos][3] = 'de-ni';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '37201';
        $plzArray[$currPos][2] = '37299';
        $plzArray[$currPos][3] = 'de-he';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '37301';
        $plzArray[$currPos][2] = '37359';
        $plzArray[$currPos][3] = 'de-th';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '37401';
        $plzArray[$currPos][2] = '37649';
        $plzArray[$currPos][3] = 'de-ni';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '37651';
        $plzArray[$currPos][2] = '37688';
        $plzArray[$currPos][3] = 'de-nw';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '37689';
        $plzArray[$currPos][2] = '37691';
        $plzArray[$currPos][3] = 'de-ni';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '37692';
        $plzArray[$currPos][2] = '37696';
        $plzArray[$currPos][3] = 'de-nw';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '37697';
        $plzArray[$currPos][2] = '38479';
        $plzArray[$currPos][3] = 'de-ni';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '38481';
        $plzArray[$currPos][2] = '38489';
        $plzArray[$currPos][3] = 'de-st';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '38501';
        $plzArray[$currPos][2] = '38729';
        $plzArray[$currPos][3] = 'de-ni';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '38801';
        $plzArray[$currPos][2] = '39649';
        $plzArray[$currPos][3] = 'de-st';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '40001';
        $plzArray[$currPos][2] = '48432';
        $plzArray[$currPos][3] = 'de-nw';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '48442';
        $plzArray[$currPos][2] = '48465';
        $plzArray[$currPos][3] = 'de-ni';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '48466';
        $plzArray[$currPos][2] = '48477';
        $plzArray[$currPos][3] = 'de-nw';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '48478';
        $plzArray[$currPos][2] = '48480';
        $plzArray[$currPos][3] = 'de-ni';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '48481';
        $plzArray[$currPos][2] = '48485';
        $plzArray[$currPos][3] = 'de-nw';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '48486';
        $plzArray[$currPos][2] = '48488';
        $plzArray[$currPos][3] = 'de-ni';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '48489';
        $plzArray[$currPos][2] = '48496';
        $plzArray[$currPos][3] = 'de-nw';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '48497';
        $plzArray[$currPos][2] = '48531';
        $plzArray[$currPos][3] = 'de-ni';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '48541';
        $plzArray[$currPos][2] = '48739';
        $plzArray[$currPos][3] = 'de-nw';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '49001';
        $plzArray[$currPos][2] = '49459';
        $plzArray[$currPos][3] = 'de-ni';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '49461';
        $plzArray[$currPos][2] = '49549';
        $plzArray[$currPos][3] = 'de-nw';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '49551';
        $plzArray[$currPos][2] = '49849';
        $plzArray[$currPos][3] = 'de-ni';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '50101';
        $plzArray[$currPos][2] = '51597';
        $plzArray[$currPos][3] = 'de-nw';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '51598';
        $plzArray[$currPos][2] = '51598';
        $plzArray[$currPos][3] = 'de-rp';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '51601';
        $plzArray[$currPos][2] = '53359';
        $plzArray[$currPos][3] = 'de-nw';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '53401';
        $plzArray[$currPos][2] = '53579';
        $plzArray[$currPos][3] = 'de-rp';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '53581';
        $plzArray[$currPos][2] = '53604';
        $plzArray[$currPos][3] = 'de-nw';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '53614';
        $plzArray[$currPos][2] = '53619';
        $plzArray[$currPos][3] = 'de-rp';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '53621';
        $plzArray[$currPos][2] = '53949';
        $plzArray[$currPos][3] = 'de-nw';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '54181';
        $plzArray[$currPos][2] = '55239';
        $plzArray[$currPos][3] = 'de-rp';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '55240';
        $plzArray[$currPos][2] = '55252';
        $plzArray[$currPos][3] = 'de-he';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '55253';
        $plzArray[$currPos][2] = '56869';
        $plzArray[$currPos][3] = 'de-rp';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '57001';
        $plzArray[$currPos][2] = '57489';
        $plzArray[$currPos][3] = 'de-nw';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '57501';
        $plzArray[$currPos][2] = '57648';
        $plzArray[$currPos][3] = 'de-rp';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '58001';
        $plzArray[$currPos][2] = '59966';
        $plzArray[$currPos][3] = 'de-nw';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '59969';
        $plzArray[$currPos][2] = '59969';
        $plzArray[$currPos][3] = 'de-he';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '59969';
        $plzArray[$currPos][2] = '59969';
        $plzArray[$currPos][3] = 'de-nw';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '60001';
        $plzArray[$currPos][2] = '63699';
        $plzArray[$currPos][3] = 'de-he';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '63701';
        $plzArray[$currPos][2] = '63774';
        $plzArray[$currPos][3] = 'de-by';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '63776';
        $plzArray[$currPos][2] = '63776';
        $plzArray[$currPos][3] = 'de-he';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '63776';
        $plzArray[$currPos][2] = '63928';
        $plzArray[$currPos][3] = 'de-by';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '63928';
        $plzArray[$currPos][2] = '63928';
        $plzArray[$currPos][3] = 'de-bw';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '63930';
        $plzArray[$currPos][2] = '63939';
        $plzArray[$currPos][3] = 'de-by';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '64201';
        $plzArray[$currPos][2] = '64753';
        $plzArray[$currPos][3] = 'de-he';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '64754';
        $plzArray[$currPos][2] = '64754';
        $plzArray[$currPos][3] = 'de-bw';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '64754';
        $plzArray[$currPos][2] = '65326';
        $plzArray[$currPos][3] = 'de-he';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '65326';
        $plzArray[$currPos][2] = '65326';
        $plzArray[$currPos][3] = 'de-rp';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '65327';
        $plzArray[$currPos][2] = '65391';
        $plzArray[$currPos][3] = 'de-he';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '65391';
        $plzArray[$currPos][2] = '65391';
        $plzArray[$currPos][3] = 'de-rp';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '65392';
        $plzArray[$currPos][2] = '65556';
        $plzArray[$currPos][3] = 'de-he';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '65558';
        $plzArray[$currPos][2] = '65582';
        $plzArray[$currPos][3] = 'de-rp';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '65583';
        $plzArray[$currPos][2] = '65620';
        $plzArray[$currPos][3] = 'de-he';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '65621';
        $plzArray[$currPos][2] = '65626';
        $plzArray[$currPos][3] = 'de-rp';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '65627';
        $plzArray[$currPos][2] = '65627';
        $plzArray[$currPos][3] = 'de-he';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '65629';
        $plzArray[$currPos][2] = '65629';
        $plzArray[$currPos][3] = 'de-rp';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '65701';
        $plzArray[$currPos][2] = '65936';
        $plzArray[$currPos][3] = 'de-he';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '66001';
        $plzArray[$currPos][2] = '66459';
        $plzArray[$currPos][3] = 'de-sl';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '66461';
        $plzArray[$currPos][2] = '66509';
        $plzArray[$currPos][3] = 'de-rp';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '66511';
        $plzArray[$currPos][2] = '66839';
        $plzArray[$currPos][3] = 'de-sl';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '66841';
        $plzArray[$currPos][2] = '67829';
        $plzArray[$currPos][3] = 'de-rp';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '68001';
        $plzArray[$currPos][2] = '68312';
        $plzArray[$currPos][3] = 'de-bw';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '68501';
        $plzArray[$currPos][2] = '68519';
        $plzArray[$currPos][3] = 'de-he';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '68520';
        $plzArray[$currPos][2] = '68549';
        $plzArray[$currPos][3] = 'de-bw';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '68601';
        $plzArray[$currPos][2] = '68649';
        $plzArray[$currPos][3] = 'de-he';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '68701';
        $plzArray[$currPos][2] = '69234';
        $plzArray[$currPos][3] = 'de-bw';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '69235';
        $plzArray[$currPos][2] = '69239';
        $plzArray[$currPos][3] = 'de-he';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '69240';
        $plzArray[$currPos][2] = '69429';
        $plzArray[$currPos][3] = 'de-bw';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '69430';
        $plzArray[$currPos][2] = '69431';
        $plzArray[$currPos][3] = 'de-he';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '69434';
        $plzArray[$currPos][2] = '69434';
        $plzArray[$currPos][3] = 'de-bw';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '69434';
        $plzArray[$currPos][2] = '69434';
        $plzArray[$currPos][3] = 'de-he';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '69435';
        $plzArray[$currPos][2] = '69469';
        $plzArray[$currPos][3] = 'de-bw';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '69479';
        $plzArray[$currPos][2] = '69488';
        $plzArray[$currPos][3] = 'de-he';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '69489';
        $plzArray[$currPos][2] = '69502';
        $plzArray[$currPos][3] = 'de-bw';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '69503';
        $plzArray[$currPos][2] = '69509';
        $plzArray[$currPos][3] = 'de-he';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '69510';
        $plzArray[$currPos][2] = '69514';
        $plzArray[$currPos][3] = 'de-bw';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '69515';
        $plzArray[$currPos][2] = '69518';
        $plzArray[$currPos][3] = 'de-he';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '70001';
        $plzArray[$currPos][2] = '74592';
        $plzArray[$currPos][3] = 'de-bw';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '74594';
        $plzArray[$currPos][2] = '74594';
        $plzArray[$currPos][3] = 'de-by';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '74594';
        $plzArray[$currPos][2] = '76709';
        $plzArray[$currPos][3] = 'de-bw';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '76711';
        $plzArray[$currPos][2] = '76891';
        $plzArray[$currPos][3] = 'de-rp';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '77601';
        $plzArray[$currPos][2] = '79879';
        $plzArray[$currPos][3] = 'de-bw';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '80001';
        $plzArray[$currPos][2] = '87490';
        $plzArray[$currPos][3] = 'de-by';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '87493';
        $plzArray[$currPos][2] = '87561';
        $plzArray[$currPos][3] = 'de-by';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '87571';
        $plzArray[$currPos][2] = '87789';
        $plzArray[$currPos][3] = 'de-by';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '88001';
        $plzArray[$currPos][2] = '88099';
        $plzArray[$currPos][3] = 'de-bw';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '88101';
        $plzArray[$currPos][2] = '88146';
        $plzArray[$currPos][3] = 'de-by';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '88147';
        $plzArray[$currPos][2] = '88147';
        $plzArray[$currPos][3] = 'de-bw';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '88147';
        $plzArray[$currPos][2] = '88179';
        $plzArray[$currPos][3] = 'de-by';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '88181';
        $plzArray[$currPos][2] = '89079';
        $plzArray[$currPos][3] = 'de-bw';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '89081';
        $plzArray[$currPos][2] = '89081';
        $plzArray[$currPos][3] = 'de-by';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '89081';
        $plzArray[$currPos][2] = '89085';
        $plzArray[$currPos][3] = 'de-bw';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '89087';
        $plzArray[$currPos][2] = '89087';
        $plzArray[$currPos][3] = 'de-by';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '89090';
        $plzArray[$currPos][2] = '89198';
        $plzArray[$currPos][3] = 'de-bw';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '89201';
        $plzArray[$currPos][2] = '89449';
        $plzArray[$currPos][3] = 'de-by';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '89501';
        $plzArray[$currPos][2] = '89619';
        $plzArray[$currPos][3] = 'de-bw';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '90001';
        $plzArray[$currPos][2] = '96489';
        $plzArray[$currPos][3] = 'de-by';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '96501';
        $plzArray[$currPos][2] = '96529';
        $plzArray[$currPos][3] = 'de-th';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '97001';
        $plzArray[$currPos][2] = '97859';
        $plzArray[$currPos][3] = 'de-by';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '97861';
        $plzArray[$currPos][2] = '97877';
        $plzArray[$currPos][3] = 'de-bw';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '97888';
        $plzArray[$currPos][2] = '97892';
        $plzArray[$currPos][3] = 'de-by';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '97893';
        $plzArray[$currPos][2] = '97896';
        $plzArray[$currPos][3] = 'de-bw';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '97896';
        $plzArray[$currPos][2] = '97896';
        $plzArray[$currPos][3] = 'de-by';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '97897';
        $plzArray[$currPos][2] = '97900';
        $plzArray[$currPos][3] = 'de-bw';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '97901';
        $plzArray[$currPos][2] = '97909';
        $plzArray[$currPos][3] = 'de-by';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '97911';
        $plzArray[$currPos][2] = '97999';
        $plzArray[$currPos][3] = 'de-bw';

        $currPos = count($plzArray);
        $plzArray[$currPos] = [];
        $plzArray[$currPos][1] = '98501';
        $plzArray[$currPos][2] = '99998';
        $plzArray[$currPos][3] = 'de-th';

        for ($i = 0; $i < $currPos; ++$i) {
            if ($zip >= $plzArray[$i][1] && $zip <= $plzArray[$i][2]) {
                return $plzArray[$i][3];
            }
        }
        
        return null;
    }
    
}
