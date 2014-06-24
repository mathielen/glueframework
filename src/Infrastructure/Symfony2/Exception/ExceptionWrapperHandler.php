<?php
namespace Infrastructure\Symfony2\Exception;

use FOS\RestBundle\View\ExceptionWrapperHandlerInterface;

class ExceptionWrapperHandler implements ExceptionWrapperHandlerInterface
{

    public function wrap($data)
    {
        return new ExceptionWrapper($data);
    }

}