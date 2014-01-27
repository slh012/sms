<?php

chdir('C:/xampp/htdocs/');

include_once('init.php');

config::set('process_name', "Text Anywhere - Get SMS Inbound");

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
	$vars['number'] = '2';
	$vars['keyword'] = 'SOME KEYWORD';
	
	utils::recursiveCall('getSMSInbound', $vars, 1, $live);
	
	
}
catch(Exception $e)
{
	debug::output($e->getMessage());
	//$status->status(CRITICAL);
}

include('end.php');
?>