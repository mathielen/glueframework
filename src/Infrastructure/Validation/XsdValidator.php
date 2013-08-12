<?php
namespace Infrastructure\Validation;

use Infrastructure\Validation\ValidationException;

class XsdValidator implements ValidatorInterface
{

    private $xsdFilename;

    public function __construct($xsdFilename)
    {
        if (!is_readable($xsdFilename) || !is_file($xsdFilename)) {
            throw new \InvalidArgumentException("File $xsdFilename cannot be read!");
        }

        $this->xsdFilename = $xsdFilename;
    }

    /**
     * (non-PHPdoc)
     * @see \Infrastructure\Validation\ValidatorInterface::validate()
     */
    public function validate($input)
    {
        if (empty($input)) {
            throw new ValidationException('Input is empty');
        }

        libxml_use_internal_errors(true);
        libxml_clear_errors();
        $doc = new \DOMDocument();
        $doc->loadXML($input);

        $errors = array();
        if (!$doc->schemaValidate($this->xsdFilename)) {
            $libXmlErrors = libxml_get_errors();
            foreach ($libXmlErrors as $libXmlError) {
                $message = $libXmlError->message;
                $errors[] = $message;
            }
        }
        libxml_clear_errors();

        if (count($errors) == 0) {
            return true;
        } else {
            throw new ValidationException('XSD validation of content failed. Errors are: '.print_r($errors, true));
        }
    }

}
