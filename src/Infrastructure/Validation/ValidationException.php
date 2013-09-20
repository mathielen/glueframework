<?php
namespace Infrastructure\Validation;

use Symfony\Component\Validator\ConstraintViolationListInterface;
class ValidationException extends \Exception
{

    /**
     * @return \Infrastructure\Validation\ValidationException
     */
    public static function fromConstraintViolations(ConstraintViolationListInterface $validationErrors)
    {
        $messages = array();
        foreach ($validationErrors as $error) {
            $messages[$error->getPropertyPath()] = 'INVALID'; //TODO how to differenciate?
        }

        return new self(serialize($messages)); //TODO how to transport the messages stack to flattenexception? see exceptioncontroller
    }

    /**
     * @return \Infrastructure\Validation\ValidationException
     */
    public static function fromField($fieldname, $message)
    {
        return new self(serialize(array($fieldname=>$message)));
    }

}
