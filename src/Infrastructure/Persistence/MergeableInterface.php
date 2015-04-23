<?php
namespace Infrastructure\Persistence;

interface MergeableInterface
{

    /**
     * @param  MergeableInterface $entity
     * @return boolean
     */
    public function merge(MergeableInterface $entity);

    /**
     * @return array
     */
    public function getMergeValues();

}
