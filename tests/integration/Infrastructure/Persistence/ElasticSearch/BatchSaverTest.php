<?php
namespace Infrastructure\Persistence\ElasticSearch;

use Infrastructure\Persistence\TestModel;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @medium
 */
class BatchSaverTest extends \PHPUnit_Framework_TestCase
{

    public static function setUpBeforeClass()
    {
        if (!self::elasticaAvailable()) {
            self::markTestSkipped('ES server is not available');
        }
    }

    private static function elasticaAvailable()
    {
        return @fsockopen (\Elastica\Connection::DEFAULT_HOST, \Elastica\Connection::DEFAULT_PORT);
    }

    public function test()
    {
        $elasticaClient = new \Elastica\Client();
        $elasticaIndex = new \Elastica\Index($elasticaClient, 'testmodelidx');
        $elasticaType = new \Elastica\Type($elasticaIndex, 'testModelType');

        $batchSaver = new BatchSaver($elasticaType);

        $collection = new ArrayCollection();
        for ($i=0; $i<10000; $i++) {
            $testModel = new \Elastica\Document(uniqid(), array('value'=>uniqid()));
//			TestModel(array('value'=>uniqid()));
            $collection->add($testModel);
        }

        $batchSaver->save($collection);
    }

}
