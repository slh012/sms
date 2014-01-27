<?php

class ftException extends Exception
{
	function __construct($error_msg)
	{
		parent::__construct($error_msg);
	}
}
?>