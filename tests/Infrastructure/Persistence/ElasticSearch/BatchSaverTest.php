<?php
namespace Infrastructure\Persistence\ElasticSearch;

use Infrastructure\Persistence\TestModel;

use Doctrine\Common\Collections\ArrayCollection;

class RepositoryTest extends \PHPUnit_Framework_TestCase
{

    public function test()
    {
        include(__DIR__ . '/../TestModel.php');

        $elasticaClient = new \Elastica_Client();
        $elasticaIndex = new \Elastica_Index($elasticaClient, 'testmodelidx');
        $elasticaType = new \Elastica_Type($elasticaIndex, 'testModelType');

        $batchSaver = new BatchSaver($elasticaType);

        $collection = new ArrayCollection();
        for ($i=0; $i<10000; $i++) {
            $testModel = new \Elastica_Document(uniqid(), array('value'=>uniqid()));
//			TestModel(array('value'=>uniqid()));
            $collection->add($testModel);
        }

        $batchSaver->save($collection);
    }

}
