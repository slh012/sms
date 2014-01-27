<?php
/*
 * Created: 16/9/2012
 * Change Log:
 * 
 *  Author: Sean Hardaker (seanhardaker@gmail.com)
 *  
 */
 
class report extends sms{

	public function __construct($input){
		
		parent::__construct($input);		
		
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
				
					debug::output("Checking lost bag report for tag {$tag}");
					
					$lostBag = $this->getLostBagLocation($tag);
					
					
					
					$this->send['body'] = $this->lostBagMessage($lostBag[0], $tag);
					
					
					parent::sendSMS();
					
					
				}//foreach valid tag
			}//if valid tags
		
		}else{		
			
			//no pin number supplied
			
			$lostBag = $this->getLostBagLocation();
			
			
			
			if(is_array($lostBag)){
				foreach($lostBag as $key => $array){
				
					$this->send['body'] = $this->lostBagMessage($array);
					parent::sendSMS();
				}//foreach lost tag
			}else{
				$this->send['body'] = 'There are no reports available for your mobile number.';
				parent::sendSMS();
			}
		
		}//else
		
	}//constructor
	
	private function lostBagMessage($lostBag, $tag=''){
	
		if(!empty($lostBag)){
			$msg = "Tag {$lostBag['tag_no']} has been found at {$lostBag['airport']}, {$lostBag['place']}, {$lostBag['state']}, {$lostBag['country']}. World Tracer number: {$lostBag['world_tracer_ref']}. ";;
		}else{
			if(!empty($tag)){
				$msg = "There are no lost bag reports on file for tag {$tag}.";
			}else{
				$msg = "There are no lost bag reports on file against this phone number.";
			}
		}
	
		return $msg;
	
	}//lostBagMessage
	
	
	
	private function getLostBagLocation($tag=''){
		
		if(!empty($tag)){
			$sql = "SELECT * FROM log_check_lost_bag_inbox_notified LEFT JOIN airports USING (iata) WHERE tag_no = '{$tag}' AND contact_details = '{$this->phone_number}' \n";
		}else{
			$sql = "SELECT * FROM log_check_lost_bag_inbox_notified LEFT JOIN airports USING (iata) WHERE contact_details = '{$this->phone_number}' \n";
		}
		
		$q = mysql::i()->query($sql);
		if(mysql::i()->num_rows($q)==0){		
			return;
		}
		elseif(mysql::i()->num_rows($q)==1){		
			
			$array[] = mysql::i()->fetch_array($q);
			
			return $array;
			
		}else{
			while($r = mysql::i()->fetch_array($q)){
			
				if($r['country']=='USA')continue;				
				
				if($tag){
					$array[] = $r;
				
				}else{
					$array[] = $r;
				}
				
			}
			
			return $array;
		}
		
	}//getLostBagLocation
	
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
		* Make sure tag exists in database
		*/
		$okay = parent::checkTagValidityGroup($tag);			
		if($okay == false){
			debug::output("Invalid: ".$this->errors[$tag]);
			return false;		
		}
		debug::output("Validated: Tag ({$tag}) exists in database.");	
		
		
		return true;
		
	}//inputValidation

}//report
?>