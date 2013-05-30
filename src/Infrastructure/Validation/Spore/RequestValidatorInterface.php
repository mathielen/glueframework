<?php
namespace Infrastructure\Validation\Spore;

interface RequestValidatorInterface
{

	public function validate(\Spore\ReST\Model\Request $request);

}