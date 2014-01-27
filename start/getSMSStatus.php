<?php

chdir('../../sms/');

define( "SITE", "" );

include_once('init.inc.php');

$lock = 'nolock';

config::set('process_name', "Inbox - Get SMS Status");


include_once 'start.inc.php';

$input['clientMessageReference'] = $argv[2];

try{

	$smsstatus = new smsstatus($input);
	$smsstatus->database();

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