<?php

namespace Infrastructure\Persistence\MongoDB;

use Doctrine\MongoDB\Connection;
use Infrastructure\Exception\ResourceNotFoundException;
use Mcs\Reporting\CoreBundle\Domain\Utils\AggregateQueryBuilder;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class MongoDBAggregation
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var LoggerInterface
     */
    private $logger;

    private $executedQueries = [];

    public function __construct(Connection $connection, LoggerInterface $logger = null)
    {
        $this->connection = $connection;
        $this->logger = $logger ? $logger : new NullLogger();
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
     *
     * @throws AggregationException
     * @throws ResourceNotFoundException
     */
    public function query($collectionName, array $query)
    {
        $collection = $this->getMongoClient()->selectDB('reporting_portal')->selectCollection($collectionName);

        try {
            $this->logger->debug('MongoDB aggregation query: '.json_encode($query));

            //aggregateCursor allows more than 16MB in resultset and might be faster
            $result = $collection->aggregateCursor($query);
        } catch (\Exception $e) {
            throw new AggregationException($query, $e);
        }

        $this->executedQueries[] = [
            'collection' => $collectionName,
            'query' => json_encode($query),
        ];

        return $result;
    }

    public function queryCounted($collectionName, AggregateQueryBuilder $queryBuilder)
    {
        $data = $this->query($collectionName, $queryBuilder->build());

        $collection = $this->getMongoClient()->selectDB('reporting_portal')->selectCollection($collectionName);
        $cntQuery = $queryBuilder
            ->append([
                '$group' => [
                    '_id' => '1',
                    'cnt' => ['$sum' => 1],
                ],
            ])
            ->sort(null)
            ->limit(null)
            ->build();

        try {
            $this->logger->debug('MongoDB aggregation query: '.json_encode($cntQuery));

            $cntResult = $collection->aggregateCursor($cntQuery);
            $cntResult->rewind();
            $cnt = $cntResult->current()['cnt'];
        } catch (\Exception $e) {
            throw new AggregationException($cntQuery, $e);
        }

        return [
            'data' => $data,
            'count' => $cnt,
        ];
    }
}
