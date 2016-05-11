<?php

namespace Infrastructure\Persistence;

use Doctrine\Common\Util\Debug;

class PersistenceException extends \Exception
{
    public static function fromEntity($entity, \Exception $previous, $additionalInfo = '')
    {
        return new self((array) Debug::export($entity, 1), get_class($entity), $previous, $additionalInfo);
    }

    public function __construct(array $data, $entityCls, \Exception $previous, $additionalInfo = '')
    {
        $message = "Exception in persisting entity of class $entityCls\nwith contents:\n".print_r(Debug::export($data, 2), true)."\n$additionalInfo";

        parent::__construct($message, 0, $previous);
    }
}
