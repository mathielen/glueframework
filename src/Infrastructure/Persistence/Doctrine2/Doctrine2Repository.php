<?php
namespace Infrastructure\Persistence\Doctrine2;

use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\EntityManager;

class Doctrine2Repository implements \Infrastructure\Persistence\Repository
{

	/**
	 * @var EntityManager
	 */
	private $entityManager;

	private $entityName;

	public function __construct(
		EntityManager $entityManager,
		$entityName)
	{
		$this->entityManager = $entityManager;
		$this->entityName = $entityName;
	}

	/**
	 * (non-PHPdoc)
	 * @see \Infrastructure\Persistence\Repository::getConnection()
	 */
	public function getConnection()
	{
		return $this->entityManager;
	}

	/**
	 * (non-PHPdoc)
	 * @see \Infrastructure\Persistence\Repository::save()
	 */
	public function save($object)
	{
		$this->entityManager->persist($object);
		$this->entityManager->flush($object);
	}

	/**
	 * (non-PHPdoc)
	 * @see \Infrastructure\Persistence\Repository::get()
	 */
	public function get($id)
	{
		if (empty($id)) {
			throw new \InvalidArgumentException('Cannot fetch record. Empty id supplied.');
		}

		return $this->entityManager->find($this->entityName, $id);
	}

	/**
	 * (non-PHPdoc)
	 * @see \Infrastructure\Persistence\Repository::delete()
	 */
	public function delete($id)
	{
		if (empty($id)) {
			throw new \InvalidArgumentException('Cannot delete record. Empty id supplied.');
		}

		$object = $this->get($id);
		$this->entityManager->remove($object);
		$this->entityManager->flush();
	}

}