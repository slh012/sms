<?php

chdir('../');



include_once('init.inc.php');

config::set('process_name', "Inbox - Check For Lost Bags");


include_once 'start.inc.php';

include_once('../public_html/system/library/mail.php');

try{

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

    $checkLostBagEmail = new checkLostBagEmail($argv, $live);

	
}
catch(filewriteException $e)
{
	debug::output($e->getMessage());
	
}
catch(fileException $e)
{
	debug::output($e->getMessage());
	
}
catch(emailException $e)
{
	debug::output($e->getMessage());
	
}
catch(dbException $e)
{
	debug::output($e->getMessage());
	
}
catch(Exception $e)
{
	debug::output($e->getMessage());
	//$status->status(CRITICAL);
}


include('end.inc.php');
?>