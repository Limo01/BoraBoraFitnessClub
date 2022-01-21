<?php
	function isNameValid($name){
		return preg_match("/^[a-zA-Z-' àèìòùáéíóú]*$/", $name) && strlen($name)>0;
	}

	function isEmailValid($email){
		return filter_var($email, FILTER_VALIDATE_EMAIL);
	}

	/*
	* Valid format: yyyy-mm-dd
	* Controlla che il formato sia valido e che la data sia compresa (rispetto ad oggi) tra 110 anni fa e 16 anni fa
	*/
	function isDateValid($date, $admin=false, $format = 'Y-m-d'){
    	$d = DateTime::createFromFormat($format, $date);

    	if($admin)
    		return $d && $d->format($format) === $date;

    	return $d && $d->format($format) === $date && $d->format($format) < date('Y-m-d', strtotime('-16 years')) && $d->format($format) > date('Y-m-d', strtotime('-110 years'));
	}

	function isPhoneNumberValid($phone_number){
		return preg_match("/^\s*(?:\+?(\d{1,3}))?[-. (]*(\d{3})[-. )]*(\d{3})[-. ]*(\d{4})(?: *x(\d+))?\s*$/", $phone_number);
	}
?>