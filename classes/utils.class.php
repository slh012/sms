<?php
	abstract class utils{
		
		static public function recursiveCall($class, $vars, $c, $debug=''){
			echo "Count: {$c}\n";
			if($c==1) $class = lcfirst($class);
			$class = new $class($vars, $c);		
			$class->request();
			$class->response();
			
			
			if($class->fault == true){
				echo "FAULT\n";
				if($c == 3){					
					unset($class);					
					return;
				}
				return self::recursiveCall($class, $vars, ++$c, $debug);
			}
			return $class;
		}//recursiveCall
        
		
		
			
        
		static public function getAirportbyIATA($iata){
			$sql = "SELECT * FROM airports where iata='{$iata}' \n";
			$q = mysql::i()->query($sql);
			if(mysql::i()->num_rows($q)==1){
				return mysql::i()->fetch_array($q);
			}else{
				while($r = mysql::i()->fetch_array($q)){
					if($r['country']=='USA')continue;					
					$array = $r;
				}
				print_r($array);
				return $array;
			}
			
		}
                
                
            static public function captureString($input){

                ob_start();
                if(is_array($input)){
                    foreach($input as $key=>$value){
                            if(is_object($value))continue;
                            echo $key.' = '.$value.",";
                    }


                    $string = ob_get_contents();



                }else{
                    echo $input;
                }
                 ob_end_clean();

                return $string;

            }//captureString

            static public function stripAlphabetic ($string){
				$string = trim($string);
                $stripped = preg_replace("([a-zA-Z ]+)","",$string);
                return $stripped;
            }
            static public function stripAlphaNumeric ($string){
				$string = trim($string);
                $stripped = preg_replace("([a-zA-Z0-9 ]+)","",$string);
                return $stripped;
            }
			static public function stripNumeric ($string){
				$string = trim($string);
                $stripped = preg_replace("([0-9 ]+)","",$string);
                return $stripped;
            }
            
			static public function removeMultiSpaces($string){
				return trim(preg_replace('/\s{2,}/',' ', $string));
			}
			
			static public function formatPhoneNumber($number){
				$number = trim($number);			
				
				$number = preg_replace('/^0/','+44',$number);
				
				return $number;
			}
			
			static public function randomString($length = 8)
			{
				$string = '';
				for ($i = 0; $i < $length; $i++)
				{
					$string .= chr(mt_rand(48, 126));
				}
				
				return strtolower($string);
			}
			
			static public function createRandString ($length = 8){

				// start with a blank password
				$password = "";

				// define possible characters - any character in this string can be
				// picked for use in the password, so if you want to put vowels back in
				// or add special characters such as exclamation marks, this is where
				// you should do it
				$possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";

				// we refer to the length of $possible a few times, so let's grab it now
				$maxlength = strlen($possible);
			  
				// check for length overflow and truncate if necessary
				if ($length > $maxlength) {
				  $length = $maxlength;
				}
				
				// set up a counter for how many characters are in the password so far
				$i = 0; 
				
				// add random characters to $password until $length is reached
				while ($i < $length) { 

				  // pick a random character from the possible ones
				  $char = substr($possible, mt_rand(0, $maxlength-1), 1);
					
				  // have we already used this character in $password?
				  if (!strstr($password, $char)) { 
					// no, so it's OK to add it onto the end of whatever we've already got...
					$password .= $char;
					// ... and increase the counter by one
					$i++;
				  }

				}

				// done!
				return $password;

			}
	static public function getIP($return_array = false)
	{
		
			$ip = null;
			if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			{
				$ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
			}
			
			if (is_array($ip))
			{
				if (count($ip) > 1)
				{
					$via_proxy = mysql::i()->escape_string(array_pop($ip));
					$via_proxy = trim($via_proxy);
				}
				
				$ip = mysql::i()->escape_string(array_pop($ip));
			}
			
			if (empty($ip))
			{
				$ip = $_SERVER['REMOTE_ADDR'];
			}
			
			if (empty($via_proxy))
			{
				$via_proxy = '';
			}
			
			$ip = trim($ip);
			
			$ip = array('ip' => $ip, 'via_proxy' => $via_proxy);
		
		
		if  (false === $return_array)
		{
			if (is_array($ip))
			{
				$iparray = $ip;
				return $iparray['ip'];
			}
			else
			{
				return $ip;
			}
		}
		else
		{
			return $ip;
		}
	}
           
		
	}
?>