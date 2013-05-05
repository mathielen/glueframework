<?php
class Infrastructure_Test_DummyDtoFactory
{

	public static function factorDummyDto($dto)
	{
		$properties = get_object_vars($dto);

		foreach ($properties as $property=>$value) {
			if (!isset($dto->$property)) {
				$dto->$property = $property;
			}
		}

		return $dto;
	}

}