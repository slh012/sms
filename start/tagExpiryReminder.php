<?php

chdir('../');

include_once('init.inc.php');

$lock = 'nolock';

config::set('process_name', "Inbox - Tag Expiry Reminder");


include_once 'start.inc.php';



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
    
    $tagExpiryReminder = new tagExpiryReminder();
	
    $tagExpiryReminder->fetchTags();
    
   
    
    
    
	
}
catch(emailException $e)
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