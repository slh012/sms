<?php
/*
 * Created: ??/6/2012
* Change Log:
*
*  Author: Sean Hardaker (seanhardaker@gmail.com)
*
*/
	class getSubscribers extends textAnywhere{
		
		/*
		 * Retrieve the phone numbers of subscribers to a short code or long number service
		 * http://developer.textapp.net/WebService/Methods_GetSubscribers.aspx
		 * 
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
			
			
			$xPathResult = $this->xpath->search("//Subscribers/Subscriber");
			if(!empty($xPathResult)){
				
				for($i=0; $i < $xPathResult->length; $i++){
				
					$originator = $this->xpath->search("//Originator", $xPathResult->item($i))->item($i)->nodeValue;
				
					$this->result[$originator]['Destination'] = $this->xpath->search("//Destination", $xPathResult->item($i))->item($i)->nodeValue;
					$this->result[$originator]['Keyword'] = $this->xpath->search("//Keyword", $xPathResult->item($i))->item($i)->nodeValue;
					$this->result[$originator]['Date'] = $this->xpath->search("//Date", $xPathResult->item($i))->item($i)->nodeValue;
					$this->result[$originator]['Time'] = $this->xpath->search("//Time", $xPathResult->item($i))->item($i)->nodeValue;
					$this->result[$originator]['Body'] = $this->xpath->search("//Body", $xPathResult->item($i))->item($i)->nodeValue;
					$this->result[$originator]['Network'] = $this->xpath->search("//Network", $xPathResult->item($i))->item($i)->nodeValue;
					$this->result[$originator]['RBID'] = $this->xpath->search("//RBID", $xPathResult->item($i))->item($i)->nodeValue;
					
				
				}//for
			}//if
				
			
			
			
			if($this->result['Code'] >= 2) $this->fault = true;
					
			
			print_r($this->result);
			
			
			
		}//xmlresponse
		
		
		
	}//getSubscribers

?>