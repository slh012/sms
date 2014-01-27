<?php
/*
 * Created: ??/6/2012
 * Change Log:
 * 
 *  Author: Sean Hardaker (seanhardaker@gmail.com)
 *  
 */


	class textAnywhere{
		
		/*
		 * This is the parent class for the methods developed for the Text Anywhere API
		 */
		
		/*
		 * Type: array
		 * Purpose: Holds the parameters passed to the API
		 */
		public $vars;
		
		
		/*
		 * Type: boolean
		 * Purpose: Is set to true when the API returns a fault 
		 */
		public $fault;
		
		
		/*
		 * Type: object
		 * Purpose: xml parser
		 */
		protected $xpath;
		
		
		/*
		 * Type: object
		 * Purpose: csv parser
		 */
		protected $csvparser;
		
		
		/*
		 * Type: array
		 * Purpose: holder the result from the API reponse XML
		 */
		protected $result;
		
		
		/*
		 * Type: object
		 * Purpose: used to make the response to the API
		 */
		protected $soap;
		
		
		/*
		 * Type: object
		 * Purpose: contains the parameters sent to the API
		 */
		protected $params;
		
		
		/*
		 * Type: string
		 * Purpose: contains the class name which corresponds to the method name for the API
		 */
		protected $method;
		
		
		/*
		 * Type: string
		 * Purpose: holds the response from the API. Can be either XML or CSV
		 */
		protected $response;
		
		
		/*
		 * Type: int
		 * Purpose: holds the rules for when to record the response from the API
		 * Definition: 0 == never, 1 == always, 2 == if fault
		 */
		protected $logcode; 
		
		
		
		/*
		 * Type: string
		 * Purpose: holds the url for the API when interfacing using the web service
		 */
		protected $soapPath;
		
		
		/*
		 * Type: boolean
		 * Purpose: set to true when we want to interigate the live api. If false the system will use the local xml files for a response
		 */
		protected $live;
		
		
		/*
		 * Type: object
		 * Purpose: contains the database log instance
		 */
		protected $logdb;
		
		
		/*
		 * Type: array
		 * Purpose: holder the clean result from the API reponse XML
		 */
		protected $cleanData;
		
		
		/*
		 * Type: int
		 * Purpose: countains the number of times this call has been attempted
		 */
		protected $count;
		
		
		/*
		 * Type: string
		 * Purpose:  This is the unique message reference that you will use when sending a message to enable you to later retrieve the message’s delivery status.
		 */
		protected $clientMessageReference;
		
		
		
		
		
		public function __construct($vars, $c){
			
			$this->count = $c;
			
			$this->soap = new soap();
			
			$this->logdb = new log_database();
			
			$this->soapPath = '';
			
			$this->live = LIVE;	
			
			$this->vars = $vars;
								
			$this->vars['externalLogin'] = config::get('ta_externalLogin');
			
			$this->vars['password'] = config::get('ta_password');
			
			$this->vars['clientBillingReference'] = 0;
			
			$this->vars['validity'] = 1;
			
			$this->vars['characterSetID'] = 2;
                        
            $this->vars['returnCSVString'] = false;            
                        
			$this->vars['replyMethodID'] = 7;
			
			$this->vars['replyData'] = '';
			
			$this->vars['statusNotificationUrl'] = '';
			
			$this->vars['originator'] = '';//shortcode needs to be setup in textanywhere portal
			
		}//__construct
		
		private function checkSuppressionList($originator){
			$originator = utils::formatPhoneNumber($originator);
			$sql = "SELECT * FROM log_sms_suppression_list WHERE originator='{$originator}' \n";
			$q = mysql::i()->query($sql);
			$rows = mysql::i()->num_rows($q);
			if($rows > 1){
				return true;
			}else{
				return false;
			}
		}
		
		public function response(){
			($this->vars['returnCSVString']) ?  $this->csvresponse() : $this->xmlresponse();
		}//response
		
		private function testxml(){
                       
			return file_get_contents('/sms/classes/api/textanywhere/testresponses/'.strtolower($this->method).'.xml');		
			
		}//testxml
		
		
		protected function xpath($xml){								
			$this->xpath = new xpath(($this->live) ? $xml : $this->testxml());//note the conditional statement
		}//xpath
		
		
		protected function csvparser($csv){
			$this->csvparser = new csvparser($csv);
		}//csvparser
		
		public function auth(){
			
			($this->vars['returnCSVString']) ?  debug::output('Expecting CSV Response...') : debug::output('Expecting XML Response...');
			
			$this->params = new stdClass();
			
			$this->params->externalLogin = $this->vars['externalLogin'];
			$this->params->password = $this->vars['password'];
			$this->params->returnCSVString = $this->vars['returnCSVString'];
			
		}//auth
		
		protected function clientMessageReference(){
			
			$this->clientMessageReference = $this->logdb->logOutboundSMS($this->params);
			$this->vars['clientMessageReference'] = $this->clientMessageReference;
			$this->params->clientMessageReference = $this->clientMessageReference;	
		}
		
		protected function send(){
				
				
			
			
				
				
				$sc = $this->soap->open($this->soapPath);				

				
				
				$this->vars['method'] = $this->method;
				
				
				
				$method = ucfirst($this->method);
				
				validator::loadDictionaries(array(ucfirst($this->method)));		
				validator::validateInput($this->vars);
				$cleanData = validator::getCleanData();
				$errors = validator::getErrors();
				
				if(empty($errors)){
				
				
					if($this->live == true){
						
						$result = $sc->__call($method, array($this->params));	 	
						
						if(!empty($this->vars['body'])){
							debug::output('***************LIVE SENDING START***************');
							debug::output($this->vars['body']);
							debug::output('***************LIVE SENDING END***************');	
						}
						$resultMethod = $method.'Result';//Dynamic method name dependent on the class name
						
						$this->response = $result->$resultMethod;
						
						sleep(10);//give the api time to report the message status
						
						debug::output("\n");
						
						debug::output('***************LIVE CHECK DELIVERY START***************');
						$smsstatus = new smsstatus($this->vars);
						$this->fault = $smsstatus->database();						
						debug::output('***************LIVE CHECK DELIVERY END***************');	
						
						
						
					}//if live
					else{
						//else dev
						
						//log for DEV
						//print_r($this->params);
						//$this->logdb->updateLogOutboundSMS($this->params); 
						
						if(!empty($this->vars['body'])){
							debug::output('***************LIVE SENDING START***************');
							debug::output($this->vars['body']);
							debug::output('***************LIVE SENDING END***************');	
						}
						
						
						
					}//else dev					
					
									
					
				
				}else{
					debug::output("There were errors in the data to be sent");
					
					$logdb->logValidationErrors($this->vars, $errors);
				}				
				
			
			
			
		}//send
		
		
		protected function error(){
			
			if($this->cleanData['Code'] >= 2 || $this->fault == true){
				//unsucessful send
			
				$this->fault = true;				
				
				
				$this->logdb->logAPISendErrors($this->vars, $this->cleanData['Code'], $this->cleanData['Description']);
									
					
			}else{
				//sucessful send
				
				$this->fault = false;
				
				$this->logdb->updateLogOutboundSMSSentTrue($this->clientMessageReference);//sets the sent SMS flag to true
			
			}//else		
					
			
		
		}//error
		
		protected function csvresponse(){
				
			$this->csvparser($this->response);
		
			print_r($this->csvparser->result);
				
			debug::output('This function has not been developed.');
				
			/*
			 * Not currently developed and included only if CSV functionality is required in the future
			 * If developed move this function into the relevant class.
			 * 
			 */
		
		}//csvresponse
		
	}//textAnywhere

?>