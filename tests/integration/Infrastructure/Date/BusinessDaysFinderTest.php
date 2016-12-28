<?php
namespace Infrastructure\Date;

use Doctrine\Common\Cache\ArrayCache;
use GuzzleHttp\Client;

class BusinessDaysFinderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var BusinessDaysFinder
     */
    private $sut;

    protected function setUp()
    {
        $guzzleClient = new Client(['base_uri' => 'http://feiertage.jarmedia.de/api/']);

        $this->sut = new BusinessDaysFinder($guzzleClient, new ArrayCache());
    }

    public function testIsBusinessDay()
    {
        $this->assertTrue($this->sut->isBusinessDay(new \DateTime('2016-12-28'), 'DE'));        //wednesday
        $this->assertFalse($this->sut->isBusinessDay(new \DateTime('2016-12-17'), 'DE'));       //saturday
        $this->assertFalse($this->sut->isBusinessDay(new \DateTime('2016-12-18'), 'DE'));       //sunday
        $this->assertFalse($this->sut->isBusinessDay(new \DateTime('2016-01-01'), 'DE'));       //friday, but holiday
        $this->assertTrue($this->sut->isBusinessDay(new \DateTime('2016-05-26'), 'DE'));       //thursday, no holiday in DE
        $this->assertFalse($this->sut->isBusinessDay(new \DateTime('2016-05-26'), 'DE', 'NW'));       //thursday, but holiday in NW
    }

    public function testGetNextBusinessday()
    {
        //normal day
        $wednesday = new \DateTime('2016-12-28');
        $actual = $this->sut->getNextBusinessday($wednesday, 'DE');
        $this->assertEquals('2016-12-29', $actual->format('Y-m-d'));

        //friday to monday
        $friday = new \DateTime('2016-12-09');
        $actual = $this->sut->getNextBusinessday($friday, 'DE');
        $this->assertEquals('2016-12-12', $actual->format('Y-m-d'));

        //Fronleichnam (skip next day)
        $fronleichnam = new \DateTime('2016-05-25');
        $actual = $this->sut->getNextBusinessday($fronleichnam, 'DE', 'NW');
        $this->assertEquals('2016-05-27', $actual->format('Y-m-d'));
    }

}