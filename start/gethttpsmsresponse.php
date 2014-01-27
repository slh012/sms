<?php



$cleanData = $_GET;

$originator = $cleanData['originator'];
$destination = $cleanData['destination'];
$date = $cleanData['date'];
$time = $cleanData['time'];
$body = $cleanData['body'];
$rbid = $cleanData['rbid'];

if(strstr($body, 'STOP')){
    //opt out
}else{
    //create account
    //send account created notice
}

?>