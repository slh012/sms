<?php
if($lock != 'nolock'){
	$lock->release();
}
debug::output("\n\nEnding script ... {$_SERVER['SCRIPT_NAME']} : ".config::get('process_name')." : ".date('d/m/Y H:i:s')."\n\n");


	$contents=ob_get_contents();
	ob_end_clean();
	
	if (config::get('email_reports'))
	{
// 		$to_email = join(',', config::get('email_reports'));
// 		$operator = config::get('operator');
// 		mail($to_email, "$operator Flat File Processed", $contents);
	}
	
	//if (config::get('process_name'))	{}
	$status->data('log_message', $contents);	
	



	if(config::get('log_to_file')==true){
		$logf = new log_file('/output/');
		$logf->output_buffer($contents);		
	}
	
	if(config::get('output_to_screen')==true){
		echo $contents;
	}

if (config::get('process_name'))
{
// 	$type = config::get('flat_file_type');
// 	if (empty($type) || strpos($type, 'run_process')  !== false)
// 	{
// 		$type = config::get('product_type');
// 	}
// 	$status->data('type', $type);
 	$status->save();
 	unset($status);
}
?>