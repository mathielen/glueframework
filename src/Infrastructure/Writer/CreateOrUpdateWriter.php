<?php
namespace Infrastructure\Writer;

use Infrastructure\Exception\ResourceNotFoundException;
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
            return $this->idResolver->resolveByModel($model);
        }

        throw new \LogicException("Cannot resolve Id");
    }

    /**
     * @return object|bool
     */
    public function write($model)
    {
        $id = $this->getId($model);
        if ($id) {
            $entity = $this->repository->get($id);
            if (!$entity) {
                throw new ResourceNotFoundException('Unknown', $id);
            }

            if (!$this->update($entity, $model)) {
                //do not save, if no update
                return $entity;
            }
        } else {
            $entity = $this->create($model);
        }

        $this->repository->save($entity);

        return $entity;
    }

    protected function update(EntityInterface $existentEntity, $inputEntity)
    {
        if ($existentEntity instanceof MergeableEntityInterface && $inputEntity instanceof MergeableEntityInterface) {
            $existentEntity->merge($inputEntity);

            return true;
        }

        return false;
    }

    protected function create($model)
    {
        if (!$this->factory) {
            return $model;
        }

        $entity = $this->factory->factor($model);

        return $entity;
    }

}
