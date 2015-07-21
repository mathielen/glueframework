<?php
namespace Infrastructure\Persistence\ElasticSearch;

use FOS\ElasticaBundle\Doctrine\AbstractElasticaToModelTransformer;
use Doctrine\ORM\Query;
use FOS\ElasticaBundle\Transformer\ElasticaToModelTransformerInterface;

class ElasticaToArrayTransformer implements ElasticaToModelTransformerInterface
{

    /**
     * Transforms an array of elastica objects into an array of
     * model objects fetched from the doctrine repository.
     *
     * @param array $elasticaObjects array of elastica objects
     *
     * @return array of model objects
     **/
    public function transform(array $elasticaObjects)
    {
        return array_map(function(\Elastica\Result $e) { return $e->getData(); }, $elasticaObjects);
    }

    public function hybridTransform(array $elasticaObjects)
    {
        return $elasticaObjects;
    }

    /**
     * Returns the object class used by the transformer.
     *
     * @return string
     */
    public function getObjectClass()
    {
        return null;
    }

    /**
     * Returns the identifier field from the options.
     *
     * @return string the identifier field
     */
    public function getIdentifierField()
    {
        return 'id';
    }
}
