<?php
namespace Infrastructure\Messaging;

class UnauthorizedStatus extends BadRequestStatus
{

    public $statusCode = 401;
    public $statusText = 'Unauthorized';

}
