<?php
/*
 * Created: 17/9/2012
 * Change Log:
 * 
 *  Author: Sean Hardaker (seanhardaker@gmail.com)
 *  
 */

class checkLostBagEmail extends sms{
    
	private $old_lost_tags_notified;
	
    public function __construct($input, $live) {
	
        parent::__construct($input, $live);
        
		$sql = "TRUNCATE log_check_lost_bag_inbox_notified";
		mysql::i()->query($sql);		
		
        
        
        $host = config::get('sita_imap_host');
        $email_address = config::get('sita_imap_email_address');
        $password = config::get('sita_imap_password');
        
        $connection = $host.'~'.$email_address.'~'.$password;

        imap::i($connection);

        $mc = imap::i()->check();
        $result = imap::i()->fetch_overview("1:{$mc->Nmsgs}");
       
        if(!empty($result)){
            
            $this->old_lost_tags_notified = $this->getPreviousSITAEmailsNotified();
            $old_lost_tags_invalid = $this->getPreviousSITAEmailsInvalid();
            
            foreach ($result as $overview) {

                $message_body = imap::i()->fetch_body($overview->msgno);
 
				$message_body = utils::removeMultiSpaces($message_body);
                list($prefix, $tag, $iata, $world_tracer_ref) = explode(' ', $message_body);
                
				
				if(empty($tag)){
					debug::output("No tag!\n"); 
					continue;
				}
                //skip any tags already handled
                if($this->old_lost_tags_notified[$tag]){
					debug::output("$tag owner previously notified\n"); 
					continue;
				}
				
				debug::output("Checking tag format for tag {$tag}..."); 
                if(parent::checkTagFormat($tag) == true){

                    debug::output("$tag valid format"); 

                    
                    
                    //do one query to establish if tag exists in database and is associated with a customer 
                    $tag_registered = $this->getCustomer($tag);

                    //check that the tag is registered
                    if($tag_registered == true){
						
						
					
                        //Check has user got a registered email address ?
                        if(!empty($this->customer_details['email'])){                            
                            
                            //is the account 'active'?
                            if($this->customer_details['status'] == 'ACTIVE'){
                                debug::output("Tag Active. Notify user by email."); 
								
								$lostBag = utils::getAirportbyIATA($iata);  
								
                                //Owner recieves email containing the report

                                //****This mail function is for mailing ACTIVE Tag holder email****//
                                $mail = new Mail();
                                $mail->protocol = config::get('config_mail_protocol');
                                $mail->parameter = config::get('config_mail_parameter');
                                $mail->hostname = config::get('config_smtp_host');
                                $mail->username = config::get('config_smtp_username');
                                $mail->password = config::get('config_smtp_password');
                                $mail->port = config::get('config_smtp_port');
                                $mail->timeout = config::get('config_smtp_timeout');				

                                $mail->setTo($this->customer_details['email']);

                                $mail->setFrom('');                      
                                $mail->setSender('');

                                $mail->setSubject(html_entity_decode(sprintf('Lost Bag Report')));

                                
$report_foundbag_emailtext =
"Dear ". $this->customer_details['firstname'] ." <br />  

A lost bag report has been completed and your tag number has been reported.<br /><br />

Details of the find<br />
<b>Tag Number: OC </b>".$this->customer_details['tag_no']."<br />
<b>Name of Finder:</b>"." ".$this->customer_details['firstname']." ".$this->customer_details['lastname']."<br />
<b>Email Address of Finder:</b>"." ".$this->customer_details['email']."<br />
<b>Telephone Number of Finder:</b>"." ".$this->customer_details['telephone']."<br />
<b>Mobile Number:</b>"." ".$this->customer_details['mobile']."<br />
<b>Post Code:</b>"." ".$this->customer_details['postcode']."<br />
<b>Location of Bag:</b> {$lostBag['airport']}, {$lostBag['place']}, {$lostBag['state']}, {$lostBag['country']}<br /><br />

Sincerely</n>
Lost Baggage Team"                           
; 
                                $mail->setText(strip_tags($report_foundbag_emailtext));
                                //                        echo "<pre>";
                                //                        print_r($mail);
                                //                        die;
                                $mail->send();
                                // End mail for ACTIVE TagNo
                                
                                $this->logCheckLostBagTagInboxNotified($tag, $iata, $world_tracer_ref, true, 'email', $this->customer_details['email']);
                                
                                $this->logDB->logCheckLostBagEmail($this->customer_details['customer_id'], strip_tags($report_foundbag_emailtext), $this->customer_details['email'], $status, $tag);
                                
                            }//email active
                            else{
                                //not active
                                debug::output("$tag inactive. Notify user by email."); 

                                /* Owner recieves an email to ask them to contact  
                                 * who will provide them with the information once they 
                                 * have paid a fee. 
                                 * (Would like to automate this so email is kept in 
                                 * user's acocunt until they have paid a premium of 
                                 * £15.00 then they can view the report)*/

                                //****This mail function is for mailing ACTIVE Tag holder email****//
                                $mail = new Mail();
                                $mail->protocol = config::get('config_mail_protocol');
                                $mail->parameter = config::get('config_mail_parameter');
                                $mail->hostname = config::get('config_smtp_host');
                                $mail->username = config::get('config_smtp_username');
                                $mail->password = config::get('config_smtp_password');
                                $mail->port = config::get('config_smtp_port');
                                $mail->timeout = config::get('config_smtp_timeout');				

                                $mail->setTo($this->customer_details['email']);

                                $mail->setFrom('');                      
                                $mail->setSender('');

                                $mail->setSubject(html_entity_decode(sprintf('Lost Bag Report')));

                                //print_r($this->customer_details);
$report_foundbag_emailtext =
"Dear ". $this->customer_details['firstname'] ." <br />  
A lost bag report has been completed and your tag number has been reported.<br /><br />                            
But we are sorry, your tag is not ACTIVE please call ".config::get('lost_bag_number')." to reactivate your tag.<br /><br />

Sincerely</n>
Lost Baggage Team"               
; 
                                $mail->setText(strip_tags($report_foundbag_emailtext));
                                //                        echo "<pre>";
                                //                        print_r($mail);
                                //                        die;
                                $mail->send();
                                // End mail for ACTIVE TagNo
                                
                                $this->logCheckLostBagTagInboxNotified($tag, $iata, $world_tracer_ref, true, 'email', $this->customer_details['email']);
                                
                                $this->logDB->logCheckLostBagEmail($this->customer_details['customer_id'], strip_tags($report_foundbag_emailtext), $this->customer_details['email'], $status, $tag);


                            }//else                      

                        }//email registered
                        else{
                            //not registered
                            debug::output("$tag has no registered email address "); 

                            //is the account 'active'?                          
                            if($this->checkTagNoStatus($tag) == 'ACTIVE'){
                                 debug::output("$tag active\n"); 


                                //Owner receives SMS to say a report is waiting for you, please text YES to receive the details, text costs £1.50 or NO to cancel
                               
                                $this->send['destinations'] = utils::formatPhoneNumber($this->customer_details['telephone']);
								
								$this->logCheckLostBagTagInboxNotified($tag, $iata, $world_tracer_ref, true, 'sms', $this->send['destinations']);
								
                                $this->send['body'] = 'Lost bag report available for tag '.$tag.'. For details text PIN REPORT. Text costs £1.50 each plus standard network rate. Text PIN STOP to cancel.'; 
								

                                parent::sendSMS();
                                

                            }//tag active
                            else{
                                 debug::output("$tag inactive\n"); 
                                //not active

                                /*user recieves text to say account not active please contact
                                 * directly on [telephone number] potentially we automate this and 
                                 * have them go online to pay the fee before receiving the info - 
                                 * your thoughts would be helpful*/

                                $this->send['destinations'] = utils::formatPhoneNumber($this->customer_details['telephone']);
                                $this->send['body'] = 'Lost bag report available for EXPIRED tag '.$tag.'. Please contact customer services on '.config::get('lost_bag_number').' Text PIN STOP to cancel.';  
							


                                parent::sendSMS();
                                $this->logCheckLostBagTagInboxNotified($tag, $iata, $world_tracer_ref, true, 'sms', $this->send['destinations']);

                            }//else          



                        }//else not registered
                    }else{
                        debug::output("$tag not registered to customer in database");    
						if(!$old_lost_tags_invalid[$tag]){
							$this->logDB->logCheckLostBagInvalidTag($tag); //only log the tag once
						}
                        
                    }

                }//tag valid format
                else{
                    debug::output("$tag invalid format\n"); 
                    $this->logDB->logCheckLostBagInvalidTag($tag, $old_lost_tags_invalid);
                    
                }
				unset($this->send);
            }//for each message
        }
        else{
             debug::output("No messages in Inbox\n");    
        }//empty
        
        imap::i()->close();

        
    }//constructor
    
    private function getCustomer($tag){
		$sql = "SELECT c.*, a.*, r.* FROM tag_registered r left join customer c using (customer_id) left join address a using (customer_id) WHERE r.tag_no = '" . $tag . "' ";
		
		$q = mysql::i()->query($sql);
		$r = mysql::i()->fetch_array($q);
		$this->customer_details = $r;//saves doing this agane later when we need the customer details for email
		if(!empty($r)){
			return true;
		}else{
			return false;
		}
	}
	
	private function getPreviousSITAEmailsNotified(){
		$sql = "SELECT * FROM log_check_lost_bag_inbox_notified where notified = true \n";
		$q = mysql::i()->query($sql);
		while($r = mysql::i()->fetch_object($q)){
			$array[$r->tag_no] = $r->tag_no;
		}
		
		return $array;
	}

	private function getPreviousSITAEmailsInvalid(){
		$sql = "SELECT * FROM log_check_lost_bag_invalid_tag  \n";
		$q = mysql::i()->query($sql);
		while($r = mysql::i()->fetch_object($q)){
			$array[$r->tag_no] = $r->tag_no;
		}
		
		return $array;
	}
    
	private function checkTagNoStatus ($tag){
		$sql = "SELECT * FROM tag_registered WHERE tag_no = '" . $tag . "' ";
	   
		$q = mysql::i()->query($sql);
		$r = mysql::i()->fetch_array($q);
		if(!empty($r['status'])){
			return $r['status'];
		}else{
			return;    
		}
		
	}
	
	private function logCheckLostBagTagInboxNotified($tag_no, $iata, $world_tracer_ref, $notified, $method, $contact_details){	
		$sql = "SELECT * FROM log_check_lost_bag_inbox_notified where tag_no = '{$tag_no}' ";
		$q = mysql::i()->query($sql);
		if(mysql::i()->num_rows($q)==0){
			$sql = "INSERT INTO log_check_lost_bag_inbox_notified (tag_no, iata, world_tracer_ref, notified, method, contact_details, datetime) VALUES ('{$tag_no}', '{$iata}', '{$world_tracer_ref}', {$notified}, '{$method}', '{$contact_details}' ,now() ) \n";
			mysql::i()->query($sql);
			$this->old_lost_tags_notified[$tag_no] = $tag_no;//in case of multiple reports for one tag in the inbox
			return mysql::i()->last_id();
		}
    }//logCheckLostBagTagInboxNotified
    
}//checkLostBagEmail
?>
