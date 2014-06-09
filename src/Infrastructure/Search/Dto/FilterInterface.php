<?php
namespace Infrastructure\Search\Dto;

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
