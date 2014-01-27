<?php
/*
 * Created: 18/9/2012
 * Change Log:
 * 
 *  Author: Sean Hardaker (seanhardaker@gmail.com)
 *  
 */
 
class cancel extends sms{

	public function __construct($input){
		
		parent::__construct($input);	
		
		$this->getTags();		
		
		foreach($this->tags as $nothing => $tag){
			
			debug::output("\n\nChecking tag... {$tag} ...\n\n");
			
			$okay = $this->inputValidation($tag);			
			
			if($okay == false)continue;
			
			$this->formatTagArray($tag);
			
			
			
		}//foreach tag
		
		
		if(!empty($this->errors)){
			
			parent::logErrors();
		
			foreach($this->errors as $tag => $message){
				debug::output($message);
				$this->send['body'] = $message;				
				parent::sendSMS();
				
				
			}//foreach error
			unset($this->errors);
		}//if errors
		
		
		
		if(!empty($this->valid)){
			foreach($this->valid as $tag => $array){
			
				debug::output("Cancel tag {$tag}");
				
				$okay = $this->cancelTag($tag);
				
				if($okay==false){				
					
					$this->send['body'] = $this->errors[$tag];
					parent::sendSMS();
					
				}else{
				
					$this->send['body'] = "Tag {$tag} has been canceled.";				
					parent::sendSMS();
				}
				
				
			}//foreach valid tag
		}//if valid tags
		
		if(!empty($this->errors))parent::logErrors();
		
	}//constructor
	
	private function cancelTag($tag){
		echo $sql = "UPDATE tag_registered r left join customer c on r.customer_id=c.customer_id SET is_deleted=1 WHERE r.tag_no='{$tag}' AND telephone='{$this->phone_number}' \n";
		$q = mysql::i()->query($sql);
		
		if(mysql::i()->affected_rows($q)==0){
		
			$this->errors[$tag] = "Tag ({$tag}) was not cancelled. Please check and try again or call ".config::get('lost_bag_number');		
			
			return false;
			
		}else{
			return true;
		}
	}
	
	private function inputValidation($tag){
		
		$okay = true;
		
		/*
		* Make sure tag is not empty
		*/
		$okay =parent::checkHasContents($tag);	
		if($okay == false)return false;		
		debug::output("Validated: Tag ({$tag}) is not empty.");
		
		
		/*
		* Make sure tag is in the correct format
		*/
		$okay = parent::checkTagFormat($tag);			
		if($okay == false)return false;		
		debug::output("Validated: Tag ({$tag}) is correctly formatted.");
		
		
		
		return true;
		
	}//inputValidation
	
	

}//cancel

?>