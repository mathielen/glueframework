<?php
namespace Infrastructure\Writer;

use Infrastructure\Persistence\PersistenceException;
use Infrastructure\Persistence\Repository;

class SimpleWriter
{

    /**
     * @var Repository
     */
    protected $repository;

    protected $entityClass;

    public function __construct(
        Repository $repository,
        $entityClass)
    {
        $this->repository = $repository;
        $this->entityClass = $entityClass;
    }

    protected function translate(&$data)
    {
    }

    public function create($data)
    {
        $this->translate($data);

        $entityClass = $this->entityClass;

        try {
            $entity = new $entityClass($data);

            return $this->persist($entity);
        } catch (\Exception $e) {
            throw new PersistenceException($data, $this->entityClass, $e);
        }
    }

    public function save($entity, $data=array())
    {
        $this->translate($data);

        try {
            if (!empty($data)) {
                $entity->applyData($data);
            }

            return $this->persist($entity);
        } catch (\Exception $e) {
            throw PersistenceException::fromEntity($entity, $e, 'New data: '.print_r($data, true));
        }
    }

    protected function persist($entity)
    {
        $this->repository->save($entity);

        return $entity;
    }

    public function delete($id)
    {
        $this->repository->delete($id);
    }

}
