<?php
/*
 * Created: 26/9/2012
 * Change Log:
 * 
 *  Author: Sean Hardaker (seanhardaker@gmail.com)
 *
 *  Needs further validation checks to be implimented
 *  
 */

	
 
class smsstatus extends sms{

	private $clientMessageReference;

	public function __construct($input){
		parent::__construct($input);
		$this->clientMessageReference = $input['clientMessageReference'];
	}
	
	public function database(){
	
		$sql = "SELECT clientMessageReference FROM log_outbound_sms WHERE received=false AND status='LIVE' and clientMessageReference = {$this->clientMessageReference}  \n"; //
		$q = mysql::i()->query($sql);
		$r = mysql::i()->fetch_array($q);
			
		$sql = "SELECT * FROM log_http_delivery_status WHERE clientmessagereference = {$this->clientMessageReference}  order by http_delivery_status_id asc \n";
		$q = mysql::i()->query($sql);	
		
		while($r = mysql::i()->fetch_array($q)){
		
			$deliveryStatus = $r['received'];
			debug::output("{$r['messagestatusgroup']} - {$this->clientMessageReference}");		
			
		}
		
		//$deliveryStatus=false; //test
		
		if($deliveryStatus==true){
			$sql = "UPDATE log_outbound_sms SET received=true WHERE clientMessageReference = {$this->clientMessageReference} \n";
			mysql::i()->query($sql);
			$fault = false;
		}else{
			$fault = true;
		}		
		//$fault = true; //test
		
		return $fault;
	}//database
	
	public function api(){
	
		//unfinished
	
		echo $sql = "SELECT clientMessageReference FROM log_outbound_sms WHERE received=false AND status='LIVE'  \n"; //and clientMessageReference = 191 
		$q = mysql::i()->query($sql);
		while($r = mysql::i()->fetch_array($q)){
			$class = utils::recursiveCall('getSMSStatus', $r, 1);
			//$class->
			sleep(1);
			
			
			
			//unset($class);
		}
	}

}