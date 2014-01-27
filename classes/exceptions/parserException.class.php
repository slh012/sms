<?php

class parserException extends Exception
{
	function __construct($error_msg)
	{
		$error_msg.=' - '.date('d/m/Y H:i:s')."\n";
		parent::__construct($error_msg);

		// need to email people about error
	}
}

?>