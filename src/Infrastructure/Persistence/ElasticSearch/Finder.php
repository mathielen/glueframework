<?php
namespace Infrastructure\Persistence\ElasticSearch;

use Infrastructure\Search\Dto\Query;
use Elastica\Facet\Terms;
use Elastica\Search;
use Infrastructure\Search\Resultset;
use Elastica\Query\Term;

class Finder implements \Infrastructure\Search\Finder
{

    /**
     * @var Search
     */
    private $elasticSearch;

    public function __construct(Search $elasticSearch)
    {
        $this->elasticSearch = $elasticSearch;
    }

    public function search(Query $query = null)
    {
        $esQuery = new \Elastica\Query();
        $esQuery->setLimit($query->limit);

        if ($query) {
            if (!empty($query->fields)) {
                $elasticaQuery  = new \Elastica\Query\Bool();

                foreach ($query->fields as $key=>$value) {
                    if (is_array($value)) {
                        $elasticaQuery->addMust($termQuery1 = new \Elastica\Query\Terms($key, $value));
                    } else {
                        $elasticaQuery->addMust($termQuery1 = new Term(array($key => $value)));
                    }
                }

                $esQuery->setQuery($elasticaQuery);
            }

            if ($query->facets) {
                foreach ($query->facets as $name=>$field) {
                    $facet = new Terms($name);
                    $facet->setAllTerms(true);
                    $facet->setField($field);
                   // $facet->setOrder('order');
                    $facet->setSize(20);
                    $esQuery->addFacet($facet);
                }
            }
        }

        return $this->searchRaw($esQuery);
    }

    private function searchRaw(\Elastica\Query $esQuery)
    {
        $esResult = $this->elasticSearch->search($esQuery);
        $esResultsets = $esResult->getResults();
        $resultset = new Resultset();

        /* @var $esResultset \Elastica\Result  */
        foreach ($esResultsets as $esResultset) {
            $data = $esResultset->getData();
            $data['id'] = $esResultset->getId();

            $resultset->add($data);
        }

        $resultset->setMetadata('facets', $esResult->getFacets());
        $resultset->setMetadata('count', $esResult->getTotalHits());

        return $resultset;
    }

}
