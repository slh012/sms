<?php

    class smsRegister extends tag{
        
        public function __construct($cleanData) {
            parent::__construct();
            
            
            
        }//constructor
        
		public function old(){
			$originator = $cleanData['originator'];
            $destination = $cleanData['destination'];
			$clientmessagereference = $cleanData['clientmessagereference'];
            $date = $cleanData['date'];
            $time = $cleanData['time'];
            $body = $cleanData['body'];
            $rbid = $cleanData['rbid'];

            $log = new log_database();
            $log->logTagActivation($cleanData);




            $send['returnCSVString'] = false;
            $send['clientBillingReference'] = '0';
            $send['clientMessageReference'] = '1'; // create new reference using primary key from database
            $send['originator'] = ORIGINATOR;
            $send['destinations'] = '';
            $send['validity'] = '1';
            $send['characterSetID'] = 2;
            $send['replyMethodID'] = 2;
            $send['replyData'] = config::get('ta_replyemail');	
            $send['StatusNotificationUrl'] = '';



            if(stristr($body, ORIGINATOR)){

                $body = trim(str_ireplace(ORIGINATOR, '', $body));

                $tags = explode(',',$body);

                //check PIN ID valid against expected format      
                $validity = parent::checkTagValidityBasic($tags);

                if($validity == false){


                    $send['body'] = 'One or more tags are not being recognised by our system please check and try again.';
                    utils::recursiveCall('sendSMS', $send, 1, $live);

                    $valid = false;



                }else{

                    //check PIN ID valid against database
                    $tagData = parent::checkTagValidityDatabase($tags);

                    if($tagData == false){ 
                        $send['body'] = 'There seems to be a problem please call XXXXXXX';
                        utils::recursiveCall('sendSMS', $send, 1, $live);
                        $valid = false;


                    }else{

                        $cost = $tagData['cost'];            

                        $send['body'] = 'Tags are valid and ready to be activated, text YES to proceed or STOP to cancel, text cost &pound;'.$cost.' for 3 month membership for paper tags. Plastic tags attract a years membership for FREE';
                        utils::recursiveCall('sendSMS', $send, 1, $live);

                        $valid = true;



                    }//else pins valid



                }//pins ok


            }elseif(stristr($body, 'YES')){

                //creat user account using existing function

                $username = '';
                $password = '';

                $send['body'] = 'Your account has been successfully created, please log in using username '.$username.' and password '.$password.' to enter your email address.';
                utils::recursiveCall('sendSMS', $send, 1, $live);

                $valid = true;

            }elseif(stristr($body, 'STOP')){
                //opt out

                //add recipient to suppression list. Use this list to ensure recipient is not contacted accidentily
                echo "add recipient to suppression list. Use this list to ensure recipient is not contacted accidentily";

                $valid = true;

            }else{
                //not a valid response. log details
                echo "not a valid response. log details";

                $valid = false;
            }

            debug::output($send['body']);
		}
        
        
    }
?>
