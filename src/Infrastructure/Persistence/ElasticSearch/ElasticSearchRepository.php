<?php
namespace Infrastructure\Persistence\ElasticSearch;

class ElasticSearchRepository implements \Infrastructure\Persistence\Repository
{

	/**
	 * @var \Elastica_Type
	 */
	private $elasticaType;

	/**
	 * @var DocumentFactory
	 */
	private $documentFactory;

	public function __construct(
		\Elastica_Type $elasticaType,
		DocumentFactory $documentFactory)
	{
		$this->elasticaType = $elasticaType;
		$this->documentFactory = $documentFactory;
	}

	/**
	 * (non-PHPdoc)
	 * @see Infrastructure_Persistence_Repository::getConnection()
	 * @return Elastica_Type
	 */
	public function getConnection()
	{
		return $this->elasticaType;
	}

	/**
	 * (non-PHPdoc)
	 * @see Infrastructure_Persistence_Repository::save()
	 */
	public function save($object)
	{
		$document = $this->documentFactory->toElasticSearchDocument($object);
		$result = $this->elasticaType->addDocument($document);
		$this->elasticaType->getIndex()->refresh();

		return $result;
	}

	/**
	 * (non-PHPdoc)
	 * @see Infrastructure_Persistence_Repository::get()
	 */
	public function get($id)
	{
		if (empty($id)) {
			throw new \InvalidArgumentException('Cannot fetch record. Empty id supplied.');
		}

		try {
			$document = $this->elasticaType->getDocument($id);
		} catch (\Elastica_Exception_NotFound $e) {
			return null;
		}

		$object = $this->documentFactory->fromElasticSearchDocument($document);
		return $object;
	}

	/**
	 * (non-PHPdoc)
	 * @see Infrastructure_Persistence_Repository::delete()
	 */
	public function delete($id)
	{
		if (empty($id)) {
			throw new \InvalidArgumentException('Cannot delete record. Empty id supplied.');
		}

		$result = $this->elasticaType->deleteById($id);
		$this->elasticaType->getIndex()->refresh();

		return $result;
	}

}