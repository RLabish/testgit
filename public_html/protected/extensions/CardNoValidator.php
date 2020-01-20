<?php

class CardNoValidator extends CStringValidator {
	public $error_fmt = "Неверный формат номера карты (12 символов 0..9,A..F)";
	
	protected function validateAttribute($object,$attribute) {
		$object->$attribute = strtoupper($object->$attribute);
		$value = $object->$attribute;
		if ($value != "") {
			if (strlen($value) != 12)
				$this->addError($object,$attribute,$this->error_fmt);
			else {				
				$arr = str_split($value);
				foreach ($arr as $c) {
					if ((($c >= '0') && ($c <= '9')) || (($c >= 'A') && ($c <= 'F')))
					;
					else {
						$this->addError($object,$attribute,$this->error_fmt);
						break;
					}
				};
			}
		}
		parent::validateAttribute($object,$attribute);
	}

}