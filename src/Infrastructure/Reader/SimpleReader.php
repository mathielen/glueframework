<?php

namespace Infrastructure\Reader;

use Infrastructure\Search\Dto\FilterInterface;
use Infrastructure\Search\Finder;
use Infrastructure\Search\Dto\Query;

class SimpleReader
{
    /**
     * @var Finder
     */
    protected $finder;

    public function __construct(Finder $finder)
    {
        $this->finder = $finder;
    }

    public function getAll()
    {
        return $this->finder->search();
    }

    public function get($id)
    {
        $queryParams = array('id' => $id);

        //security constraint
        //$this->securityAdvisor->applyClientIdToQueryArray($queryParams);

        $query = new Query($queryParams);
        $query->limit = 1;
        $objects = $this->finder->search($query);

        if (count($objects) == 0) {
            return;
        }

        return $objects[0];
    }

    public function readWith(FilterInterface $filter)
    {
        $query = $filter->toQuery();
        $data = $this->finder->search($query);

        return $data;
    }
}
