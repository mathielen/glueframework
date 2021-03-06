<?php

namespace Infrastructure\Date;

use Assert\Assertion;
use Doctrine\Common\Cache\Cache;
use GuzzleHttp\Client;
use Infrastructure\Exception\NotImplementedException;

class BusinessDaysFinder
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var Cache
     */
    private $cache;

    private $weekDays;

    public function __construct(Client $client, Cache $cache, array $weekDays = [1, 2, 3, 4, 5])
    {
        $this->client = $client;
        $this->cache = $cache;
        $this->weekDays = $weekDays;
    }

    /**
     * @throws NotImplementedException
     */
    public function isBusinessDay(\DateTime $date, string $countryCode, string $provinceCode = null): bool
    {
        if ($countryCode != 'DE') {
            throw new NotImplementedException('Only DE supported');
        }

        $weekDay = $date->format('w');

        //weekend
        if (!in_array($weekDay, $this->weekDays)) {
            return false;
        }

        return !$this->isHoliday($date, $countryCode, $provinceCode);
    }

    /**
     * @throws NotImplementedException
     */
    public function isHoliday(\DateTime $date, string $countryCode, string $provinceCode = null): bool
    {
        if ($countryCode != 'DE') {
            throw new NotImplementedException('Only DE supported');
        }

        $province = $provinceCode ? $provinceCode : 'NATIONAL';
        $url = '?jahr=' . $date->format('Y') . '&nur_land=' . $province;

        $holidays = $this->cache->fetch($url);

        if (!$holidays) {
            $response = $this->client->get($url);
            $holidays = json_decode($response->getBody(), true);

            if (!$holidays) {
                throw new \LogicException("Could not get holidays for Year: ".$date->format('Y').", Country: $countryCode, Province: $provinceCode");
            }
            $this->cache->save($url, $holidays);
        }

        foreach ($holidays as $holiday) {
            if ($holiday['datum'] == $date->format('Y-m-d')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @throws NotImplementedException
     */
    public function addBusinessdays(int $days, \DateTime $date, string $countryCode, string $provinceCode = null): \DateTime
    {
        if ($countryCode != 'DE') {
            throw new NotImplementedException('Only DE supported');
        }

        for ($i = 0; $i < $days; $i++) {
            do {
                $date->modify('+1 day');
            } while (!$this->isBusinessDay($date, $countryCode, $provinceCode));
        }

        return $date;
    }

    /**
     * @throws NotImplementedException
     */
    public function subBusinessdays(int $days, \DateTime $date, string $countryCode, string $provinceCode = null): \DateTime
    {
        if ($countryCode != 'DE') {
            throw new NotImplementedException('Only DE supported');
        }

        for ($i = 0; $i < $days; $i++) {
            do {
                $date->modify('-1 day');
            } while (!$this->isBusinessDay($date, $countryCode, $provinceCode));
        }

        return $date;
    }
}
