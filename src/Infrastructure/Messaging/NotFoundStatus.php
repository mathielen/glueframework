<?php
namespace Infrastructure\Messaging;

class NotFoundStatus extends BadRequestStatus
{

    public $statusCode = 404;
    public $statusText = 'Not Found';

}
