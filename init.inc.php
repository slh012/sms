<?php

//print "<pre>";

if ( ! defined( "PATH_SEPARATOR" ) ) {
	if ( strpos( $_ENV[ "OS" ], "Win" ) !== false )
		define( "PATH_SEPARATOR", ";" );
	else define( "PATH_SEPARATOR", ":" );
}

set_include_path('classes/log'.PATH_SEPARATOR.'classes/api/textanywhere'.PATH_SEPARATOR.'classes/api'.PATH_SEPARATOR.'classes/parsers'.PATH_SEPARATOR.'classes/textanywhere'.PATH_SEPARATOR.'classes/database'.PATH_SEPARATOR.'classes/library'.PATH_SEPARATOR.'classes/exceptions'.PATH_SEPARATOR.'classes/extract'.PATH_SEPARATOR.'classes/datadictionary'.PATH_SEPARATOR.'classes'.PATH_SEPARATOR);




function __autoload($class)
{       
        if($fh=@fopen("{$class}.inc.php",'r',true))
	{
		fclose($fh);
		require("{$class}.inc.php");
	}
	else
	{
		require("{$class}.class.php");
	}
}



try
{
	config::setup();
	mysql::i(config::get('mysql_conn'));
	
	
}
catch(dbException $e)
{
	debug::output($e->getMessage());
	// any errors in this block then should probably exit
	exit;
}
catch(Exception $e)
{
	debug::output($e->getMessage());
	// any errors in this block then should probably exit
	exit;
}
?>