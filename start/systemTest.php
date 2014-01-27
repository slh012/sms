<?php
print "<pre>";
chdir('../../sms/');

define( "SITE", "" );

include_once('init.inc.php');

$lock = 'nolock';

config::set('process_name', "Text Anywhere - HTTP SMS Inbound");

$dev = 1;

include_once 'start.inc.php';



$testData[0] = array(
	"Originator"=>"",
	"Destination"=>"",
	"Body"=>""
);

foreach($testData as $posn=>$data){

	$data['Date'] = date('d/m/Y', time());
	$data['Time'] = date('H:i', time());
	
	new register($data);
	
}
include('end.inc.php');
?>