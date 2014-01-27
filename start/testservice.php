<?php

chdir('../');

include_once('init.inc.php');

config::set('process_name', "Text Anywhere - Test Service");


include_once 'start.inc.php';

//config::set('script_folder',SITE);

//set_include_path(SET_INCLUDE_PATH.PATH.'api'.PATH.'api'.DIR.'textanywhere'.PATH);

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

//include('start.php');

try{

	$vars['returnCSVString'] = false;
	echo getcwd();
	
	utils::recursiveCall('testService', $vars, 1, $live);
	
	
}
catch(Exception $e)
{
	debug::output($e->getMessage());
	$status->status(CRITICAL);
}

include('end.inc.php');
?>