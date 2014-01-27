<?php
/*
 * Created: ??/6/2012
 * Change Log:
 *
 *  Author: Sean Hardaker (seanhardaker@gmail.com)
 *
 */

	class testService extends textAnywhere{
		
		/*
		 * Query the current status of the SMS Gateway
		 * http://developer.textapp.net/WebService/Methods_TestService.aspx
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
			
			print_r($this->result);
			
			if($this->result['Code'] >= 2) $this->fault = true;
			
			
		}//xmlresponse
		
		
		
	}//testService

?>