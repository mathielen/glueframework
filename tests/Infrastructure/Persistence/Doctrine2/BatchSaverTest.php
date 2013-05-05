<?php
namespace Infrastructure\Persistence\Doctrine2;

use Infrastructure\Persistence\TestModel;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\EntityManager;

use Doctrine\Common\Cache\ArrayCache;

use Doctrine\ORM\Tools\Setup;

class RepositoryTest extends \PHPUnit_Framework_TestCase
{

	public function test()
	{
		include(__DIR__ . '/../TestModel.php');

		$conn = array(
			'driver' => 'pdo_mysql',
			'user' => 'root',
			'password' => 'dev',
			'dbname' => 'test'
		);
		$config = Setup::createAnnotationMetadataConfiguration(
			array(__DIR__),
			true,
			'/tmp',
			new ArrayCache(),
			false
		);

		$entityManager = EntityManager::create($conn, $config);
		$batchSaver = new BatchSaver($entityManager,1000);

		$collection = new ArrayCollection();
		for($i=0; $i<10000; $i++) {
			$testModel = new TestModel(array('value'=>uniqid()));
			$collection->add($testModel);
		}

		$batchSaver->save($collection);
	}

}