<?php

class log_database{
    
	private $ip;
	
	private $status;
	
    public function __construct()
    {
		$this->ip = utils::getIP();
		
		
		
		$this->status = (LIVE) ?  'LIVE' : 'DEV';
		
		
    }
    
	public function logAPISendErrors($vars, $code, $description){
	//print_r($vars);
		$sql = "INSERT INTO log_api_send_errors (destinations, clientMessageReference, code, description, datetime, ip, status) VALUES ('{$vars['destinations']}', '{$vars['clientMessageReference']}', '{$code}', '{$description}', NOW() ,INET_ATON('{$this->ip}'), '{$this->status}' ) \n";
		mysql::i()->query($sql);
	}//logAPISendErrors
	
	public function logValidationErrors($send, $errors){
		
	
		$error = utils::captureString($errors);
	
		$sql = "INSERT INTO log_validation_errors (destinations, error, datetime, ip) VALUES ('{$send['destinations']}', '{$error}', NOW() ,INET_ATON('{$this->ip}') ) \n";
		mysql::i()->query($sql);
		
		$send['destinations'] = utils::formatPhoneNumber($send['originator']);
		$send['body'] = 'There was an error. Please check and try again or call '.config::get('lost_bag_number');	
		
		utils::recursiveCall('sendSMS', $send, 1);
		
	}//logValidationErrors
	
	
	public function logSMSStatus($array){
		$q = mysql::i()->query('show columns from log_sms_status');
                
		while($r=mysql::i()->fetch_object($q))
		{
			$fields[]=$r->Field;
		}
                
		foreach($array as $key => $value){
			if(in_array($key,$fields)){
				$cols.="$key,";
				$values.="'".mysql::i()->escape_string($value)."',";				
			}
		}

		$cols=substr($cols,0,-1);
		$values=substr($values,0,-1);
                
		$sql = "INSERT INTO log_sms_status ({$cols}) VALUES ({$values}) \n";
		mysql::i()->query($sql);
		
	}//logSMSStatus
	
	public function addToSuppressionList($fields){
		$sql = "INSERT INTO log_sms_suppression_list (originator, destination, body, rbid, date, time, datetime, ip) VALUES ('{$fields['originator']}', '{$fields['destination']}', '{$fields['body']}', '{$fields['rbid']}', '{$fields['date']}', '{$fields['time']}', NOW() ,INET_ATON('{$this->ip}')) \n";		
		mysql::i()->query($sql);
	}
	
	public function logOutboundSMS($array){
		
		$q = mysql::i()->query('show columns from log_outbound_sms');
                
		while($r=mysql::i()->fetch_object($q))
		{
			$fields[]=$r->Field;
		}
		foreach($array as $key => $value){
			if(in_array($key,$fields)){
				$cols.="$key,";
				$values.="'".mysql::i()->escape_string($value)."',";				
			}
		}
		$cols=substr($cols,0,-1);
		$values=substr($values,0,-1);
                
        $sql = "INSERT INTO log_outbound_sms ({$cols}, date, time, datetime, status) VALUES ({$values}, CURDATE() , CURTIME() , NOW(), '{$this->status}' ) \n\n";
		mysql::i()->query($sql);
		$id = mysql::i()->last_id();
		//debug::output("** $id **");
		return $id;
	}
	
	public function updateLogOutboundSMS($sent){
	//redundant
		$clientMessageReference = $sent->clientMessageReference;
		unset($sent->clientMessageReference);
		
		$q = mysql::i()->query('show columns from log_outbound_sms');
                
		while($r=mysql::i()->fetch_object($q))
		{
			$fields[]=$r->Field;
		}
		foreach($sent as $key => $value){
			if(in_array($key,$fields))
			{
				
				$set.="{$key}='".mysql::i()->escape_string($value)."',";				
			}
		}
		$set=substr($set,0,-1);		
                
        $sql = "UPDATE log_outbound_sms SET {$set} WHERE clientMessageReference='{$clientMessageReference}' \n\n";
		mysql::i()->query($sql);
		return mysql::i()->last_id();
	}
	
	public function logClientMsgRefTag($tags){
		$sql = "INSERT INTO log_client_msg_ref_tag (tags) VALUES ('{$tags}') \n";
		mysql::i()->query($sql);
		return mysql::i()->last_id();
	}
	
	public function updateLogOutboundSMSSentTrue($clientMessageReference){
		//$sql = "UPDATE log_outbound_sms SET sent=true,  clientMessageReference='{$log_outbound_sms_id}' WHERE log_outbound_sms_id='{$log_outbound_sms_id}' \n";
		$sql = "UPDATE log_outbound_sms SET sent=true WHERE clientMessageReference='{$clientMessageReference}' \n";
		mysql::i()->query($sql);
	}
	
	public function logHttpDeliveryStatus($fields){
		$sql = "INSERT INTO log_http_delivery_status (destination, messagestatuscode, clientmessagereference, messagereference, messagestatusdescription, messagestatusgroup, received, partcurrent, parttotal, date, time, datetime, ip) VALUES ('{$fields['destination']}', '{$fields['messagestatuscode']}', '{$fields['clientmessagereference']}', '{$fields['messagereference']}', '{$fields['messagestatusdescription']}', '{$fields['messagestatusgroup']}', '{$fields['received']}', '{$fields['partcurrent']}', '{$fields['parttotal']}', CURDATE() , CURTIME() , NOW()  ,INET_ATON('{$this->ip}')) \n";
		mysql::i()->query($sql);
	}
	
	public function logHttpInbound($fields){	
	
		$sql = "INSERT INTO log_http_inbound (originator, destination, body, rbid, date, time, datetime, ip, status) VALUES ('{$fields['originator']}', '{$fields['destination']}', '{$fields['body']}', '{$fields['rbid']}', '{$fields['date']}', '{$fields['time']}', NOW() ,INET_ATON('{$this->ip}'), '{$this->status}' ) \n";
		
		mysql::i()->query($sql);
	}
	
    public function logTagActivation($fields){
        $sql = "INSERT INTO sms_log_tag_activation (mobile_number, message, valid) VALUES ('{$fields['mobile_number']}', '{$fields['message']}', '{$fields['valid']}') \n";
    }//smsLogTagActivation
    
    public function logSendSMS(){
        echo $sql = "UPDATE SOME TABLE THAT THERE HAS BEEN AN ERROR\n";
        
    }//smsLogSendSMS
    
    
    
    public function logCheckLostBagEmail($customer_id, $email_message, $email_address, $status, $tag_no){      
        $sql = "INSERT INTO log_check_lost_bag_email (customer_id, email_message, email_address, status, tag_no, date_time) VALUES ('{$customer_id}', '{$email_message}', '{$email_address}', '{$status}', '{$tag_no}', now() ) \n";
        mysql::i()->query($sql);
    }//logCheckLostBagEmail
    
    public function logCheckLostBagSMS($customer_id, $email_message, $email_address, $status){      
        echo $sql = "INSERT INTO log_check_lost_bag_sms \n";
        mysql::i()->query($sql);
    }//logCheckLostBagSMS
    
    public function logCheckLostBagInvalidTag($tag_no){
        
		$sql = "INSERT INTO log_check_lost_bag_invalid_tag (tag_no, datetime) VALUES ('{$tag_no}', now() ) \n";
		mysql::i()->query($sql);
        
    }//logCheckLostBagInvalidTag
    
	
	
    
	
	public function logUnrecognisedTagRequest($body, $originator, $telephone){
		$sql = "INSERT INTO log_unrecognised_tags (body, originator, telephone, datetime, ip) values ('{$body}', '{$originator}', '{$telephone}', now() ,INET_ATON('{$this->ip}')) \n";
		mysql::i()->query($sql);
		
	}
	
	
	public function logValidTags($tag, $phone_number){
			
		$sql = "SELECT * FROM log_recognised_tags where tag_no = '{$tag}' ";
		$q = mysql::i()->query($sql);
		if(mysql::i()->num_rows($q)==0){
			$sql = "INSERT INTO log_recognised_tags (tag_no, phone_number, activated, datetime, status) values ('{$tag}', '{$phone_number}', false, now(), '{$this->status}' ) \n";
			mysql::i()->query($sql);
		}
		
	}
	
	public function logValidTagsOld($tags, $phone_number){
		$already_registered = false;
		foreach($tags as $tag_no => $tag){
		
			
			$sql = "SELECT * FROM log_recognised_tags where tag_no = '{$tag['tag_no']}' and activated=true";
			$q = mysql::i()->query($sql);
			if(mysql::i()->num_rows($q)==0){
				$sql = "INSERT INTO log_recognised_tags (tag_no, phone_number, activated, datetime, status) values ('{$tag['tag_no']}', '{$phone_number}', false, now(), '{$this->status}' ) \n";
				mysql::i()->query($sql);
			}
		}
		return $already_registered;
	}
	
	public function unLogValidTags($tag){
					
		$sql = "UPDATE log_recognised_tags SET activated=true  WHERE tag_no = '{$tag}'  \n";
		mysql::i()->query($sql);	
				
	}//unLogValidTags
    
}
?>
