<?php

namespace Infrastructure\Persistence\Salesforce;

use Ddeboer\Salesforce\MapperBundle\Mapper;
use Ddeboer\Salesforce\MapperBundle\Response\MappedRecordIterator;
use Infrastructure\Persistence\PersistenceException;
use Infrastructure\Persistence\Repository;

class SalesforceRepository implements \Infrastructure\Persistence\Repository
{
    /**
     * @var Mapper
     */
    protected $mapper;

    protected $entityName;
    protected $strategy;

    /**
     * @var MappedRecordIterator
     */
    protected $modelList = null;

    protected $deleteList = [];
    protected $saveList = [];

    public function __construct(
        Mapper $mapper,
        $entityName,
        $strategy = Repository::STRATEGY_EAGER)
    {
        $this->mapper = $mapper;
        $this->entityName = $entityName;
        $this->strategy = $strategy;
    }

    /**
     * (non-PHPdoc).
     *
     * @see \Infrastructure\Persistence\Repository::getConnection()
     */
    public function getConnection()
    {
        return $this->mapper;
    }

    /**
     * (non-PHPdoc).
     *
     * @see \Infrastructure\Persistence\Repository::save()
     */
    public function save($object)
    {
        if ($this->strategy === Repository::STRATEGY_EAGER) {
            $this->mapper->save($object);
        } else {
            $this->saveList[] = $object;
        }

        return $object;
    }

    /**
     * (non-PHPdoc).
     *
     * @see \Infrastructure\Persistence\Repository::get()
     */
    public function get($id)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('Cannot fetch record. Empty id supplied.');
        }

        if ($this->strategy === Repository::STRATEGY_EAGER) {
            return $this->mapper->findOneBy($this->entityName, ['id' => $id]);
        } else {
            if (!array_key_exists($id, $this->getAll())) {
                return;
            }

            return $this->getAll()[$id];
        }
    }

    /**
     * (non-PHPdoc).
     *
     * @see \Infrastructure\Persistence\Repository::delete()
     */
    public function delete($id)
    {
        if ($this->strategy === Repository::STRATEGY_EAGER) {
            return $this->mapper->delete($this->get($id));
        } else {
            $this->deleteList[] = $id;
        }
    }

    public function flush()
    {
        try {
            $deleteResults = $this->mapper->delete($this->deleteList);
            $saveResults = $this->mapper->save($this->saveList);

            $this->deleteList = [];
            $this->saveList = [];
        } catch (\Exception $e) {
            $e = new PersistenceException($this->saveList, $this->entityName, $e);

            $this->deleteList = [];
            $this->saveList = [];

            throw $e;
        }

        return ['deleteResults' => $deleteResults, 'saveResults' => $saveResults];
    }

    public function getAll()
    {
        if (is_null($this->modelList)) {
            foreach ($this->mapper->findAll($this->entityName, [], 0) as $model) {
                $this->modelList[$model->getId()] = $model;
            }
        }

        return $this->modelList;
    }
}
