<?php
namespace Infrastructure\Search\Dto;

use Infrastructure\Search\Dto\Query;

interface FilterInterface
{

    /**
     * @return array
     */
    public function toArray();

    /**
     * @return Query
     */
    public function toQuery();

}
