<?php
/*
 * Created: ??/6/2012
* Change Log:
*
*  Author: Sean Hardaker (seanhardaker@gmail.com)
*
*/
	class sendPremiumSMS extends textAnywhere{
		
		/*
		 * Send a premium, charged-for text message to one or more mobile numbers
		* http://developer.textapp.net/WebService/Methods_SendPremiumSMS.aspx
		*/
		
		
		public function __construct($vars, $live=false){
		
			parent::__construct($vars, $live);
			
			$this->method = get_class();
			
			//$this->logcode = 2;
		}//__construct
		
		public function request(){
			
			parent::auth();
			
			
			
			$this->params->clientBillingReference = $this->vars['clientBillingReference'];
			$this->params->clientMessageReference = $this->vars['clientMessageReference'];
			$this->params->rbid = $this->vars['rbid'];
			$this->params->body = $this->vars['body'];
			$this->params->validity = $this->vars['validity'];			
			$this->params->characterSetID = $this->vars['characterSetID'];
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
			
			$xPathResult = $this->xpath->search("//Status");
			if(!empty($xPathResult)){
				$this->result[$xPathResult->item(0)->nodeName] = $xPathResult->item(0)->nodeValue;
			}
			
			
			if(isset($this->result['Code']))if($this->result['Code'] >= 2) $this->fault = true;
					
			
			print_r($this->result);
			
			
			
		}//xmlresponse
		
		
		
	}//sendPremiumSMS

?>