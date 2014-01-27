<?php
/*
 * Created: 10/10/2012
* Change Log:
*
*  Author: Sean Hardaker (seanhardaker@gmail.com)
*
*/

	class getSMSStatus extends textAnywhere{
		
		/*
		 * This method is used to retrieve the delivery status of one or more messages previously sent using the SendSMS method.
		 * http://developer.textapp.net/WebService/Methods_GetSMSStatus.aspx
		 */
		
		public function __construct($vars, $c){
		
			parent::__construct($vars, $c);
			
			$this->method = get_class();
			
			//$this->logcode = 2;
		}//__construct
		
		public function request(){
			
			parent::auth();
			
			$this->params->clientMessageReference = $this->vars['clientMessageReference'];
			
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
			$this->validateResponse();
			
			parent::error();
			
			
		}//xmlresponse
		
		private function validateResponse(){
		
			validator::loadDictionaries(array('TextAnywhereAPIResponse'));	
			foreach($this->result as $key => $val){
				if(is_array($val)){
		
					validator::validateInput($val);
					$this->cleanData = validator::getCleanData();
					$errors = validator::getErrors();
				
					if(!empty($errors)){
						$this->logdb->logValidationErrors($this->result, $errors);
						exit();
					}
				}
			}	
		}//validateResponse
		
		private function sendold(){
				
				
			
			
				
				
				$sc = $this->soap->open($this->soapPath);				

				
				
				$this->vars['method'] = $this->method;
				
				
				
				
				$method = ucfirst($this->method);
				/*echo "VARS: ";
				print_r($this->vars);
				echo "END VARS";*/
				validator::loadDictionaries(array(ucfirst($this->method)));		
				validator::validateInput($this->vars);
				$cleanData = validator::getCleanData();
				$errors = validator::getErrors();
			/*	echo "clean:";
				print_r($cleanData);
				print_r($errors);
				echo "END clean";*/
				if(empty($errors)){
				
				
					if($this->live == true){
						
						$result = $sc->__call($method, array($this->params));						
						
						
						$resultMethod = $method.'Result';//Dynamic method name dependent on the class name
						
						$this->response = $result->$resultMethod;
						
						$logf = new log_file('/api/textanywhere/');
						$logf->request($this->method, $this->vars, $this->count);
						$logf->response($this->method, $this->response, $this->count);
						
						
					}//if live
					
									
					
				
				}else{
					debug::output("There were errors in the data to be sent");
					
					$logdb->logValidationErrors($this->vars, $errors);
				}				
				
			
			
			
		}//send
		
		private function log(){
			if($this->live == true){
				$logf = new log_file('/api/textanywhere/');
				$logf->request($this->method, $this->vars, $this->count);
				$logf->response($this->method, $this->response, $this->count);
			}
		}//log
		
	}//sendSMS

?>