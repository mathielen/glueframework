<?php

namespace Infrastructure\Writer;

use Infrastructure\Exception\ResourceNotFoundException;
use Infrastructure\Persistence\EntityInterface;
use Infrastructure\Persistence\Factory;
use Infrastructure\Persistence\IdentityResolverInterface;
use Infrastructure\Persistence\MergeableInterface;
use Infrastructure\Persistence\Repository;
use Psr\Log\LoggerInterface;

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

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(
        Repository $repository,
        Factory $factory = null,
        IdentityResolverInterface $idResolver = null,
        LoggerInterface $logger = null)
    {
        $this->repository = $repository;
        $this->factory = $factory;
        $this->idResolver = $idResolver;
        $this->logger = $logger;
    }

    /**
     * @return Repository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    protected function getId($model)
    {
        if (!$this->idResolver && $model instanceof EntityInterface) {
            return $model->getId();
        } elseif ($this->idResolver) {
            return $this->idResolver->resolveByModel($model);
        }

        throw new \LogicException('Cannot resolve Id');
    }

    /**
     * @return object|bool
     */
    public function write($model)
    {
        $id = $this->getId($model);

        if ($id) {
            $this->logger ? $this->logger->debug("Resolved id from model: '$id'", ['model' => $model]) : null;

            $entity = $this->repository->get($id);
            if (!$entity) {
                throw new ResourceNotFoundException('Unknown', $id);
            }

            if (!$this->update($entity, $model)) {
                $this->logger ? $this->logger->notice('Entity was not updated. Entity did not implemenet MergeableInterface or merge implementation returned false.', ['model' => $model]) : null;

                //do not save, if no update
                return $entity;
            }
        } else {
            $entity = $this->create($model);

            $this->logger ? $this->logger->debug('Created new entity from model', ['model' => $model, 'entity' => $entity]) : null;
        }

        $this->repository->save($entity);

        return $entity;
    }

    protected function update($existentEntity, $inputEntity)
    {
        if ($existentEntity instanceof MergeableInterface && $inputEntity instanceof MergeableInterface) {
            return $existentEntity->merge($inputEntity);
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
