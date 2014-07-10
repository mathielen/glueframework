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

    public function create(array $data)
    {
        $entityClass = $this->entityClass;
        $entity = new $entityClass($data);

        return $this->persist($entity);
    }

    public function save($entity)
    {
        return $this->persist($entity);
    }

    private function persist($entity)
    {
        $this->repository->save($entity);

        return $entity;
    }

    public function delete($id)
    {
        $this->repository->delete($id);
    }

}
