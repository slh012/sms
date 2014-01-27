<?php
/*
 * Created: ??/6/2012
* Change Log:
*
*  Author: Sean Hardaker (seanhardaker@gmail.com)
*
*/

	class getSMSReply extends textAnywhere{
		
		/*
		 * Retrieve inbound messages sent as replies to a previously sent message
		 * http://developer.textapp.net/WebService/Methods_GetSMSReply.aspx
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
			
			
			
			$xPathResult = $this->xpath->search("//Transaction/Code");			
			if(!empty($xPathResult)){
				$this->result[$xPathResult->item(0)->nodeName] = $xPathResult->item(0)->nodeValue;
			}
			
			$xPathResult = $this->xpath->search("//Transaction/Description");
			if(!empty($xPathResult)){
				$this->result[$xPathResult->item(0)->nodeName] = $xPathResult->item(0)->nodeValue;
			}
			
			
			
			$xPathResult = $this->xpath->search("//Replies/Reply");
			if(!empty($xPathResult)){
				
				for($i=0; $i < $xPathResult->length; $i++){
					
					$id = $this->xpath->search("//ID", $xPathResult->item($i))->item($i)->nodeValue;			
				 
					$this->result[$id]['ClientMessageReference'] = $this->xpath->search("//ClientMessageReference", $xPathResult->item($i))->item($i)->nodeValue;
					$this->result[$id]['PartTotal'] = $this->xpath->search("//PartTotal", $xPathResult->item($i))->item($i)->nodeValue;
					$this->result[$id]['Originator'] = $this->xpath->search("//Originator", $xPathResult->item($i))->item($i)->nodeValue;
					$this->result[$id]['Destination'] = $this->xpath->search("//Destination", $xPathResult->item($i))->item($i)->nodeValue;
					$this->result[$id]['Date'] = $this->xpath->search("//Date", $xPathResult->item($i))->item($i)->nodeValue;
					$this->result[$id]['Time'] = $this->xpath->search("//Time", $xPathResult->item($i))->item($i)->nodeValue;
					$this->result[$id]['Body'] = $this->xpath->search("//Body", $xPathResult->item($i))->item($i)->nodeValue;
									
				}//for
				
			}//if
			
			
			print_r($this->result);
			
			
			
			if(isset($this->result['Code']))if($this->result['Code'] >= 2) $this->fault = true;
				
			
			
		}//xmlresponse
		
		
		
	}//getSMSReply

?>