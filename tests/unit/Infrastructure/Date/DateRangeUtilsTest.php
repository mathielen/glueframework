<?php
namespace Infrastructure\Date;

class DateRangeUtilsTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider getExplodeDateRangeData
     */
    public function testExplodeDateRange($shortcut, $expected)
    {
        $this->assertEquals($expected, DateRangeUtils::explodeDateRange($shortcut));
    }

    public function getExplodeDateRangeData()
    {
        return [
            ['2014Y', ['from' => '2014-01-01', 'to' => '2014-12-31']],
            ['2014HY1', ['from' => '2014-01-01', 'to' => '2014-06-30']],
            ['2014Q3', ['from' => '2014-07-01', 'to' => '2014-09-30']],
            ['2014M2', ['from' => '2014-02-01', 'to' => '2014-02-28']],
            ['2014CW17', ['from' => '2014-04-21', 'to' => '2014-04-27']],
            ['2014-01-05', ['from' => '2014-01-05', 'to' => '2014-01-05']],

            ['2011Y>2013Y', ['from' => '2011-01-01', 'to' => '2013-12-31']],
            ['2011HY2>2013HY2', ['from' => '2011-07-01', 'to' => '2013-12-31']],
            ['2011Q1>2012Q2', ['from' => '2011-01-01', 'to' => '2012-06-30']],
            ['2011M1>2012M2', ['from' => '2011-01-01', 'to' => '2012-02-29']],
            ['2011CW1>2014CW17', ['from' => '2011-01-03', 'to' => '2014-04-27']],
            ['2011-01-01>2012-01-01', ['from' => '2011-01-01', 'to' => '2012-01-01']],

            ['>2013-01-01', ['to' => '2013-01-01']],
            ['2012-01-01>', ['from' => '2012-01-01']]
        ];
    }

    /**
     * @dataProvider getExplodeDateRangeInvalidData
     * @expectedException \InvalidArgumentException
     */
    public function testExplodeDateRangeInvalid($shortcut)
    {
        DateRangeUtils::explodeDateRange($shortcut);
    }

    public function getExplodeDateRangeInvalidData()
    {
        return [
            ['2014'],
            ['2014Y123'],
            ['2014HY'],
            ['2014HY3'],
            ['2014Q'],
            ['2014Q5'],
            ['2014M'],
            ['2014M13'],
            ['2014CW'],
            ['2014CW99'],
            ['8888-88-88>9999-99-99'],
            ['>2013-01-01>']
        ];
    }

}
