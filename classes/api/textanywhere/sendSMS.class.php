<?php
/*
 * Created: ??/6/2012
* Change Log:
*
*  Author: Sean Hardaker (seanhardaker@gmail.com)
*
*/

	class sendSMS extends textAnywhere{
		
		/*
		 * Send a free-to-receive text message to one or more numbers
		 * http://developer.textapp.net/WebService/Methods_SendSMS.aspx
		 */
		
		public function __construct($vars, $c){
		
			parent::__construct($vars, $c);
			
			$this->method = get_class();
			
			//$this->logcode = 2;
		}//__construct
		
		public function request(){
			
			parent::auth();
			
			
			
			$this->params->clientBillingReference = $this->vars['clientBillingReference'];
			$this->params->originator = $this->vars['originator'];
			$this->params->destinations = $this->vars['destinations'];
			$this->params->body = utf8_encode ( $this->vars['body'] );
			$this->params->validity = $this->vars['validity'];
			$this->params->replyMethodID = $this->vars['replyMethodID'];
			$this->params->replyData = $this->vars['replyData'];
			$this->params->statusNotificationUrl = $this->vars['statusNotificationUrl'];
			$this->params->characterSetID = $this->vars['characterSetID'];
			$this->params->clientMessageReference = $this->vars['clientMessageReference'];
			
			parent::clientMessageReference();
			
			parent::send();
			
			$this->log();
			
			
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
			
			$this->validateResponse();
			
			parent::error();
			
			
					
			
			
			
			
			
		}//xmlresponse
		
		private function validateResponse(){
		
			validator::loadDictionaries(array('TextAnywhereAPIResponse'));		
			validator::validateInput($this->result);
			$this->cleanData = validator::getCleanData();
			$errors = validator::getErrors();
		
			if(!empty($errors)){
				$this->logdb->logValidationErrors($this->result, $errors);
				exit();
			}
		
		}//validateResponse
		
		
		private function log(){
			if($this->live == true){
				$logf = new log_file('/api/textanywhere/');
				$logf->request($this->method, $this->vars, $this->count);
				$logf->response($this->method, $this->response, $this->count);
			}
		}//log
		
		
	}//sendSMS

?>