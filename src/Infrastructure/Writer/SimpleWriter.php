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

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function save($object)
    {
        $this->repository->save($object);
    }

    public function delete($id)
    {
        $this->repository->delete($id);
    }

}
