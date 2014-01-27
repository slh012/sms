<?php
/*
 * Created: 16/9/2012
 * Change Log:
 * 
 *  Author: Sean Hardaker (seanhardaker@gmail.com)
 *  
 */
 
class activate extends sms{
	
	/*
	 * Type: object
	 * Purpose: Class for managing customer records
	 */
	private $customer;
	
	public function __construct($input){
		
		parent::__construct($input);
		
		
		
		if($this->getValidTags()==false){
		
			debug::output("There are no tags pending activation against this phone number: {$this->phone_number}");
			
			$this->send['body'] = 'There are no tags pending activation against this phone number. For support call customer services on '.config::get('customer_services');
			
			parent::sendSMS();
			
		}else{
		
			foreach($this->tags as $tag => $array){
				
				debug::output("Checking tag... {$tag} ...");
				
				$okay = $this->tagValidation($tag);		
				
				if($okay == false)continue;
			
				$this->valid[$tag] = $array;
				
			}//foreach tag
			
			if(!empty($this->errors)){
			
				parent::logErrors();
			
				parent::sendErrors();
				
			}//if errors
			
			
			
			if(!empty($this->valid)){
			
				$this->customer = new customer($this->logDB);
				
				debug::output("Create customer account...");
				
				$result = $this->customer->createCustomerAccount($this->phone_number);
				
				if($result['success']==false){					
					debug::output($result['message']);
					$this->errors[] = $result['message'];
					
				}else{
					//iterate through each tag and register each tag
					foreach($this->valid as $tag => $array){
						
						debug::output("Register tag {$tag}");
					
						$this->registerTag($tag);		
						
						parent::updateTagGroup($tag);
						
						$this->logDB->unLogValidTags($tag);	
						
						$registerd[$tag]['message'] = 'Tag '.$tag.' activated successfully. ';	
						
						$registerd[$tag]['tag_type'] = $array['tag_type'];
						
					}//foreach tag
				}
				
				if(!empty($this->errors)){
					
					parent::sendErrors();
					
					parent::logErrors();
					
				}//if errors
				
				
				/*
				* send as few sms as possible each time
				*/
				
				if(!empty($registerd)){
					foreach($registerd as $tag => $array){
						debug::output($message);
						$activation_m .= $array['message'];
						//$array['tag_type']; use type to determine premium or free sms					
						
					}//foreach valid
					
					$this->send['body'] = $activation_m.$result['message'];
					$this->sendSMS();
				}//if not empty
				
			}//else
		
		}//else
		
		
	}//constructor
	
	
	
	private function registerTag($tag){			
			
		$t = self::getTagsFromGroup($tag);		
					
		$date_expire = $this->tagExpireDate($t['validity_period']);
		
		$sql = "INSERT INTO tag_registered (tag_no, customer_id, tag_type, date_registered, validity_period, date_expire, status) VALUES ( '{$t['tag']}', '{$this->customer->customer_id}', '{$t['tag_type']}', now(), '{$t['validity_period']}', '$date_expire', 'ACTIVE' ) \n";
		
		mysql::i()->query($sql);
		
			
	}//registerTag
	
	private function tagExpireDate($validity_period){
	
		$period = utils::stripNumeric($validity_period);
		
		$duration = utils::stripAlphabetic($validity_period);
		
		switch(strtolower(trim($period))){
			case "month":
			case "months":
				$date_expire = date('Y-m-d', mktime(0,0,0,date('m')+$duration,date('d'),date('Y')));
			break;
			case "day":
			case "days":
				$date_expire = date('Y-m-d', mktime(0,0,0,date('m'),date('d')+$duration,date('Y')));
			break;
			case "year":
			case "years":
				$date_expire = date('Y-m-d', mktime(0,0,0,date('m'),date('d'),date('Y')+$duration));
			break;
			default;
				$date_expire = '1900-01-01';
				debug::output("Expiry period Unknown");
		}//end switch
		return $date_expire;
		
	}
	
	private function getTagsFromGroup($tag){
		$sql = "SELECT * FROM group_tags WHERE tag='{$tag}' \n";
		$q = mysql::i()->query($sql);		
		return mysql::i()->fetch_array($q);		
		
	}//getTagsFromGroup
	
	private function tagValidation($tag){
	
		$okay = true;
		
		/*
		* Make sure tag is not already registered
		*/
		$okay = $this->tagAlreadyRegistered($tag);			
		if($okay == false)return false;		
		debug::output("Validated: Tag ({$tag}) has not been registered.");
		
		//validate exists in groups also
		
		return true;
	
	}//tagValidation
	
	public function tagAlreadyRegistered($tag){
						
		$sql = "SELECT * FROM tag_registered WHERE tag_no='{$tag}' \n";
		
		$q = mysql::i()->query($sql);
		
		if(mysql::i()->num_rows($q)>0){	
		
			$this->errors[$tag] = 'Tag '.$tag['tag_no'].' has already been already activated. Please check and try again or call '.config::get('lost_bag_number');	
			
			return false;					
			
		}else{
		
			return true;
		}			
		
	}//tagAlreadyRegistered
	
	private function getValidTags(){
	
		debug::output("Get tags against phone number {$this->phone_number}...");
	
		echo $sql = "SELECT * FROM log_recognised_tags t left join group_tags g on t.tag_no = g.tag  WHERE phone_number = '{$this->phone_number}' AND activated=false \n";
		$q = mysql::i()->query($sql);
		
		if(mysql::i()->num_rows($q)==0){
			return false;
		}else{
		
			while($r = mysql::i()->fetch_array($q)){
				$this->tags[$r['tag_no']] = $r;
			}
			return true;
		}
		
	}//getValidTags
	
	
	
}//activate
?>