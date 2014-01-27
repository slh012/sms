<?php
	class customer{
		
		public $customer_id;
		
		public $customer;
		private $logDB;
		
		public function __construct($logDB){
			$this->logDB = $logDB;
		}
		
		
		
		public function createCustomerAccount($phone_number){
			
			$username = $this->createRandomUsername();
		
			$pw = $this->createRandomPassword();
			
			$password = $pw['password'];
			$password_hash = $pw['password_hash'];
			
			if($this->checkAccountExists($phone_number)==true){			
				if(empty($this->customer['email'])){
					//account exists without an email address
					$return['message'] = 'Account already exists. Please login to www.example.com using temp username: '.$this->customer['username'].' to add a valid email address.';
				}else{
					//account exists with an email address
					$return['message'] = 'Account already exists using email address: '.$this->customer['email'];	
				}
				
				$return['success'] = true;
			}						
			elseif($this->createNewAccount($username, $password_hash, $phone_number)){

				$return['message'] = 'Account created. Please login to www.example.com using username: '.$username.' and password: '.$password.' to add a valid email address.';
				$return['success'] = true;
			}else{
				$return['message'] = 'There was a problem setting up your account. Call '.config::get('customer_services').' to resolve';
				$return['success'] = false;
			}		
			
			return $return;
			
		}//createCustomerAccount
		
		public function checkAccountExists($telephone){
			$sql = "SELECT * FROM customer WHERE telephone = '{$telephone}'";
			$q = mysql::i()->query($sql);
			if(mysql::i()->num_rows($q)==0){
				return false;
			}else{
				$this->customer = mysql::i()->fetch_array($q);
				$this->customer_id = $this->customer['customer_id'];				
				return true;
			}
		}
		
		public function createNewAccount($username, $password, $phone_number){
			
			$ip = utils::getIP();
			
			
			
			$sql = "INSERT INTO customer SET username= '{$username}', store_id = '0', telephone = '{$phone_number}', password = '{$password}', customer_group_id = '8', status = '1', approved = '1', ip = '{$ip}', date_added = NOW() \n";
			mysql::i()->query($sql);

			$this->customer_id = mysql::i()->last_id();
			
			if(empty($this->customer_id)){
				return false;
			}
			$sql = "INSERT INTO address SET customer_id = '" . (int) $this->customer_id . "' \n";
			mysql::i()->query($sql);
			
			return true;

		}
		
		private function createRandomEmail(){
	
			$email = utils::createRandString();
			
			$email .= '@example.com';
			
			return $email;
			
		}//createRandomEmail
		
		private function createRandomUsername(){
			
			return $this->recursiveUsername();
			
		}//createRandomUsername
		
		private function recursiveUsername($username=''){
			
			$username = utils::createRandString();		
			$sql = "SELECT * FROM customer WHERE username='{$username}' \n";
			$q = mysql::i()->query($sql);
			if( mysql::i()->num_rows($q)==0){
				$username = strtolower($username);
				return $username;
			}else{				
				return $this->recursiveUsername($username);
			}
			
		}
		
		private function createRandomPassword(){
			
			$password = utils::createRandString();
			$password = strtolower($password);
			
			$password_hash = Bcrypt::hash($password);
			
			$pw['password'] = $password;
			$pw['password_hash'] = $password_hash;
			
			return $pw;
		}
		
		private function getTagsFromGroup(tag){
			$sql = "SELECT * FROM group_tags WHERE tag='{$tag}' \n";
			$q = mysql::i()->query($sql);
			return mysql::i()->fetch_array($q);
			
		}
		
		private function tagExpireDate($validity_period){
		echo "<b>{$validity_period}</b>";
			$period = utils::stripNumeric($validity_period);
			
			$duration = utils::stripAlphabetic($validity_period);
			
			switch(strtolower(trim($period))){
				case "month":
				case "months":
					$date_expire = date('Y-m-d', mktime(0,0,0,date('m')+$duration,date('d'),date('Y')));
				break;
				case "day":
				case "days":
					$date_expire = date('Y-m-d', mktime(0,0,0,date('m'),date('d')+$duration,date('Y')));
				break;
				case "year":
				case "years":
					$date_expire = date('Y-m-d', mktime(0,0,0,date('m'),date('d'),date('Y')+$duration));
				break;
				default;
					$date_expire = '1900-01-01';
					debug::output("Expiry period Unknown");
			}//end switch
			return $date_expire;
			
		}
		
		
		
		public function registerTag($tags){
			
			if(is_array($tags)){
                foreach($tags as $tag){
					$t = self::getTagsFromGroup($tag['tag_no']);
					$sql = "SELECT * FROM tag_registered WHERE tag_no = '{$t['tag']}' \n";
					$q = mysql::i()->query($sql);
					if(mysql::i()->num_rows($q) == 0){					
						$date_expire = self::tagExpireDate($t['validity_period']);
						echo $sql = "INSERT INTO tag_registered (tag_no, customer_id, tag_type, date_registered, validity_period, date_expire, status) VALUES ( '{$t['tag']}', '{$this->customer_id}', '{$t['tag_type']}', now(), '{$t['validity_period']}', '$date_expire', 'ACTIVE' ) \n";
						mysql::i()->query($sql);
					}
                }//foreach                        
            }else{
                $t = self::getTagsFromGroup($tags['tag_no']);
				$sql = "SELECT * FROM tag_registered WHERE tag_no = '{$t['tag']}' \n";
				$q = mysql::i()->query($sql);
				if(mysql::i()->num_rows($q) == 0){	
					$date_expire = self::tagExpireDate($t['validity_period']);
					echo $sql = "INSERT INTO tag_registered (tag_no, customer_id, tag_type, date_registered, validity_period, date_expire, status) VALUES ( '{$t['tag']}', '{$this->customer_id}', '{$t['tag_type']}', now(), '{$t['validity_period']}', '$date_expire', 'ACTIVE' ) \n";
					mysql::i()->query($sql);
				}
				
            }  
		}
		
		public function updateTagGroup($tags){
		echo "update tag group";
			if(is_array($tags)){
                foreach($tags as $tag){
                    echo $sql = "UPDATE group_tags SET action='USED' WHERE tag = '{$tag['tag_no']}' \n";
					mysql::i()->query($sql);
                }//foreach                        
            }else{
                echo $sql = "UPDATE group_tags SET action='USED' WHERE tag = '{$tags}' \n";
				mysql::i()->query($sql);
            }            
			
		}
		
		
		
	}
?>