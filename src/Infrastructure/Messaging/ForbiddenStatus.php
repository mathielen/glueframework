<?php

namespace Infrastructure\Messaging;

class ForbiddenStatus extends BadRequestStatus
{
    public $statusCode = 403;
    public $statusText = 'Forbidden';
}
