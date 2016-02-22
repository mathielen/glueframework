<?php
namespace Infrastructure\Exception;

use Infrastructure\Search\NotFoundException;

class ResourceNotFoundException extends NotFoundException
{

    public function __construct($resourceName, $identifier, array $validIdentifiers = [])
    {
        parent::__construct(sprintf("Resource '%s' with id '%s' not found." . (empty($validIdentifiers) ? '' : ' Valid identifiers are: %s.'), $resourceName, $identifier, join(',', $validIdentifiers)));
    }

}
