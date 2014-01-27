<?php

    class tagExpiryReminder extends tag{
        public function __construct() {
            //$this->log = new log_database();
        }
        
        public function fetchTags(){
			$sql = "SELECT c.*, t.* from tag_registered t left join customer c using (customer_id) WHERE c.customer_id<>'' AND t.status = 'ACTIVE' AND t.is_deleted IS NULL AND date_expire <= DATE_ADD(UTC_DATE(),INTERVAL 31 DAY) ";
			//$sql = "SELECT c.*, t.* from tag_registered t left join customer c using (customer_id) WHERE (c.telephone<>'' OR c.email<>'') AND t.status = 'ACTIVE' AND t.is_deleted IS NULL AND t.date_expire < DATE_SUB(UTC_DATE(),INTERVAL 31 DAY) ";
            $q = mysql::i()->query($sql);

            while ($row = mysql::i()->fetch_array($q)) {
			
                $status = $this->getStatus($row);

                $oneweek_periordate = $this->expiryDate($row, '+1 week', '1 week');

                $twoweek_periordate = $this->expiryDate($row, '+2 week', '2 weeks');

                $onemonth_periordate = $this->expiryDate($row, '+1 month', '1 month');

          

                print_r($status);

         

            }//while
        }//fetchtags
        
        
        //INACTIVE tags

        private function getStatus($row) {
			
			 
			
			
			
            $todays_date = date("Y-m-d");

           

            $expiration_date = ($row['date_expire']);
			
			
            if ($expiration_date <= $todays_date) {
                 //print_r($row);
				
                //mysql::i()->query("UPDATE tag_registered SET status = 'INACTIVE' WHERE tag_id='$tag_id' ");
                			
                if ($row['customer_id']) {                   
					
					if(!empty($row['email'])){
					
						$this->sendEmailtoCustomer($row['email'], $row['firstname'], $row['lastname'], $row['tag_no'], 'INACTIVE');
						
					}elseif(!empty($row['telephone'])){
						
						$this->sendSMStoCustomer($row, 'INACTIVE');
						
					}else{
						debug::output('No means of contacting customer '.$row['customer_id']);
						//status::status(WARNING);
					}
                   
                }
				else{
					debug::output("No Customer ID");
					return false;
				}
                
			} else {
				
				return false;

			}

        }

        
        private function expiryDate($row, $duration, $msg_duration) {



            $todays_date = date("Y-m-d");

            $date = strtotime(date("Y-m-d", strtotime($todays_date)) . " {$duration}");

            $expirydate = date('Y-m-d', $date);   
			
			list($year, $month, $day) = explode ('-',$expirydate);
					
			$expirydateformat = date('jS M Y', mktime(0,0,0, $month, $day, $year));
			
			if ($row['date_expire'] == $expirydate) {
           			
				if ($row['customer_id']) {                   
						
					if(!empty($row['email'])){
					
						$this->sendEmailtoCustomer($row['email'], $row['firstname'], $row['lastname'], $row['tag_no'], 'DURATION', $expirydateformat);
						
					}elseif(!empty($row['telephone'])){
					
						
						
						$this->sendSMStoCustomer($row, 'DURATION', $expirydateformat);
						
					}else{
						debug::output('No means of contacting customer '.$row['customer_id']);
						//status::status(WARNING);
					}
				   
				}
				else{
					debug::output("No Customer ID");
				}
			}
			else{
				return false;
			}
            
        }//End One week
        
        private function sendSMStoCustomer($row, $message, $expirydate=''){
			
			$tag_type = strtolower(trim($row['tag_type']));
		
            debug::output("SMS: {$row['telephone']}, {$row['tag_type']}");
			
            //$clientMessageReference = $thos->log->logSMS('sendSMS', $telephone);
            
            
            $send['destinations'] = utils::formatPhoneNumber($row['telephone']);               
          
			switch($message){
				case "INACTIVE":
                    switch($tag_type){
						case"paper":
							$send['body'] = "Tag {$row['tag_no']} has expired. Text PIN RENEW {$row['tag_no']} to renew for 3 months, or text PIN CANCEL {$row['tag_no']}. Text cost £1.50";
							break;
						case "plastic":
							$send['body'] = "Tag {$row['tag_no']} has expired. Text PIN RENEW {$row['tag_no']} to renew for 12 months, or text PIN CANCEL {$row['tag_no']}. Text cost £3.00";
							//$send['body'] = 'Text YES to renew for 12 months, text cost £tbc, text NO to cancel';
							break;				
						default;
					}
                break;
                case "DURATION":
                    switch($tag_type){
						case"paper":
							$send['body'] = "Tag {$row['tag_no']} will expire on {$expirydate}. Text PIN RENEW {$row['tag_no']} to renew for 3 months, or text PIN CANCEL {$row['tag_no']}. Text cost £1.50";
							//$send['body'] = 'Text YES to renew for 3 months, text cost £1.50, text NO to cancel';
							break;
						case "plastic":
							$send['body'] = "Tag {$row['tag_no']} will expire on {$expirydate}. Text PIN RENEW {$row['tag_no']} to renew for 12 months, or text PIN CANCEL {$row['tag_no']}. Text cost £3.00";
							//$send['body'] = 'Text YES to renew for 12 months, text cost £tbc, text NO to cancel';
							break;				
						default;
					}
                break;
                default;
			}
            
            
			
            utils::recursiveCall('sendSMS', $send, 1, $live);
			
            
        }
        

       
        private function sendEmailtoCustomer($customer_email, $firstname, $lastname, $tag_no, $message, $msg_duration='') {
			debug::output("Email: $customer_email, $firstname, $lastname, $tag_no, $message, $msg_duration");
			
            // multiple recipients

            $to = $customer_email . ', '; // note the comma

            //$to .= 'wez@example.com';

            // subject

            $subject = 'Tag Membership Team - Reminder';

            // message        
            switch($message){
                case "INACTIVE":
                    $message = $this->messageBodyInactive($firstname, $lastname, $tag_no);
                break;
                case "DURATION":
                    $message = $this->messageBodyDuration($firstname, $lastname, $tag_no, $msg_duration);
                break;
                default;
            }            

        

            $headers = 'MIME-Version: 1.0' . "\r\n";

            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
          

            $headers .= 'To: ' . $to . "\r\n";

            $headers .= 'From: foundabag@example.com <foundabag@example.com>' . "\r\n";

           

            // Mail it

            mail($to, $subject, $message, $headers);

        }
        
        private function messageBodyInactive($firstname, $lastname, $tag_no){
             $message = '

            <html>

            <body>

              Dear ' . $firstname . ' ' . $lastname . ' Your tag <b>' . $tag_no . '</b> has expired and is no longer active on our system<br />

              which means you will not be notified if the tag is found.<br /> 

              Please renew your membership by clicking <a href="http://example.com/index.php?route=account/tag_no" target="_blank">here</a> <br /><br />



              Regards Tag Membership Team

            </body>

            </html>

            ';
             return $message;
        }
        
        private function messageBodyDuration($firstname, $lastname, $tag_no, $msg_duration){
            $message = '

            <html>

            <body>

              Dear ' . $firstname . ' ' . $lastname . ' Your tag <b>' . $tag_no . '</b> is due to expire in '. $msg_duration .',<br /> 

              after which it will no longer be active on our system which means you will not be notified if the tag is found. <br />

              Please renew your membership by clicking <a href="http://example.com/index.php?route=account/tag_no" target="_blank">here</a> <br /><br />



              Regards Tag Membership Team

            </body>

            </html>

            ';
            return $message;
        }

        // One Week perior Customer

       

        private function fetchTag($customer_id){
             $sql = "SELECT tag_no from tag_registered WHERE tag_id='{$customer_id}' \n";
			 $q = mysql::i()->query($sql);
             $tag = mysql::i()->fetch_object($q);
             return $tag;
        }

        private function fetchCustomer($customer_id){
            $q = mysql::i()->query("SELECT * from customer WHERE customer_id='{$customer_id}' ");
            $customer = mysql::i()->fetch_object($q);       
            return $customer;
        }
        
        private function fetchCustomerId($tag_id, $expirydate){
            echo $sql = "SELECT * from tag_registered WHERE tag_id='{$tag_id}' AND date_expire = '{$expirydate}' \n";
			$result = mysql::i()->query($sql);
            $r = mysql::i()->fetch_object($result);
            return $r;
        }
        
    }//tagExpiryReminder
?>
