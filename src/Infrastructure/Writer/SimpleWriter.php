<?php
namespace Infrastructure\Writer;

use Infrastructure\Search\Dto\FilterInterface;
use Infrastructure\Search\Finder;
use Infrastructure\Search\Dto\Query;
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
        $entity = new $entityClass($data);

        return $this->persist($entity);
    }

    public function save($entity, $data=array())
    {
        $this->translate($data);

        $entity->applyData($data);

        return $this->persist($entity);
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
