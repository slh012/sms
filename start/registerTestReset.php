<?php

chdir('../../sms/');

define( "SITE", "" );

include_once('init.inc.php');

$lock = 'nolock';

config::set('process_name', "Text Anywhere - Register Test Reset");

include_once 'start.inc.php';

$tags = array();//contains tags to reset

foreach($tags as $k => $v){

	$v = strtoupper(trim($v));
	
	$sql = "DELETE FROM log_recognised_tags WHERE tag_no =  '{$v}'";
	
	debug::output($sql);
	mysql::i()->query($sql);

	$sql = "UPDATE group_tags set action='UNUSED' where tag_no = '{$v}' ";
	
	debug::output($sql);
	mysql::i()->query($sql);
	
	$sql = "DELETE FROM tag_registered WHERE tag_no =  '{$v}' ";
	debug::output($sql);
	mysql::i()->query($sql);

}


$sql = "truncate log_check_lost_bag_email";
debug::output($sql);
mysql::i()->query($sql);

$sql = "truncate log_http_inbound";
debug::output($sql);
mysql::i()->query($sql);

$sql = "truncate log_http_delivery_status";
debug::output($sql);
mysql::i()->query($sql);

$sql = "truncate log_sms_suppression_list";
debug::output($sql);
mysql::i()->query($sql);

$sql = "truncate log_recognised_tags";
debug::output($sql);
mysql::i()->query($sql);

$sql = "truncate log_unrecognised_tags";
debug::output($sql);
mysql::i()->query($sql);

$sql = "truncate log_validation_errors";
debug::output($sql);
mysql::i()->query($sql);

$sql = "truncate log_api_send_errors";
debug::output($sql);
mysql::i()->query($sql);

include('end.inc.php');
?>






