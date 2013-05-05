<?php
namespace Infrastructure\Persistence\ElasticSearch;

use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\EntityManager;

class BatchSaver
{

	/**
	 * @var \Elastica_Type
	 */
	private $elasticaType;

	private $chunkSize;

	public function __construct(\Elastica_Type $elasticaType, $chunkSize = 500)
	{
		$this->elasticaType = $elasticaType;
		$this->chunkSize = $chunkSize;
	}

	public function save(\Traversable $list)
	{
		$this->chunkBegin();

		$i = 0;
		foreach ($list as $object) {
			$document = $object; //TODO transform??

			$this->elasticaType->addDocument($document);
			++$i;

			if ($i % $this->chunkSize == 0) {
				$this->chunkComplete();
				$this->chunkBegin();
			}
		}

		$this->chunkComplete();
	}

	private function chunkBegin()
	{
	}

	private function chunkComplete()
	{
		$this->elasticaType->getIndex()->refresh();
	}

}