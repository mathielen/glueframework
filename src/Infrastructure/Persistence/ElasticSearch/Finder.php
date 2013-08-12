<?php
namespace Infrastructure\Persistence\ElasticSearch;

use Infrastructure\Search\Dto\Query;

class Finder implements \Infrastructure\Search\Finder
{

    /**
     * @var \Elastica_Search
     */
    private $elasticSearch;

    public function __construct(\Elastica_Search $elasticSearch)
    {
        $this->elasticSearch = $elasticSearch;
    }

    public function search(Query $query = null)
    {
        $esQuery = new Elastica_Query();

        if ($query) {
            if ($query->fields) {
                $esQuery->setFields($query->fields);
            }
            $esQuery->setLimit($query->limit);
        }

        return $this->searchRaw($esQuery);
    }

    private function searchRaw(Elastica_Query $esQuery)
    {
        $esResult = $this->elasticSearch->search($esQuery);
        $esResultsets = $esResult->getResults();
        $result = array();

        /* @var $esResultset Elastica_Result  */
        foreach ($esResultsets as $esResultset) {
            $result[] = $esResultset->getData();
        }

        return $result;
    }

}
