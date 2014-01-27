<?php

class debug
{
	private function __construct()
	{

	}

	private function __clone()
	{

	}

	static public function output($str)
	{
		//fwrite(STDOUT, "$str\n\n");
		if(isset($_SERVER['SSH_TTY'])){
		  echo "$str\n";
		}else{
		  echo "hi<p>$str</p>";
		}
              
		//shell_debug($str);

	}

	static public function backtrace()
	{
		debug_print_backtrace();
		echo "\n";
	}
}
?>