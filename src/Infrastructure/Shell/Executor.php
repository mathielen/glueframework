<?php
namespace Infrastructure\Shell;

use Symfony\Component\Process\ProcessBuilder;

use Symfony\Component\Process\Process;

class Executor
{

	private $cmd;
	private $timeout;

	public function __construct($cmd, $timeout = 180)
	{
		if (!is_readable($cmd)) {
			throw new ExecutorException('Given command is not readable '.$cmd);
		}

		$this->cmd = $cmd;
		$this->timeout = $timeout;
	}

	public function run(array $arguments = array(), $stdOut = null)
	{
		array_unshift($arguments, $this->cmd);

		$process = ProcessBuilder::create($arguments)->getProcess();
		$process->addOutput($stdOut);
		$process->setTimeout($this->timeout);
		$process->run();

		$result = array(
			'commandLine' => $process->getCommandLine(),
			'exitCode' => $process->getExitCode(),
			'errorOutput' => $process->getErrorOutput(),
			'output' => $process->getOutput(),
		);

		if ($process->isSuccessful()) {
			return $result;
		}

		throw new ExecutorException('Process did not return successfully', $result);
	}

}