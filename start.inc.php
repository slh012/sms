<?php
if($lock != 'nolock'){
	$lock = new scriptLock;
	$lock->release_all();
}


if($argv[1]=='dev' || $_GET['dev']=='true' || $dev==1){	
	config::set('log_to_file', false);
	config::set('output_to_screen', true);
	define(LIVE,0);
	
	config::set('dev',true);
}else{
	config::set('log_to_file', true);
	config::set('output_to_screen', false);
	config::set('dev',false);
	
	define(LIVE,1);
}

if($argv[2]=='false' || $_GET['live']=='false'){	
	
}else{	
	
}

if (config::get('process_name'))
{
 	$status=status::instance();
 	$status->status(GOOD);
 	$status->data('process_name',config::get('process_name'));
}
	
ob_start();

debug::output("\n\nStarting script ... {$_SERVER['SCRIPT_NAME']} : ".config::get('process_name')." : ".date('d/m/Y H:i:s')."\n\n");
?>