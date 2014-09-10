<?php
namespace Infrastructure\Persistence\MongoDB;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use TestDocuments\TestModel;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\Common\Cache\ArrayCache;

/**
 * @medium
 */
class BatchSaverTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var DocumentManager
     */
    protected static $dm = null;

    public static function setUpBeforeClass()
    {
        $connection = new Connection();
        $config = new Configuration();
        $config->setProxyDir('/tmp');
        $config->setProxyNamespace('Proxies');
        $config->setHydratorDir('/tmp');
        $config->setHydratorNamespace('Hydrators');
        $config->setDefaultDB('doctrine_odm');

        $config->setMetadataDriverImpl(AnnotationDriver::create(__DIR__."/../../../../metadata/TestDocuments"));

        AnnotationDriver::registerAnnotationClasses();

        self::$dm = DocumentManager::create($connection, $config);

        $qb = self::$dm->createQueryBuilder('TestDocuments\TestModel');
        $qb->remove()
            ->getQuery()
            ->execute();
        self::$dm->flush();
    }

    public static function tearDownAfterClass()
    {
        self::$dm = NULL;
    }

    public function test()
    {
        //this document should be always accessible
        $document = new TestModel(array('value'=>123));
        self::$dm->persist($document);
        self::$dm->flush();

        $allwaysDoc = self::$dm
            ->getRepository('TestDocuments\TestModel')
            ->find($document->getId());

        $batchSaver = new BatchSaver(self::$dm, 1000);

        $collection = new ArrayCollection();
        for ($i=0; $i<5000; $i++) {
            $testModel = new TestModel(array('value'=>uniqid()));
            $collection->add($testModel);
        }

        $batchSaver->save($collection);

        $entities = self::$dm
            ->getRepository('TestDocuments\TestModel')
            ->findAll();

        //import worked
        $this->assertEquals(5001, count($entities));
    }

}
