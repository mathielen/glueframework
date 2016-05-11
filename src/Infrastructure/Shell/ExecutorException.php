<?php

namespace Infrastructure\Shell;

class ExecutorException extends \Exception
{
    private $result;

    public function __construct($message, $result = array())
    {
        parent::__construct($message."\nResult:\n".print_r($result, true));

        $this->result = $result;
    }

    public function getExecutorResult()
    {
        return $this->result;
    }
}
