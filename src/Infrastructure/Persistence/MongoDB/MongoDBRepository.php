<?php
namespace Infrastructure\Persistence\MongoDB;

use Infrastructure\Exception\ResourceNotFoundException;
use Doctrine\ODM\MongoDB\DocumentManager;

class MongoDBRepository implements \Infrastructure\Persistence\Repository
{

    /**
     * @var DocumentManager
     */
    private $documentManager;

    private $entityName;

    public function __construct(
        DocumentManager $documentManager,
        $entityName)
    {
        $this->documentManager = $documentManager;
        $this->entityName = $entityName;
    }

    /**
     * (non-PHPdoc)
     * @see \Infrastructure\Persistence\Repository::getConnection()
     */
    public function getConnection()
    {
        return $this->documentManager;
    }

    /**
     * (non-PHPdoc)
     * @see \Infrastructure\Persistence\Repository::save()
     */
    public function save($object)
    {
        $this->documentManager->persist($object);
        $this->documentManager->flush(); //flush everything pending (so cascaded objects get flushed, too)
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

        return $this->documentManager->find($this->entityName, $id);
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
        if (!$object) {
            throw new ResourceNotFoundException($this->entityName, $id);
        }

        $this->documentManager->remove($object);
        $this->documentManager->flush();
    }

}
