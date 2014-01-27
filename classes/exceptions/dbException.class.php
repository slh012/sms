<?php

class dbException extends Exception
{
	function __construct($error_msg,$code)
	{
		$error_msg=$code.': '.$error_msg.' - '.date('d/m/Y H:i:s')."\n";
		parent::__construct($error_msg,$code);

		// any clean up needed?
		// need to email people about error
	}
}
?>