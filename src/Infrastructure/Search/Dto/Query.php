<?php
namespace Infrastructure\Search\Dto;

use Infrastructure\Search\SearchException;

class Query
{

    const SORT_ASCENDING = 'asc';
    const SORT_DESCENDING = 'desc';

    public $fields;
    public $sortField;
    public $sortDirection = self::SORT_ASCENDING; //self::SORT_ASCENDING or self::SORT_DESCENDING

    public $offset;
    public $limit;
    public $paginated = false;

    public $useResultCache = true;
    public $resultCacheId;

    public function __construct($fields = array())
    {
        $this->fields = $fields;
    }

    public function __wakeup()
    {
        if (!in_array($this->sortDirection, array(self::SORT_ASCENDING, self::SORT_DESCENDING))) {
            throw new SearchException('Sortdirection can only be desc or asc. Given: '.$this->sortDirection);
        }
    }

}
