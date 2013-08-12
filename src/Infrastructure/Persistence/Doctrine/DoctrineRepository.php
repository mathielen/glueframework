<?php
namespace Infrastructure\Persistence\Doctrine;

use Infrastructure\Persistence\PersistenceException;
use Infrastructure\Persistence\Repository;

class DoctrineRepository implements Repository
{

    /**
     * @var \Doctrine_Connection
     */
    private $connection;

    private $dtoName;

    public function __construct(
        \Doctrine_Connection $connection,
        $dtoName)
    {
        $this->connection = $connection;
        $this->dtoName = $dtoName;

        if (@!class_exists($dtoName)) {
            throw new PersistenceException('Class for supplied dtoName does not exist: '.$dtoName);
        }
    }

    /**
     * (non-PHPdoc)
     * @see \Infrastructure\Persistence\Repository::getConnection()
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * (non-PHPdoc)
     * @see \Infrastructure\Persistence\Repository::save()
     */
    public function save($object)
    {
        if (!($object instanceof \Doctrine_Record)) {
            throw new PersistenceException('Cannot persist object. It doesnt extends Doctrine_Record.');
        }
        if (@get_class($object) != $this->dtoName) {
            throw new PersistenceException('Wrong class supplied. Expected classname: '.$this->dtoName. ' was '.@get_class($object));
        }

        $object->save();
    }

    /**
     * (non-PHPdoc)
     * @see \Infrastructure\Persistence\Repository::get()
     */
    public function get($id)
    {
        if (empty($id)) {
            throw new InvalidArgumentException('Cannot fetch record. Empty id supplied.');
        }

        return Doctrine::getTable($this->dtoName)->find($id);
    }

    /**
     * (non-PHPdoc)
     * @see \Infrastructure\Persistence\Repository::delete()
     */
    public function delete($id)
    {
        if (empty($id)) {
            throw new InvalidArgumentException('Cannot fetch record. Empty id supplied.');
        }

        $dto = $this->get($id);
        if (!$dto) {
            throw new InvalidArgumentException('Cannot delete record. It doesnt exist.');
        }

        $dto->delete();
    }

}
