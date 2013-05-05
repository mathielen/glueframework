<?php
namespace Infrastructure\Gearman;

use Monolog\Handler\StreamHandler;

use Monolog\Handler\TestHandler;

use Monolog\Logger;

use Guzzle\Tests\Log\MonologLogAdapterTest;

class GearmanPipelineTest extends \PHPUnit_Framework_TestCase
{

	public function test()
	{
		global $logger;
        $logger = new Logger('GearmanPipelineTest');
        $handler = new StreamHandler('/tmp/log.log');
        $logger->pushHandler($handler);

        global $gearmanClient;
        $gearmanClient = new \GearmanClient();
        $gearmanClient->addServer('127.0.0.1', '4730');

		$pipeline = GearmanPipeline::create($gearmanClient, $logger)
			->pipeline('doException')
			->pipeline('doStuff')
			->run('some workload');
	}

}