<?php
namespace Infrastructure\Persistence\ElasticSearch;

use Infrastructure\Search\Dto\Query;

class Finder implements \Infrastructure\Search\Finder
{

    /**
     * @var Elastica\Search
     */
    private $elasticSearch;

    public function __construct(\Elastica\Search $elasticSearch)
    {
        $this->elasticSearch = $elasticSearch;
    }

    public function search(Query $query = null)
    {
        $esQuery = new \Elastica\Query();

        if ($query) {
        	$elasticaQuery  = new \Elastica\Query\Bool();
        	
        	foreach ($query->fields as $key=>$value) {
        		$elasticaQuery->addMust($termQuery1 = new \Elastica\Query\Term(array($key => $value)));
        	}
        	
            $esQuery->setLimit($query->limit);
            $esQuery->setQuery($elasticaQuery);
        }

        return $this->searchRaw($esQuery);
    }

    private function searchRaw(\Elastica\Query $esQuery)
    {
        $esResult = $this->elasticSearch->search($esQuery);
        $esResultsets = $esResult->getResults();
        $result = array();

        /* @var $esResultset Elastica\Result  */
        foreach ($esResultsets as $esResultset) {
        	$resultSet = $esResultset->getData();
        	$resultSet['id'] = $esResultset->getId();
        	
            $result[] = $resultSet;
        }

        return $result;
    }

}
