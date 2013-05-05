<?php
class Infrastructure_Validation_NullValidator implements Infrastructure_Validation_Validator
{

	/**
	 * (non-PHPdoc)
	 * @see Infrastructure_Validation_Validator::validate()
	 */
	public function validate($value)
	{
		return array();
	}

}