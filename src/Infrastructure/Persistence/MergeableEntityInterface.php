<?php
namespace Infrastructure\Persistence;

interface MergeableEntityInterface extends EntityInterface
{

    public function merge(MergeableEntityInterface $entity);
    public function getMergeValues();

}
