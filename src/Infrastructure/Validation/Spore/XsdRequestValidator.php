<?php
namespace Infrastructure\Validation\Spore;

use Infrastructure\Validation\ValidationException;

class XsdRequestValidator implements RequestValidatorInterface
{

	private $xsdFilename;

	public function __construct($xsdFilename)
	{
		if (!is_readable($xsdFilename) || !is_file($xsdFilename)) {
			throw new \InvalidArgumentException("File $xsdFilename cannot be read!");
		}

		$this->xsdFilename = $xsdFilename;
	}

	private function isXml(\Slim\Http\Request $request)
	{
		return $request->getContentType() == 'application/xml';
	}

	/**
	 * (non-PHPdoc)
	 * @see \Infrastructure\Validation\Spore\RequestValidatorInterface::validate()
	 */
	public function validate(\Spore\ReST\Model\Request $request)
	{
		$request = $request->request();
		$requestBody = $request->originalInput;

		if (!$this->isXml($request)) {
			throw new ValidationException('Content Type is not application/xml. Unable to validate.');
		}
		if (empty($requestBody)) {
			return true;
		}

		libxml_use_internal_errors(true);
		$doc = new \DOMDocument();
		$doc->loadXML($requestBody);

		$errors = array();
		if (!$doc->schemaValidate($this->xsdFilename)) {
			$libXmlErrors = libxml_get_errors();
			foreach ($libXmlErrors as $libXmlError) {
				$message = $libXmlError->message;

				// Work-Around bug in libxml2 (aus: http://www.w3.org/TR/xmlschema-2/#decimal-lexical-representation):
				//
				// All ·minimally conforming· processors ·must·
				// support decimal numbers with a minimum of 18
				// decimal digits (i.e., with a ·totalDigits·
				// of 18). However, ·minimally conforming·
				// processors ·may· set an application-defined
				// limit on the maximum number of decimal
				// digits they are prepared to support, in
				// which case that application-defined maximum
				// number ·must· be clearly documented
				//
				if(! preg_match("!'[+-]?\d+(\.\d+)?' is not a valid value of the atomic type 'xs:decimal'!", $message)) {
					$errors[] = $message;
				}
			}
		}

		if(count($errors) == 0) {
			return true;
		} else {
			throw new ValidationException('XSD validation of content failed. Errors are: '.print_r($errors, true));
		}
	}


}