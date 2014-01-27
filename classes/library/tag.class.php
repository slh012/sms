<?php

    class tag{
        
		protected $log;
		
		public $tagData;
		
		public $invalid_tags;
		public $valid_tags;
		
        protected $customer_details;
        
        public function __construct() {
		

        }
        
		public function getLostBagLocationUsingTag($tag_no){
			$sql = "SELECT * FROM log_check_lost_bag_inbox_notified LEFT JOIN airports USING (iata) WHERE tag_no = '{$tag_no}' \n";
			$q = mysql::i()->query($sql);
			if(mysql::i()->num_rows($q)==1){
				return mysql::i()->fetch_array($q);
			}else{
				while($r = mysql::i()->fetch_array($q)){
					if($r['country']=='USA')continue;					
					$array = $r;
				}
				
				return $array;
			}
		}
		
		public function getLostBagLocationUsingID($check_lost_bag_inbox_id){
			$sql = "SELECT * FROM log_check_lost_bag_inbox_notified LEFT JOIN airports USING (iata) WHERE log_check_lost_bag_inbox_id = '{$check_lost_bag_inbox_id}' \n";
			$q = mysql::i()->query($sql);
			if(mysql::i()->num_rows($q)==1){
				return mysql::i()->fetch_array($q);
			}else{
				while($r = mysql::i()->fetch_array($q)){
					if($r['country']=='USA')continue;					
					$array = $r;
				}
				
				return $array;
			}
			
		}
		
		public function tagAlreadyRegistered($tags, $phone_number, $live){
			$registered = true;
			if(is_array($tags)){
                foreach($tags as $tag){					
                    $sql = "SELECT * FROM tag_registered WHERE tag_no='{$tag['tag_no']}' \n";
					$q = mysql::i()->query($sql);
					if(mysql::i()->num_rows($q)==0){									
						$registered = false;						
						$this->valid_tags[] = $tag;
					}else{
					
						if($registered != false){	
							$send['destinations'] = $phone_number;
							echo $send['body'] = 'Tag '.$tag['tag_no'].' is already registered. Please check and try again or call '.config::get('lost_bag_number');				
							utils::recursiveCall('sendSMS', $send, 1, $live);				
							$registered = true;
						}
					}
                }//foreach                        
            }else{                
				$sql = "SELECT * FROM tag_registered WHERE tag_no='{$tags}' \n";
				$q = mysql::i()->query($sql);
				if(mysql::i()->num_rows($q)==0){
					$registered = false;
					$this->valid_tags[]['tag_no'] = $tags;
					
				}else{
					$registered = true;
					$send['destinations'] = $phone_number;
					echo $send['body'] = 'Tag '.$tags.' is already registered. Please check and try again or call '.config::get('lost_bag_number');				
					utils::recursiveCall('sendSMS', $send, 1, $live);
				}
            }
			return $registered;		
		}
		
		public function getLostBagLocationUsingOriginator($originator){
			$sql = "SELECT * FROM log_check_lost_bag_inbox_notified LEFT JOIN airports USING (iata) WHERE contact_details = '{$originator}' \n";
			$q = mysql::i()->query($sql);
			if(mysql::i()->num_rows($q)==1){
				return mysql::i()->fetch_array($q);
			}else{
				while($r = mysql::i()->fetch_array($q)){
					if($r['country']=='USA')continue;					
					$array = $r;
				}
				
				return $array;
			}
			
		}
		
		public function getValidTags($phone_number){
			echo $sql = "SELECT * FROM log_recognised_tags WHERE phone_number = '{$phone_number}' AND registered=false \n";
			$q = mysql::i()->query($sql);
			while($r = mysql::i()->fetch_array($q)){
				$array[] = $r;
			}
			return $array;
		}
		
		public function getTags($clientMessageReference){
		//do not use
			$sql = "SELECT tag_nos FROM log_client_msg_ref_tag WHERE clientMessageReference='{$clientMessageReference}'";
			$q = mysql::i()->query($sql);
			$r = mysql::i()->fetch_array($q);
			return $r['tag_nos'];
		}
		
		public function getClientMessageReference($data){
		//do not use
			$q = mysql::i()->query('show columns from log_client_msg_ref_tag');
					
			while($r=mysql::i()->fetch_object($q))
			{
				$fields[]=$r->Field;
			}
			foreach($data as $key => $value){
				if(in_array($key,$fields))
				{
					$cols.="$key,";
					$values.="'".mysql::i()->escape_string($value)."',";				
				}
			}
			$cols=substr($cols,0,-1);
			$values=substr($values,0,-1);
					
			$sql = "INSERT INTO log_client_msg_ref_tag ($cols) VALUES ($values)";
			mysql::i()->query($sql);
			return mysql::i()->last_id();
			
		}
		
        protected function getPreviousSITAEmailsNotified(){
            $sql = "SELECT * FROM log_check_lost_bag_inbox_notified where notified = true \n";
            $q = mysql::i()->query($sql);
            while($r = mysql::i()->fetch_object($q)){
                $array[$r->tag_no] = $r->tag_no;
            }
            
            return $array;
        }

        protected function getPreviousSITAEmailsInvalid(){
            $sql = "SELECT * FROM log_check_lost_bag_invalid_tag  \n";
            $q = mysql::i()->query($sql);
            while($r = mysql::i()->fetch_object($q)){
                $array[$r->tag_no] = $r->tag_no;
            }
            
            return $array;
        }
        


        protected function getCustomer($tag_no){
            $sql = "SELECT c.*, a.*, r.* FROM tag_registered r left join customer c using (customer_id) left join address a using (customer_id) WHERE r.tag_no = '" . $tag_no . "' ";
            
            $q = mysql::i()->query($sql);
            $r = mysql::i()->fetch_array($q);
            $this->customer_details = $r;//saves doing this agane later when we need the customer details for email
            if(!empty($r)){
                return true;
            }else{
                return false;
            }
        }
        
        protected function checkTagNoStatus ($tag_no){
            $sql = "SELECT * FROM tag_registered WHERE tag_no = '" . $tag_no . "' ";
           
            $q = mysql::i()->query($sql);
            $r = mysql::i()->fetch_array($q);
            if(!empty($r['status'])){
                return $r['status'];
            }else{
                return;    
            }
            
        }
        
        


        private function checkTagFormat($tag){
			$tag = trim($tag);
            
            
            $nonAlphaNumeric = utils::stripAlphaNumeric($tag_no);
			echo "if(strlen($tag_no) != 16 || !empty($nonAlphaNumeric)){";
            if(strlen($tag_no) != 16 || !empty($nonAlphaNumeric)){
                return false;
            }//if
            else{
                return true;
            }//else
        }

        public function checkTagValidityBasic($tags, $phone_number, $live){
			print_r($tags);
            if(is_array($tags)){
                foreach($tags as $tag){
                    if(self::checkTagFormat($tag)==false){
					//if invalid
						$send['destinations'] = $phone_number;
						echo $send['body'] = 'Tag '.$tag .' is not in the correct format. Please check and try again or call '.config::get('lost_bag_number');				
						utils::recursiveCall('sendSMS', $send, 1, $live);
                        $valid = false;
						$this->invalid_tags[$tag] = $tag;
                    }else{
					//if valid
						$this->valid_tags[$tag] = $tag;
					}

                }//foreach                        
            }else{
				if(self::checkTagFormat($tags)==false){
					$valid = false;
					$this->invalid_tags[$tag] = $tag;
				}else{
					$this->valid_tags[$tag] = $tag;
				}
            }                    

            return $valid;
        }//checkPinValidityBasic

		
		
        public function checkTagValidityDatabase($tags, $phone_number, $live){ 
			
            foreach($this->valid_tags as $tag){
                $tag_no = utils::extractTagNo($tag);  
                echo $sql = "SELECT * FROM group_tags WHERE tag = '{$tag_no}' \n";
                $q = mysql::i()->query($sql);
                $r = mysql::i()->fetch_array($q);
               


                if(empty($r)){
					unset($this->valid_tags[$tag]);
					$this->invalid_tags[$tag] = $tag;
					$send['destinations'] = $phone_number;
					echo $send['body'] = 'Tag '.$tag.' is not recognised by our system please. Please check and try again or call '.config::get('lost_bag_number');				
					utils::recursiveCall('sendSMS', $send, 1, $live);
					$valid = false;
                }
				
				if($r['action']=='USED'){
				
					unset($this->valid_tags[$tag]);
					$this->invalid_tags[$tag] = $tag;
					$send['destinations'] = $phone_number;
					echo $send['body'] = 'Tag '.$tag.' is already in use. Please check and try again or call '.config::get('lost_bag_number');				
					utils::recursiveCall('sendSMS', $send, 1, $live);
					$valid = false;
				}

                $type = $r['tag_type'];

                if($type == 'paper'){
                    $cost += '1.50';
                }else{
                    $cost = 0;
                }
				
				$this->valid_tags[$tag] = $r;
				$this->valid_tags[$tag]['cost'] = $cost;
                //$this->tagData[$tag]['type'] = $type;
                //$this->tagData[$tag]['authorised'] = 1;
				unset($cost);
            }//foreach

            //$this->tagData['cost'] = $cost;

            return $valid;

        }//checkPinValidityDatabase
    }
?>
