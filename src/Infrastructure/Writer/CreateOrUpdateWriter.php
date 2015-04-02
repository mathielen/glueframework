<?php
namespace Infrastructure\Writer;

use Infrastructure\Persistence\EntityInterface;
use Infrastructure\Persistence\Factory;
use Infrastructure\Persistence\IdentityResolverInterface;
use Infrastructure\Persistence\MergeableEntityInterface;
use Infrastructure\Persistence\Repository;

class CreateOrUpdateWriter
{

    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @var Factory
     */
    protected $factory;

    /**
     * @var IdentityResolverInterface
     */
    protected $idResolver;

    public function __construct(
        Repository $repository,
        Factory $factory=null,
        IdentityResolverInterface $idResolver=null)
    {
        $this->repository = $repository;
        $this->factory = $factory;
        $this->idResolver = $idResolver;
    }

    protected function getId($model)
    {
        if (!$this->idResolver && $model instanceof EntityInterface) {
            return $model->getId();
        } elseif ($this->idResolver) {
            return $this->idResolver->resolveId($model);
        }

        throw new \LogicException("Cannot resolve Id");
    }

    public function write($model)
    {
        $id = $this->getId($model);
        if ($id) {
            $entity = $this->update($this->repository->get($id), $model);
        } else {
            $entity = $this->create($model);
        }

        return $this->repository->save($entity);
    }

    protected function update(EntityInterface $existentEntity, $inputEntity)
    {
        if ($existentEntity instanceof MergeableEntityInterface) {
            $existentEntity->merge($inputEntity);
        }

        return $existentEntity;
    }

    protected function create($inputEntity)
    {
        if (!$this->factory) {
            return $inputEntity;
        }

        $entity = $this->factory->factor($inputEntity);

        return $entity;
    }

}
