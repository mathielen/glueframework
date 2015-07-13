<?php
namespace Infrastructure\Validation;

use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationException extends \Exception
{

    /**
     * @return \Infrastructure\Validation\ValidationException
     */
    public static function fromConstraintViolations(ConstraintViolationListInterface $validationErrors)
    {
        $messages = array();

        /** @var ConstraintViolation $error */
        foreach ($validationErrors as $error) {
            $messages[$error->getPropertyPath()] = $error->getMessage();
        }

        return new self(json_encode($messages)); //TODO how to transport the messages stack to flattenexception? see exceptioncontroller
    }

    /**
     * @return \Infrastructure\Validation\ValidationException
     */
    public static function fromField($fieldname, $message)
    {
        return new self(json_encode(array($fieldname=>$message)));
    }

}
