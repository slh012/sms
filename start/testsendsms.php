<?php

chdir('../../sms/');

define( "SITE", "" );

include_once('init.inc.php');

$lock = 'nolock';

config::set('process_name', "Text Anywhere - TEST Send SMS");

include_once 'start.inc.php';

if($_GET['argv1']){
	$arg = $_GET['argv1'];
}else{
	$arg = $argv[1];
}

switch($arg){
	case "test":
		$live = false;
		break;
	case "live":
		$live = true;
		break;
	default;
		$live = false;
}



try{

	
	
	$vars['clientMessageReference'] = '999'; // create new reference using primary key from database
	
	$vars['destinations'] = '';
	$vars['body'] = 'This is a test message';
	
	
	utils::recursiveCall('sendSMS', $vars, 1, $live);
	
	
}
catch(Exception $e)
{
	debug::output($e->getMessage());
	//$status->status(CRITICAL);
}

include('end.inc.php');
?>