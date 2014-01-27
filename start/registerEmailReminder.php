<?php

chdir('../');

include_once('init.inc.php');

$lock = 'nolock';

config::set('process_name', "Inbox - Register Email Address Reminder");


include_once 'start.inc.php';



try{

	$sql = "SELECT * FROM customer WHERE email='' AND approved=1 AND telephone<>'' AND username<>'' \n";
	$q = mysql::i()->query($sql);
	while($r = mysql::i()->fetch_array($q)){
		$send['destinations'] = utils::formatPhoneNumber($r['telephone']); 
		$send['body'] = "Account {$r['username']} does not have a valid email address to recieve lost bag reports. Please log on to example.com to update your details. Text PIN STOP to cancel";
		utils::recursiveCall('sendSMS', $send, 1);
	}

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