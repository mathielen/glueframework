<?php
namespace Infrastructure\Persistence\MongoDB;


use Doctrine\MongoDB\Connection;
use Infrastructure\Exception\ResourceNotFoundException;

class MongoDBAggregation
{

    /**
     * @var Connection
     */
    protected $connection;

    private $executedQueries = [];

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return \MongoClient
     */
    public function getMongoClient()
    {
        $client = $this->connection->getMongoClient();
        if (!$client) {
            $this->connection->connect();
            $client = $this->connection->getMongoClient();
        }

        return $client;
    }

    public function getExecutedQueries()
    {
        return $this->executedQueries;
    }

    public function query($collectionName, array $query)
    {
        //TODO check if DB and collection exists
        if (!in_array($collectionName, ['OrderPosition', 'Order', 'Stock'])) {
            throw new ResourceNotFoundException('Collection', $collectionName);
        }

        $collection = $this->getMongoClient()->selectDB('reporting_portal')->selectCollection($collectionName);

        $ms = microtime(true);
        $result = $collection->aggregate($query);
        $me = microtime(true);

        $this->executedQueries[] = [
            'collection'=>$collectionName,
            'query'=>json_encode($query),
            'duration'=>$me-$ms
        ];

        return $result['result'];
    }

}