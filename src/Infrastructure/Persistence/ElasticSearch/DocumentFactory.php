<?php

namespace Infrastructure\Persistence\ElasticSearch;

use Infrastructure\Serialization\Utils;
use Sylius\Component\Product\Model\Product;

class DocumentFactory
{
    /**
     * @return \Elastica\Document
     */
    public function toElasticSearchDocument(EntityInterface $entity)
    {
        $data = get_object_vars($entity);
        $document = new \Elastica\Document($entity->id, $data);

        return $document;
    }

    public function fromElasticSearchDocument(\Elastica\Document $document)
    {
        $stdClassObject = new \stdClass();
        foreach ($document->getData() as $key => $value) {
            $stdClassObject->$key = $value;
        }

        $object = Utils::recursiveCastToObject($stdClassObject, Product::class); //$document->getType());

        return $object;
    }
}
