<?php
namespace Infrastructure\Validation;

use Symfony\Component\Validator\ConstraintViolationListInterface;
class ValidationException extends \Exception
{

    public static function fromConstraintViolations(ConstraintViolationListInterface $validationErrors)
    {
        $messages = array();
        foreach ($validationErrors as $error) {
            $messages[] = $error->getMessage();
        }

        return new self(join("\n", $messages));
    }

}
