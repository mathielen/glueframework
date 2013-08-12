<?php
namespace Infrastructure\Exception;

class ResourceNotFoundException extends \Exception
{

    public function __construct($resourceName, $identifier)
    {
        parent::__construct(sprintf('Resource "%s" with id %s not found', $resourceName, $identifier));
    }

}
