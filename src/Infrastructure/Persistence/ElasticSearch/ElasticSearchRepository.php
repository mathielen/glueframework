<?php

namespace Infrastructure\Persistence\ElasticSearch;

use Elastica\Document;
use JMS\Serializer\SerializerInterface;

class ElasticSearchRepository implements \Infrastructure\Persistence\Repository
{
    /**
     * @var \Elastica\Type
     */
    private $elasticaType;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    private $cls;

    public function __construct(
        \Elastica\Type $elasticaType,
        SerializerInterface $serializer,
        $cls)
    {
        $this->elasticaType = $elasticaType;
        $this->serializer = $serializer;
        $this->cls = $cls;
    }

    /**
     * (non-PHPdoc).
     *
     * @see Infrastructure_Persistence_Repository::getConnection()
     *
     * @return Elastica_Type
     */
    public function getConnection()
    {
        return $this->elasticaType;
    }

    /**
     * (non-PHPdoc).
     *
     * @see Infrastructure_Persistence_Repository::save()
     */
    public function save($object)
    {
        $document = $this->documentFactory->toElasticSearchDocument($object);
        $result = $this->elasticaType->addDocument($document);
        $this->elasticaType->getIndex()->refresh();

        return $result;
    }

    /**
     * (non-PHPdoc).
     *
     * @see Infrastructure_Persistence_Repository::get()
     */
    public function get($id)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('Cannot fetch record. Empty id supplied.');
        }

        try {
            $document = $this->elasticaType->getDocument($id);
        } catch (\Elastica\Exception\NotFoundException $e) {
            return;
        }

        $entity = $this->deserialize($document);

        return $entity;
    }

    protected function deserialize(Document $document)
    {
        $entity = $this->serializer->deserialize(json_encode($document->getData()), $this->cls, 'json');

        return $entity;
    }

    /**
     * (non-PHPdoc).
     *
     * @see Infrastructure_Persistence_Repository::delete()
     */
    public function delete($id)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('Cannot delete record. Empty id supplied.');
        }

        $result = $this->elasticaType->deleteById($id);
        $this->elasticaType->getIndex()->refresh();

        return $result;
    }
}
