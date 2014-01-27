<?php

chdir('../');

include_once('init.inc.php');

config::set('process_name', "Text Anywhere - Send Premium SMS");

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

	$log = new log_database();
	
	$send['rbid'] = '879762236';
	$send['body'] = 'This is a test message';	
	$send['clientMessageReference'] = $log->logOutboundSMS($send);
	
	utils::recursiveCall('sendPremiumSMS', $send, 1, $live);
	
	
}
catch(Exception $e)
{
	debug::output($e->getMessage());
	//$status->status(CRITICAL);
}

include('end.inc.php');
?>