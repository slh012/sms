<?php
/*
 * Created: 14/9/2012
 * Change Log:
 * 
 *  Author: Sean Hardaker (seanhardaker@gmail.com)
 *  
 */
 
class register extends sms{
	
	
	
	
	
	
	
	public function __construct($input){
		
		parent::__construct($input);
		
		/*
		* Make sure tag is not empty
		*/
			
		if(parent::checkHasContents($this->fInput['body']) == true){			
		
		
			parent::getTags();		
			
			foreach($this->tags as $tag => $group){
				
				debug::output("\n\nChecking tag... {$tag} ...\n\n");
				
				$okay = $this->inputValidation($tag);			
				
				if($okay == false)continue;
				
				parent::formatTagArray($tag);
				
				
				
			}//foreach tag
			
			
			if(!empty($this->errors)){
				
				parent::logErrors();
				
				parent::sendErrors();
								
			}//if errors
			
			
			if(!empty($this->valid)){
				foreach($this->valid as $tag => $array){
				
					debug::output("Registering tag {$tag}");
					
					$this->logDB->logValidTags($tag, $this->phone_number);
					
					if($array['type']=='PAPER'){
					
						$this->send['body'] = "Tag {$tag} ready to be activated text PIN ACTIVATE to proceed. Tag valid for 3 months. Text cost £{$array['cost']} each plus standard network rate. Text PIN STOP to cancel.";						
						
					}else{
						//plastic

						$this->send['body'] = "Tag {$tag} ready to be activated text PIN ACTIVATE to proceed. Tag valid for 1 year. Text cost standard network rate each. Text PIN STOP to cancel.";						
						
					}
					
					
					
					parent::sendSMS();
					
					
				}//foreach valid tag
			}//if valid tags
			
		}//not empty
		
		
		
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
		* Make sure tag not already activated
		*/
		$okay = $this->checkTagAlreadyActivated($tag);			
		if($okay == false){
			debug::output("Invalid: ".$this->errors[$tag]);
			return false;		
		}	
		debug::output("Validated: Tag ({$tag}) not already activated.");
						
		
		/*
		*
		* Do we want to do this check? Better that the user gets a standard registration msg and continues as if it's the first time it's been registered. 
		* If logged by another user we should inform the current user that this tag is already in use
		*
		* Make sure tag not already registered to another user
		*/
		$okay = $this->checkTagAlreadyLogged($tag);			
		if($okay == false){
			debug::output("Invalid: ".$this->errors[$tag]);
			return false;		
		}	
		debug::output("Validated: Tag ({$tag}) not already registered.");
		
		
		
		/*
		* Make sure tag exists in database
		*/
		$okay = $this->checkTagValidityDatabase($tag);			
		if($okay == false){
			debug::output("Invalid: ".$this->errors[$tag]);
			return false;		
		}
		debug::output("Validated: Tag ({$tag}) exists in database.");	
		
		
		return true;
		
	}//inputValidation
	
	
	
	private function checkTagValidityDatabase($tag){ 	
			
		$q = parent::checkTagValidityGroup($tag);	
		
		if($q != false){
		
			$r = mysql::i()->fetch_array($q);	
			
			$this->tags[$tag] = $r;
			
			if($r['action']=='USED'){		
				//unless there has been a problem with the previous validation checks this condition should never be satisfied
				$this->errors[$tag] = 'Tag '.$tag.' is already in use. Please check and try again or call '.config::get('customer_services');				
				
				return false;
				
			}
			
			
			$this->cost = parent::getTagCost($r['tag_type']);
			
			
			
			$this->type = trim(strtoupper($r['tag_type']));
			
			
			return true;
		
		}

	}//checkPinValidityDatabase
	
	
	
	
	
	
	private function checkTagAlreadyActivated($tag){
		
				
		$sql = "SELECT * FROM tag_registered r left join customer c on r.customer_id=c.customer_id WHERE r.tag_no='{$tag}' AND is_deleted IS NULL  \n";
		$q = mysql::i()->query($sql);		
		
		if(mysql::i()->num_rows($q) > 0){									
			
			$r = mysql::i()->fetch_array($q);
			
			$status = strtoupper(trim($r['status']));
			
			$telephone = trim($r['telephone']);
			
			
			if($status == 'INACTIVE' && $telephone == $this->phone_number){
				
				$cost = $this->getTagCost($r['tag_type'], true);
				
				$this->send['body'] = "Tag {$tag} is already registered but has expired, to continue text PIN RENEW {$tag}. Tag valid for 3 months. Text cost £{$cost} each plus standard message rate.  Text PIN STOP to cancel.";						
				
				
			}elseif($status == 'ACTIVE' && $telephone == $this->phone_number){
			
				$this->errors[$tag] = 'Tag '.$tag.' is already registered and active. Please check and try again or call '.config::get('customer_services');	
				
			}else{
			
				$this->errors[$tag] = 'Tag '.$tag.' is already in use. Please check and try again or call '.config::get('customer_services');
				
			}		
			
			
			return false;
			
		}else{
		
			return true;
		}
			
		return false;		
	}//checkTagAlreadyActivated
	
	private function checkTagAlreadyLogged($tag){
		
				
		$sql = "SELECT * FROM log_recognised_tags WHERE tag_no='{$tag}' \n";
		$q = mysql::i()->query($sql);		
		
		if(mysql::i()->num_rows($q) > 0){				
			
			$r = mysql::i()->fetch_array($q);
			
			if($r['phone_number'] == $this->phone_number){
				//tag is pending activation and belongs to this mobile number. No need to create confusion. Better to validate and continue as if it's the first time it's been registered. 
				return true;
			}else{
				$this->errors[$tag] = 'Tag '.$tag.' is pending activation against another mobile number. Please check and try again or call '.config::get('customer_services');
				return false;
			}		
						
			
		}else{
			//not previously registered according to this log
			return true;
		}
			
		return false;		
	}//checkTagAlreadyRegistered
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}//register
?>