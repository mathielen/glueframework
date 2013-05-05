<?php
namespace Infrastructure\Persistence\Doctrine2;

use Monolog\Logger;

use Doctrine\DBAL\Logging\SQLLogger;

class MonologSQLLogger implements SQLLogger
{

	/**
	 * @var Logger
	 */
	private $logger;

	public function __construct(Logger $logger)
	{
		$this->logger = $logger;
	}

	public function startQuery($sql, array $params = null, array $types = null)
	{
		$query = array('sql'=>$sql, 'params'=>$params, 'types'=>$types);
		$this->logger->addDebug('Doctrine2 query', $query);
	}

	public function stopQuery()
	{

	}

}