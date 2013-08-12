<?php
namespace Infrastructure\Persistence\ElasticSearch\IdGenerator;

use Infrastructure\Persistence\IdGenerator;

class Incremental implements IdGenerator
{

    /**
     * @var \Elastica_Search
     */
    private $elasticSearch;

    private $baseId;

    public function __construct(
        \Elastica_Search $elasticSearch,
        $baseId=0)
    {
        $this->elasticSearch = $elasticSearch;
        $this->baseId = $baseId;
    }

    public function generate()
    {
        $currentId = $this->currentId();

        //increment
        $currentId++;

        return $currentId;
    }

    private function currentId()
    {
        $esQuery = new \Elastica_Query();
        $esQuery->setFields(array('id'));
        $esQuery->setLimit(1);
        $esQuery->setSort(array(array('id' => array('order' => Infrastructure_Search_Dto_Query::SORT_DESCENDING))));
        $esResult = $this->elasticSearch->search($esQuery);

        if (count($esResult) == 0) {
            return $this->baseId;
        } else {
            $ds = $esResult->current();

            return $ds->id;
        }
    }

}
