<?php
namespace Infrastructure\Validation\Spore;

use Infrastructure\Validation\ValidationException;

class RequestValidator implements RequestValidatorInterface
{

    /**
     * contenttype => validator
     *
     * @var ValidatorInterface[]
     */
    private $validators;

    public function __construct(array $validators)
    {
        $this->validators = $validators;
    }

    /**
     * (non-PHPdoc)
     * @see \Infrastructure\Validation\Spore\RequestValidatorInterface::validate()
     */
    public function validate(\Spore\ReST\Model\Request $request)
    {
        $request = $request->request();
        $requestBody = $request->originalInput;
        $contentType = $request->getContentType();

        if (empty($requestBody)) {
            return true;
        }

        if (!array_key_exists($contentType, $this->validators)) {
            throw new ValidationException("No validator found for Content Type $contentType. Unable to validate.");
        }

        return $this->validators[$contentType]->validate($requestBody);
    }

}
