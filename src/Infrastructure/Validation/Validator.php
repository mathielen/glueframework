<?php
interface Infrastructure_Validation_Validator
{

	const INVALID_REQUIRED_BUT_MISSING = 1;
	const INVALID_FILEEXTENSION_DENIED = 2;
	const INVALID_CONTAINER = 3;
	const INVALID_MIMETYPE_DENIED = 4;
	const INVALID_REQUIRED_MIMETYPE_OR_FILE = 5;
	const INVALID_TYPE_WRONG = 6;
	const INVALID_FORMAT = 7;

	/**
	 * returns the fields that are invalid. Properties of given $object are considered fields.
	 * Values of returned array mean error codes, definied above.
	 *
	 * example:
	 *
	 * class MyClass {
	 *   public $field1;
	 * }
	 * $object = new MyClass();
	 * $result = $validator->validate($object);
	 *
	 * $result ==
	 *   array(
	 *     'field1' => 1
	 *   )
	 *
	 */
	public function validate($value);

}