<?php
namespace Infrastructure\Persistence\MongoDB;

use Infrastructure\Search\SearchException;

class AggregationException extends SearchException
{

    protected $query;

    public function __construct($query, \Exception $previous = null)
    {
        $this->query = json_encode($query);
        parent::__construct('Error in Aggregate query: '.$previous->getMessage()."\n".$this->query, $previous);
    }

}
