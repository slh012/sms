<?php
chdir('../');



include_once('init.inc.php');

config::set('process_name', "AirDB - Process CSV");


include_once 'start.inc.php';

try
{
    
    $file_location = '/airdb.csv';
    debug::output("Processing file: $file_location");

    
    $csv = new csvparser($file_location);
		
	
	mysql::i()->query("truncate airports_backup");
	mysql::i()->query("insert into airports_backup select * from airports");
	mysql::i()->query("truncate airports");
     
	
	validator::loadDictionaries(array('AirDB'));
	$iata = array('BKN', 'AAS', 'BOO');
	$air = array();
    foreach($csv as $row)
    {
	
		//print_r($row);
		validator::validateInput($row);
		$cleanData = validator::getCleanData();
		$errors = validator::getErrors();		
		
		if(!empty($errors)){
			print_r($errors);
			exit();
		}
		
		$cleanData['Airport'] = str_replace(']','',str_replace('[','',$cleanData['Airport']));
		
		$sql = "INSERT INTO airports (ICAO,IATA,place,state,airport,country) VALUES ('{$cleanData['ICAO']}','{$cleanData['IATA']}','{$cleanData['Place']}','{$cleanData['State']}','{$cleanData['Airport']}','{$cleanData['Country']}') ";
		mysql::i()->query($sql);   
		
		//if(in_array($cleanData['IATA'], $iata))continue;
		
		//print_r($cleanData);
		 
		//print_r($cleanData);
		/*
		if(in_array($cleanData['IATA'], $air)){
			if($air[$cleanData['IATA']]['Country'] == 'USA'){
				$air[$cleanData['IATA']] = $cleanData;
				
			}
		}else{
			$air[$cleanData['IATA']] = $cleanData;
			
		}*/
		
		
		
		
    }
	/*
print_r($air);
    foreach($air as $iata){
			foreach($iata as $key => $value){
			echo "$key => $value\n";
				$sql = "INSERT INTO airports (ICAO,IATA,place,state,airport,country) VALUES ('{$cleanData['ICAO']}','{$cleanData['IATA']}','{$cleanData['Place']}','{$cleanData['State']}','{$cleanData['Airport']}','{$cleanData['Country']}') ";
				mysql::i()->query($sql);   
			}
		}
*/
include_once 'end.inc.php';
}
catch(downloadException $e)
{
	debug::output($e->getMessage());
	$status->status(CRITICAL);
}
catch(parserException $e)
{
	debug::output($e->getMessage());
	$status->status(CRITICAL);
}
catch(inserterException $e)
{
	debug::output($e->getMessage());
	$status->status(CRITICAL);
}
catch(Exception $e)
{
	debug::output($e->getMessage());
	$status->status(CRITICAL);
}
?>