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

    /**
     * @return \MongoCommandCursor
     * @throws AggregationException
     * @throws ResourceNotFoundException
     */
    public function query($collectionName, array $query)
    {
        $collection = $this->getMongoClient()->selectDB('reporting_portal')->selectCollection($collectionName);

        $ms = microtime(true);
        try {
            //aggregateCursor allows more than 16MB in resultset and might be faster
            $result = $collection->aggregateCursor($query);
        } catch (\Exception $e) {
            throw new AggregationException($query, $e);
        }
        $me = microtime(true);

        $this->executedQueries[] = [
            'collection'=>$collectionName,
            'query'=>json_encode($query),
            'duration'=>$me-$ms
        ];

        return $result;
    }

}
