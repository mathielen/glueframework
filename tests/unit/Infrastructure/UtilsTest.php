<?php
namespace Infrastructure;

class UtilsTest extends \PHPUnit_Framework_TestCase
{

    public function testJoinWithKey()
    {
        $this->assertEquals('key=value;otherkey=othervalue', Utils::joinWithKey(';', '=', array('key'=>'value', 'otherkey'=>'othervalue')));
    }

    public function testIsCli()
    {
        $this->assertTrue(Utils::isCli());
    }

    public function testWhoAmI()
    {
        $this->assertEquals('root', Utils::whoAmI());
    }

}
