<?php
namespace Infrastructure\Gearman;

use Monolog\Logger;

class GearmanPipeline
{

    /**
     * @var \GearmanClient
     */
    private $gearmanClient;

    /**
     * @var Logger
     */
    private $logger;

    private $pipelineStack = array();

    public function __construct(
        \GearmanClient $gearmanClient,
        Logger $logger)
    {
        $this->gearmanClient = $gearmanClient;
        $this->logger = $logger;
    }

    /**
     * creates new pipeline
     *
     * @param  \GearmanClient                          $gearmanClient
     * @param  Logger                                  $logger
     * @return \Infrastructure\Gearman\GearmanPipeline
     */
    public static function create(
        \GearmanClient $gearmanClient,
        Logger $logger)
    {
        return new self($gearmanClient, $logger);
    }

    /**
     * adds a task to the pipeline
     *
     * @param  string                                  $task
     * @return \Infrastructure\Gearman\GearmanPipeline
     */
    public function pipeline($task)
    {
        $this->pipelineStack[] = $task;

        return $this;
    }

    /**
     * executes the pipeline with a specific workload. workload will be serialized, thus can be
     * any type.
     *
     * @param unknown $workload
     */
    public function run($workload)
    {
        $gearmanPipelineChunk = new GearmanPipelineTask($this->logger, $this->gearmanClient, $this->pipelineStack, $workload);
        $gearmanPipelineChunk->executeNextTask();
    }

}
