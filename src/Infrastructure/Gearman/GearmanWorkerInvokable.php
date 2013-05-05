<?php
namespace Infrastructure\Gearman;

interface GearmanWorkerInvokable
{

	public function invokeByGearmanWorker($name, $workload);

}