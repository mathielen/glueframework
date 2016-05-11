<?php

namespace Infrastructure\Persistence\Rest;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Infrastructure\Persistence\Repository;
use JMS\Serializer\SerializerInterface;

class RestRepository implements \Infrastructure\Persistence\Repository
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    private $cls;

    public function __construct(Client $client, SerializerInterface $serializer, $cls)
    {
        $this->client = $client;
        $this->serializer = $serializer;
        $this->cls = $cls;
    }

    /**
     * returns specific connection object for this kind of repository.
     */
    public function getConnection()
    {
        return $this->client;
    }

    /**
     * persists given $object in repository.
     */
    public function save($object)
    {
        // TODO: Implement save() method.
    }

    /**
     * fetches an object identified by given $id.
     */
    public function get($id)
    {
        try {
            $response = $this->client->get($id);
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                return;
            }

            throw $e;
        }

        $responseJson = (string) $response->getBody();

        return $this->serializer->deserialize($responseJson, $this->cls, 'json');
    }

    /**
     * deletes object from repository identified by given $id.
     */
    public function delete($id)
    {
        // TODO: Implement delete() method.
    }
}
