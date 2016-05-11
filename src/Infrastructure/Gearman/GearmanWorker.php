<?php

namespace Infrastructure\Gearman;

use Monolog\Logger;

class GearmanWorker
{
    /**
     * @var \GearmanWorker
     */
    private $gearmanWorker;

    /**
     * @var Logger
     */
    private $logger;

    public function __construct($servers, Logger $logger = null)
    {
        $this->gearmanWorker = $this->factorGearmanWorker($servers);
        $this->logger = $logger;
    }

    private function factorGearmanWorker($servers)
    {
        $worker = new \GearmanWorker();
        $worker->addServers($servers);

        return $worker;
    }

    public function spawn()
    {
        if ($this->logger) {
            $this->logger->addDebug('Worker spawned');
        } else {
            echo "Worker spawned\n";
        }

        while ($this->gearmanWorker->work()) {
            if (GEARMAN_SUCCESS != $this->gearmanWorker->returnCode()) {
                if ($this->logger) {
                    $this->logger->addError('Worker failed: '.$this->gearmanWorker->error());
                } else {
                    echo 'Worker failed: '.$this->gearmanWorker->error()."\n";
                }
            }
        }
    }

    public function addFunction($functionName, $task)
    {
        $logger = $this->logger;

        return $this->gearmanWorker->addFunction($functionName, function () use ($task, $logger) {
            try {
                $args = func_get_args();
                $gearmanJob = $args[0];
                $result = call_user_func_array($task, $args);
            } catch (\Exception $e) {
                $logger->addError($e);
                echo $e;

                $result = GEARMAN_WORK_EXCEPTION;
                $gearmanJob->sendException($e->getMessage());

                syslog(LOG_ERR, $e);
                exit(255);
            }

            return $result;
        });
    }
}
