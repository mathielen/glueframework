<?php

namespace Infrastructure\Gearman;

use Monolog\Logger;

class GearmanPipelineTask
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var \GearmanClient
     */
    private $gearmanClient;

    /**
     * @var array
     */
    private $pipelineStack;

    private $serializedWorkload;
    private $lastTaskException;

    public function __construct(
        Logger $logger,
        \GearmanClient $gearmanClient,
        array $pipelineStack,
        $workload)
    {
        $this->logger = $logger;
        $this->gearmanClient = $gearmanClient;
        $this->pipelineStack = $pipelineStack;
        $this->serializedWorkload = serialize($workload);

        $this->gearmanClient->setCompleteCallback(array($this, 'completeCallback'));
        //$this->gearmanClient->setCreatedCallback(array($this, 'createdCallback'));
        //$this->gearmanClient->setDataCallback(array($this, 'dataCallback'));
        $this->gearmanClient->setExceptionCallback(array($this, 'exceptionCallback'));
        //$this->gearmanClient->setFailCallback(array($this, 'failCallback'));
        //$this->gearmanClient->setStatusCallback(array($this, 'statusCallback'));
        //$this->gearmanClient->setWarningCallback(array($this, 'warningCallback'));
        //$this->gearmanClient->setWorkloadCallback(array($this, 'workloadCallback'));
    }

    public function executeNextTask()
    {
        $nextTask = array_shift($this->pipelineStack);
        if (!$nextTask) {
            return false;
        }

        $this->logger->addInfo('Executing task: '.$nextTask);
        $this->lastTaskException = false;
        $this->gearmanClient->addTask($nextTask, $this->serializedWorkload);
        $this->gearmanClient->runTasks();

        $this->logger->addInfo('Task done: '.$nextTask);
        if (!$this->lastTaskException) {
            $this->executeNextTask();
        }
    }

    public function completeCallback(\GearmanTask $task)
    {
        //$this->log('completeCallback: ');
    }

    public function createdCallback()
    {
        //$this->log('createdCallback: ');
    }

    public function dataCallback()
    {
        //$this->log('dataCallback: ');
    }

    public function exceptionCallback(\GearmanTask $task)
    {
        //$this->log('exceptionCallback: ');

        $this->lastTaskException = true;
    }

    public function failCallback()
    {
        //$this->log('failCallback: ');
    }

    public function statusCallback()
    {
        //$this->log('statusCallback: ');
    }

    public function warningCallback()
    {
        //$this->log('warningCallback: ');
    }

    public function workloadCallback()
    {
        //$this->log('workloadCallback: ');
    }
}
