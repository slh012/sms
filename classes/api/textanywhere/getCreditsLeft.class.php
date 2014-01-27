<?php
/*
 * Created: ??/6/2012
* Change Log:
*
*  Author: Sean Hardaker (seanhardaker@gmail.com)
*
*/

	class getCreditsLeft extends textAnywhere{
		
		/*
		 * Query the number of Message Credits available on a client’s pre-pay account
		 * http://developer.textapp.net/WebService/Methods_GetCreditsLeft.aspx
		 */
		
		
		public function __construct($vars, $live=false){
		
			parent::__construct($vars, $live);
			
			$this->method = get_class();
			
			$this->logcode = 1;
			
		}//__construct
		
		public function request(){
			
			parent::auth();
			
			parent::send();
			
		}//request
		
				
		protected function xmlresponse(){
			
			parent::xpath($this->response, $this->method);
			
			$xPathResult = $this->xpath->search("//Transaction/IP");
			if(!empty($xPathResult)){
				$this->result[$xPathResult->item(0)->nodeName] = $xPathResult->item(0)->nodeValue;
			}
			
			$xPathResult = $this->xpath->search("//Transaction/Code");			
			if(!empty($xPathResult)){
				$this->result[$xPathResult->item(0)->nodeName] = $xPathResult->item(0)->nodeValue;
			}
			
			$xPathResult = $this->xpath->search("//Transaction/Description");
			if(!empty($xPathResult)){
				$this->result[$xPathResult->item(0)->nodeName] = $xPathResult->item(0)->nodeValue;
			}
			
			$xPathResult = $this->xpath->search("//Status");
			if(!empty($xPathResult)){
				$this->result[$xPathResult->item(0)->nodeName] = $xPathResult->item(0)->nodeValue;
			}
			
			$xPathResult = $this->xpath->search("//CreditLeft");
			if(!empty($xPathResult)){
				$this->result[$xPathResult->item(0)->nodeName] = $xPathResult->item(0)->nodeValue;
			}
			
			
			if($this->result['Code'] >= 2) $this->fault = true;
					
			
			print_r($this->result);
			
			
			
		}//xmlresponse
		
		
		
	}//getCreditsLeft

?>