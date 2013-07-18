<?php
namespace Infrastructure\Validation;

class UtilsTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider getIsIntegerData
     */
	public function testIsInteger($input, $expectedResult)
	{
        $actualResult = Utils::isInteger($input);
        $this->assertEquals($expectedResult, $actualResult);
	}

	public function getIsIntegerData()
	{
	    return array(
            array(23, true),
            array("23", true),
            array(23.5, false),
            array("23.5", false),
            array(null, false),
            array("", false)
	    );
	}

	/**
	 * @dataProvider getIsMysqlDateData
	 */
	public function testIsMysqlDate($input, $expectedResult)
	{
	    $actualResult = Utils::isMysqlDate($input);
	    $this->assertEquals($expectedResult, $actualResult);
	}

	public function getIsMysqlDateData()
	{
	    return array(
            array("2013-01-01", true),
            array("2013-1-1", true),
	        array("9999-50-50", true),
            array("2013/01/01", false),
            array("5", false),
            array(null, false),
            array("", false)
	    );
	}

}