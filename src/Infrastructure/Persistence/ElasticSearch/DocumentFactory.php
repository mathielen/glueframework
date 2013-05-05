<?php
namespace Infrastructure\Persistence\ElasticSearch;

use Infrastructure\Serialization\Utils;

use Infrastructure\Persistence\Entity;

class DocumentFactory
{

	/**
	 * @return \Elastica_Document
	 */
	public function toElasticSearchDocument(Entity $entity)
	{
		$data = get_object_vars($entity);
		$document = new \Elastica_Document($entity->id, $data);
		return $document;
	}

	public function fromElasticSearchDocument(\Elastica_Document $document)
	{
		$stdClassObject = new \stdClass();
		foreach ($document->getData() as $key=>$value) {
			$stdClassObject->$key = $value;
		}

		$object = Utils::recursiveCastToObject($stdClassObject, $document->getType());
		return $object;
	}

}