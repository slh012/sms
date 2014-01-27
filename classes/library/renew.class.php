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
 
class renew extends sms{
	
	
	
	public function __construct($input){
		
		$input['body'] = str_ireplace('RENEW','',$input['body']);
		
		parent::__construct($input);	
		
		
		
		if(parent::checkHasContents($this->fInput['body']) == true){	
		
			parent::getTags();		
			
			foreach($this->tags as $tag){
				
				debug::output("\n\nChecking tag... {$tag} ...\n\n");
				
				$okay = $this->inputValidation($tag);			
				
				if($okay == false)continue;
							
						
							
				$this->valid[$tag]['type'] = $tag;
				
				parent::formatTagArray($tag);
				
			}//foreach tag
			
			
			if(!empty($this->errors)){
				
				parent::logErrors();
				
				parent::sendErrors();
								
			}//if errors
			
			
			if(!empty($this->valid)){
			
				$customer = new customer($this->logDB);
				$customer->checkAccountExists($this->phone_number);
				
				
				
				foreach($this->valid as $tag => $array){
				
					debug::output("Renew tag {$tag}");
					
					if($this->updateTagRegistered($tag, $customer->customer_id)){
					
						parent::updateTagGroup($tag);
						
						//paper & plastic
						//with & w/out email address
						
						
						//is paper going to be renewed. Confirm.
						
						$cost = parent::getTagCost($array['type'], true);
						
						if(empty($customer->customer['email'])){
							$renewed[$tag] = "{$tag} renewed.  Text cost £{$cost} each plus standard network rate. Go to example.com to update your email address with username {$customer->customer['username']}. Text PIN STOP to cancel";
						}else{
							$renewed[$tag] = "{$tag} renewed.  Text cost £{$cost} each plus standard network rate. Manage your account at example.com with email {$customer->customer['email']}. Text PIN STOP to cancel";
						}
						
					}//tag registered									
					
					
				}//foreach valid tag
			}//if valid tags
			
			
			
			if(!empty($this->errors)){
				
				parent::logErrors();
				
				parent::sendErrors();
								
			}//if errors
			
			if(!empty($renewed)){
				foreach($renewed as $tag => $message){
				
					debug::output($message);
					
					$this->send['body'] = $message;
					
					//$array['tag_type']; use type to determine £1.50 or £3
					$this->sendSMS();
					
				}//foreach valid				
				
			}//if not empty
			
		}//not empty
		else{
		
			//no tag provided
		
		}
		
		
	}//constructor
	
	
	private function inputValidation($tag){
		
		$okay = true;
		
		/*
		* Order of validation. Order done to reduce database calls. Check formatting is correct. Then check if activated next. 
		* This cuts out 2 other DB checks as it must be in the group table if activated. 
		* Same reason to check group table next, then if logged.
		*/
		
		
		/*
		* Make sure tag is in the correct format
		*/
		$okay = parent::checkTagFormat($tag);			
		if($okay == false){
			debug::output("Invalid: ".$this->errors[$tag]);
			return false;		
		}
		debug::output("Validated: Tag ({$tag}) is correctly formatted.");
		
			
		
		/*
		* Make sure tag exists in database (do we need to do this check?)
		*/
		$okay = $this->checkTagValidityDatabase($tag);			
		if($okay == false){
			debug::output("Invalid: ".$this->errors[$tag]);
			return false;		
		}
		debug::output("Validated: Tag ({$tag}) exists in database.");	
		
		/*
		* Make sure tag exists in database
		*/
		$okay = $this->checkDueForRenew($tag);			
		if($okay == false){
			debug::output("Invalid: ".$this->errors[$tag]);
			return false;		
		}
		debug::output("Validated: Tag ({$tag}) is due for renewal.");		
		
		return true;
		
		
		
	}//inputValidation
	
	private function checkDueForRenew($tag){
		
		//$sql = "SELECT c.*, t.* from tag_registered t left join customer c using (customer_id) WHERE t.status = 'ACTIVE' AND is_deleted IS NULL AND  t.tag_no='{$tag}'";
		
		$sql = "SELECT c.*, t.* from tag_registered t left join customer c using (customer_id) WHERE (c.telephone<>'' OR c.email<>'') AND t.status = 'ACTIVE' AND t.is_deleted IS NULL AND t.date_expire < UTC_DATE() AND t.tag_no='{$tag}'";
		
		$q = mysql::i()->query($sql);
		
		if(mysql::i()->affected_rows($q)==0){
			$this->errors[$tag] = 'Tag '.$tag.' is not due for renewal. Contact customer servies on '.config::get('customer_services');
			return false;
		}else{
			$r = mysql::i()->fetch_array($q);
			
			//check customer is associated with tag to be renewed
			if($r['telephone'] != $this->phone_number){
				$this->errors[$tag] = 'Tag '.$tag.'. can\'t be renewed from this mobile. Contact customer servies on '.config::get('customer_services');
				return false;
			}else{
				return true;
			}
		
		}
		
	
	}
	
	
	
	
	private function updateTagRegistered($tag, $customer_id){
	
		$date_expire = date('Y-m-d', mktime(0,0,0,date('m')+3,date('d'),date('Y')));
	
		$sql = "UPDATE tag_registered SET date_renew=now(), date_expire='{$date_expire}', validity_period='3months', status='ACTIVE' WHERE tag_no='{$tag}' AND customer_id='{$customer_id}' \n";
		$q = mysql::i()->query($sql);
		if(mysql::i()->affected_rows($q)==0){
			$this->errors[$tag] = 'There was a problem renewing tag '.$tag.'. Contact customer servies on '.config::get('customer_services');
			return false;
		}else{
			
			return true;
		}
	}//renewTag
	
	protected function checkTagValidityDatabase($tag){ 	
			
			
		$q = parent::checkTagValidityGroup($tag);	
		
		if($q != false){
		
			$r = mysql::i()->fetch_array($q);	
			
			$this->tags[$tag] = $r;
			
					
			
			$this->cost = parent::getTagCost($r['tag_type']);
			
			
			
			$this->type = trim(strtoupper($r['tag_type']));
			
			
			return true;
		
		}

	}//checkPinValidityDatabase
	
	
}//renew		
?>