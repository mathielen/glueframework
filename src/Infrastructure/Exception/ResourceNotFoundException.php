<?php
namespace Infrastructure\Exception;

use Infrastructure\Search\NotFoundException;

class ResourceNotFoundException extends NotFoundException
{

    public function __construct($resourceName, $identifier)
    {
        parent::__construct(sprintf("Resource '%s' with id '%s' not found", $resourceName, $identifier));
    }

}
