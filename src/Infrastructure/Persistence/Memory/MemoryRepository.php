<?php
namespace Infrastructure\Persistence\Memory;

use Infrastructure\Persistence\PersistenceException;
use Infrastructure\Persistence\Repository;

class MemoryRepository implements Repository
{

	/**
	 * @var array
	 */
	private $storage = array();

	/**
	 * (non-PHPdoc)
	 * @see \Infrastructure\Persistence\Repository::getConnection()
	 */
	public function getConnection()
	{
		return null;
	}

	/**
	 * (non-PHPdoc)
	 * @see \Infrastructure\Persistence\Repository::save()
	 */
	public function save($object)
	{
		$this->storage[$object->getId()] = $object;
	}

	/**
	 * (non-PHPdoc)
	 * @see \Infrastructure\Persistence\Repository::get()
	 */
	public function get($id)
	{
		return $this->storage[$id];
	}

	/**
	 * (non-PHPdoc)
	 * @see \Infrastructure\Persistence\Repository::delete()
	 */
	public function delete($id)
	{
		unset($this->storage[$id]);
	}

}