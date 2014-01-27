<?php

class emailException extends Exception
{
	public function __construct($m, $c = 0)
	{
            print "\Email Error: ";
            parent::__construct($m,$c);
              
	}
}

?>