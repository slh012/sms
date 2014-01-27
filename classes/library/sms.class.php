<?php

class sms{
	
	/*
	 * Type: boolean
	 * Purpose: Specifies the environment
	 */
	protected $live;
	
	
	/*
	 * Type: object
	 * Purpose: Class for recording events to the log tables
	 */
	protected $logDB;
	
	
	/*
	 * Type: array
	 * Purpose: Holds the external input in it's original form
	 */
	protected $oInput;
	
	
	/*
	 * Type: array
	 * Purpose: Holds the external input after formatting
	 */
	protected $fInput;
	
	/*
	 * Type: array
	 * Purpose: Holds the errors when processing the tags
	 */
	public $errors;
	
	/*
	 * Type: array
	 * Purpose: Holds the send data for the API
	 */
	protected $send;
	
	
	/*
	 * Type: string
	 * Purpose: Holds the originating phone number
	 */
	protected $phone_number;
	
	/*
	 * Type: array
	 * Purpose: Holds the tags during processing
	 */
	protected $tags;
	
	
	/*
	 * Type: array
	 * Purpose: Holds the valid tags
	 */
	protected $valid;
	
	
	/*
	 * Type: float
	 * Purpose: Holds the cost of a tag
	 */
	protected $cost;
	
	/*
	 * Type: string
	 * Purpose: Holds the type of a tag
	 */
	protected $type;
	
	public function __construct($input){
		
		$this->oInput = $input;
		
		//$this->live = LIVE;
	
		$this->logDB = new log_database();
		
		$this->formatInputData();
		
		$this->formatPhoneNumber();	
		
	}
	
	protected function formatInputData(){
		if(!empty($this->oInput['body'])){
			$this->fInput['body'] = strtoupper(trim($this->oInput['body']));	
			
			if(!empty($this->oInput['rbid'])) $this->send['rbid'] = trim($this->oInput['rbid']);	
		}
	}
	
	protected function formatPhoneNumber(){
		if(!empty($this->oInput['originator'])){
			$this->phone_number = utils::formatPhoneNumber($this->oInput['originator']);  
			$this->send['destinations'] = $this->phone_number;
		}
	}
	
	protected function extractTagNo($tag){
		
		$tag = trim($tag);
		$firstTwo = strtoupper(substr($tag, 0, 2));
		
		if($firstTwo == 'OC' && strlen($tag) == 18){
			return trim(substr($tag, 2));
		}else{
			return $tag;
		}
		
	}		
	
	protected function logErrors(){
		//not valid response. log details
		$this->logDB->logUnrecognisedTagRequest($this->oInput['body'], $this->oInput['originator'], $this->phone_number);		
	}//logErrors
	
	protected function sendErrors(){
		foreach($this->errors as $tag => $message){
			debug::output($message);
			$this->send['body'] = $message;				
			$this->sendSMS();
		}//foreach error
		unset($this->errors);
	}//sendErrors
	
	protected function sendSMS($debug=''){
		
		
		
		utils::recursiveCall('sendSMS', $this->send, 1, $debug);
		sleep(1);
		
	}//sendSMS
	
	
	
	protected function checkTagFormat($tag){				 
		
		
		
		$nonAlphaNumeric = utils::stripAlphaNumeric($tag);
		
		if(strlen($tag) != 16 || !empty($nonAlphaNumeric)){
			
			$this->errors[$tag] = "Tag {$tag} is not in the correct format. Please check and try again or call ".config::get('customer_services');

			
			return false;
			
		}//if invalid
		else{
		
			return true;
			
		}//else
	}//checkTagFormat	
	
	protected function checkHasContents($var){
	
		if(empty($var)){			
			
			//$this->errors[$var] = 'No Message Body.';
			
			return false;
			
		}//empty body
		else{
		
			return true;
			
		}
		
	}//checkHasContents
	
	protected function checkTagValidityGroup($tag){
		$sql = "SELECT * FROM group_tags WHERE tag = '{$tag}' \n";
		$q = mysql::i()->query($sql);


		if(mysql::i()->num_rows($q)==0){
			
			$this->errors[$tag] = 'Tag '.$tag.' is not recognised by our system please. Please check and try again or call '.config::get('customer_services');				
			
			return false;
		}else{
			return $q;
		}
	}	
	
	protected function getTags(){
	
		$tags_temp = explode(',',trim($this->fInput['body']));    		
		
		if(count($tags_temp)==1){
			//allow spaces as well as CSV			
			$tags_temp = explode(' ',trim($this->fInput['body']));    		
		}
		
		foreach($tags_temp as $k => $tag){
			$tag = trim($tag);
			
			if(empty($tag))continue;
			
			$tag = $this->extractTagNo($tag); 
			
			$this->tags[$tag] = trim($tag);
		}
		
	}//getTags
	
	protected function updateTagGroup($tag){	
		
		$sql = "UPDATE group_tags SET action='USED' WHERE tag = '{$tag}' \n";
		mysql::i()->query($sql);		         
		
	}//updateTagGroup
	
	protected function getTagCost($tag_type, $renewal = false){
		
		$tag_type = strtoupper(trim($tag_type));
	
		if(!$renewal){
			//new tag
			if($tag_type == 'PAPER'){
				return '1.50';
			}else{
				//plastic
				return 0;
			}
		}else{
			//renewal
			if($tag_type == 'PAPER'){
				return '1.50';
			}else{
				//plastic
				return '3.00';
			}
		}
	}//getTagCost
	
	protected function formatTagArray($tag){
		$this->valid[$tag]['cost'] = number_format($this->cost,2,'.',',');
		$this->valid[$tag]['type'] = $this->type;
	}
	
}//sms

?>