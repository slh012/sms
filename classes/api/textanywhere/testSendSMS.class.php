<?php
/*
 * Created: ??/6/2012
 * Change Log:
 *
 *  Author: Sean Hardaker (seanhardaker@gmail.com)
 *
 */

	class testSendSMS extends textAnywhere{
		
		/*
		 * Send a no-charge test SMS message to one or more numbers
		 * http://developer.textapp.net/WebService/Methods_TestService.aspx
		 */
		
		public function __construct($vars, $live=false){
		
			parent::__construct($vars, $live);
			
			$this->method = get_class();
			
			//$this->logcode = 2;
			
		}//__construct
		
		public function request(){
			
			parent::auth();
			
			$this->params->clientBillingReference = $this->vars['clientBillingReference'];
			$this->params->originator = $this->vars['originator'];
			$this->params->destinations = $this->vars['destinations'];
			$this->params->body = $this->vars['body'];
			$this->params->validity = $this->vars['validity'];
			$this->params->replyMethodID = $this->vars['replyMethodID'];
			$this->params->replyData = $this->vars['replyData'];
			$this->params->StatusNotificationUrl = $this->vars['StatusNotificationUrl'];
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
		
		
		
	}

?>