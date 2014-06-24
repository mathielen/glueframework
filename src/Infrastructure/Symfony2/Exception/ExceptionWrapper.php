<?php
namespace Infrastructure\Symfony2\Exception;

/**
 * Wraps an exception into the FOSRest exception format.
 */
class ExceptionWrapper
{
    private $code;
    private $message;
    private $errors;

    /**
     * @param array $data
     */
    public function __construct($data)
    {
        //TODO only debug?
        $this->errors = $data['exception']->getTrace();

        $this->code = $data['status_code'];
        $this->message = $data['message'];

        if (isset($data['errors'])) {
            $this->errors = $data['errors'];
        }
    }
}
