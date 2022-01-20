<?php
	function isNameValid($name){
		return preg_match("/^[a-zA-Z-' àèìòùáéíóú]*$/", $name) && strlen($name)>0;
	}

	function isEmailValid($email){
		return filter_var($email, FILTER_VALIDATE_EMAIL);
	}

	/*
	* Valid format: yyyy-mm-dd
	*/
	function isDateValid($date, $format = 'Y-m-d'){
    	$d = DateTime::createFromFormat($format, $date);

    	return $d && $d->format($format) === $date && $d->format($format) < date('Y-m-d');
	}

	function isPhoneNumberValid($phone_number){
		return preg_match("/^\s*(?:\+?(\d{1,3}))?[-. (]*(\d{3})[-. )]*(\d{3})[-. ]*(\d{4})(?: *x(\d+))?\s*$/", $phone_number);
	}

	echo isDateValid("2030-02-01");
?>