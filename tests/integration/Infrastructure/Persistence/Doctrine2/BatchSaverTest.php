<?php
namespace Infrastructure\Persistence\Doctrine2;

use Doctrine\ORM\Tools\SchemaTool;
use TestEntities\TestModel;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\EntityManager;

use Doctrine\ORM\Tools\Setup;

/**
 * @medium
 */
class BatchSaverTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var EntityManagerInterface
     */
    protected static $em = null;

    public static function setUpBeforeClass()
    {
        $isDevMode = true;
        $config = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/../../../../metadata/TestEntities"), $isDevMode, null, null, false);

        $connectionOptions = array('driver' => 'pdo_sqlite', 'memory' => true);

        // obtaining the entity manager
        self::$em =  EntityManager::create($connectionOptions, $config);

        $schemaTool = new SchemaTool(self::$em);

        $cmf = self::$em->getMetadataFactory();
        $classes = $cmf->getAllMetadata();

        $schemaTool->dropDatabase();
        $schemaTool->createSchema($classes);
    }

    public static function tearDownAfterClass()
    {
        self::$em = NULL;
    }

    public function test()
    {
        $batchSaver = new BatchSaver(self::$em, 1000);

        $collection = new ArrayCollection();
        for ($i=0; $i<10000; $i++) {
            $testModel = new TestModel(array('value'=>uniqid()));
            $collection->add($testModel);
        }

        $batchSaver->save($collection);

        $entities = self::$em
            ->getRepository('TestEntities\TestModel')
            ->findAll();

        //import worked
        $this->assertEquals(10000, count($entities));
    }

}
