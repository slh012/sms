<?php
/*
 * Created: ??/6/2012
* Change Log:
*
*  Author: Sean Hardaker (seanhardaker@gmail.com)
*
*/

	class getPremiumSMSStatus extends textAnywhere{
		
		/*
		 * Query the SMS gateway and return the statuses of previously sent premium messages
		 * http://developer.textapp.net/WebService/Methods_GetPremiumSMSStatus.aspx
		 */
		
		
		public function __construct($vars, $live=false){
		
			parent::__construct($vars, $live);
			
			$this->method = get_class();
			
			$this->logcode = 2;
			
		}//__construct
		
		public function request(){
			
			parent::auth();
			
			$this->params->clientMessageReference = $this->vars['clientMessageReference'];
			
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
			
						
			$xPathResult = $this->xpath->search("//Statuses/Status");
			if(!empty($xPathResult)){
				for($i=0; $i < $xPathResult->length; $i++){
					
					$destination = $this->xpath->search("//Destination", $xPathResult->item($i))->item($i)->nodeValue;					
					
					$this->result[$destination]['CurrentPart'] = $this->xpath->search("//CurrentPart", $xPathResult->item($i))->item($i)->nodeValue; 
					$this->result[$destination]['TotalPart'] = $this->xpath->search("//TotalPart", $xPathResult->item($i))->item($i)->nodeValue;
					$this->result[$destination]['StatusCode'] = $this->xpath->search("//StatusCode", $xPathResult->item($i))->item($i)->nodeValue;
					$this->result[$destination]['StatusName'] = $this->xpath->search("//StatusName", $xPathResult->item($i))->item($i)->nodeValue;
					$this->result[$destination]['StatusDescription'] = $this->xpath->search("//StatusDescription", $xPathResult->item($i))->item($i)->nodeValue;
									
				}//for
			
			}//if
			
			print_r($this->result);
			
			if(isset($this->result['Code']))if($this->result['Code'] >= 2) $this->fault = true;
			
			
		}//xmlresponse
		
		
		
	}//getPremiumSMSStatus

?>