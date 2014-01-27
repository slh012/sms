<?php

chdir('C:/xampp/htdocs/tag/');

include_once('init.php');

config::set('process_name', "Text Anywhere - Send SMS");

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

include('start.php');

try{

	$vars['returnCSVString'] = false;
	$vars['clientBillingReference'] = '0';
	$vars['clientMessageReference'] = '1'; // create new reference using primary key from database
	$vars['originator'] = ORIGINATOR;
	$vars['destinations'] = '';
	$vars['body'] = 'This is a test message';
	$vars['validity'] = '1';
	$vars['characterSetID'] = 2;
	$vars['replyMethodID'] = 2;
	$vars['replyData'] = config::get('ta_replyemail');	
	$vars['StatusNotificationUrl'] = '';
	
	
	utils::recursiveCall('sendSMS', $vars, 1, $live);
	
	
}
catch(Exception $e)
{
	debug::output($e->getMessage());
	//$status->status(CRITICAL);
}

include('end.php');
?>