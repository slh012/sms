<?php

    class validator {

        static public $cleanData = array();
		
        static public $errors = array();
        static public $completed = array();
        static public $orderedData = array();
        static protected $multiple = array();

        static public $dictionaryList;
        
        public function __construct() {

        }
        
        static public function loadDictionaries($dictionaryList){
			
                if(!is_array($dictionaryList)){
                        self::$errors['dictionaryList'] = 'You need to pass an array of dictionaries';
                        return false;
                }else{
                        self::$dictionaryList = $dictionaryList;
                }

        }//loadDictionaries
        
        static public function getCleanData(){
            $cleandata = self::$cleanData;
			self::$cleanData = '';
			return  $cleandata;
        }
        
        static public function getErrors(){
            $errors = self::$errors;
			self::$errors = '';
			return $errors;
        }
        
        private function my_htmlspecialchars($value){
            return htmlspecialchars($value,ENT_QUOTES);
        }//my_htmlspecialchars
		
        private function get_magic_quotes_gpc($value){
            if (get_magic_quotes_gpc()){
                $value = stripslashes($value);
            }
            return $value;
        }//my_htmlspecialchars
		
        private function strip_tags($value){
            return strip_tags($value);
        }//my_htmlspecialchars
        
        private function utf8_decode($value){
            return utf8_decode($value);
        }
        
        static public function validateInput($data){
	//print_r($data);
            $data=array_map(array('validator','utf8_decode'),$data);
            $data=array_map(array('validator','get_magic_quotes_gpc'),$data);
            $data=array_map(array('validator','strip_tags'),$data);
            $data=array_map(array('validator','my_htmlspecialchars'),$data);
//            if (get_magic_quotes_gpc()){
//                $data=array_map(array('validator','stripslashes'),$data);
//            }
//            $data=array_map(array('validator','strip_tags'),$data);           
//            $data=array_map(array('validator','htmlspecialchars'),$data);
            
			
            foreach(self::$dictionaryList as $table){


                $DataDictionary = 'dataDictionary_'.$table;

                $dataDic = new $DataDictionary;
                $fieldSpec = $dataDic->fieldSpec;
				

                        foreach($data as $key=>$value){
							
					
							
                            if( (isset($fieldSpec[$key])) ){
								
                                self::fieldValidation($key, $value , $fieldSpec[$key]);

                                self::$completed[trim($key)] = trim($value);
                            }//if

                        }//foreach
            }//forach
            
            self::$cleanData = self::$completed;//do the swap here so we can handle default data
			self::$completed = '';

        }//validateInput
        
        static private function fieldValidation($key, $value, $spec){
		
			/*the order can be adjusted ie formatByFunction after we've checked its required*/

		

			//$spec['function']='';
			//$spec['required']='';
			//$spec['size']='';
			//$spec['value']='';
			//$spec['library']='';
			$spec['type']='';
			//$spec['object']='';


			$okay = true;

			if($spec['function']){				
				//in order to return properly formated data within fields for the user to see, esp if they miss something out, we'll format the data
				//according to the spec			
				$function = trim($spec['function']);
				$okay = self::formatByFunction($key, $value, $function);			
			}//special


			if($okay == FALSE)return false;					

			if($spec['required']){
				//just checks the spec to see if the value is Y (required) or N (not required)			
				$required = trim($spec['required']);
				$default = trim($spec['default']);
				//echo "<br/>$key, $value, $required, $default<br/>";
				$okay = self::checkRequired($key, $value, $required, $default);
				//check required and check empty are two different tasks			
			}//type

			if($okay == FALSE)return false;
				if(empty($value)){//allow search fields with no value, by this point they have been identified as not required				
				return true;
			}

			if($spec['type']){
				//checks the type passed, ie INT, STRING or ENUM
				$type = trim($spec['type']);
				$okay = self::checkType($key, $value, $type, $spec);			
			}//type

			if($okay == FALSE)return false;

			if($spec['function']){		
				//each value serves a purpose, here we determine what the value is for and check it conforms to the spec			
				$function = trim($spec['function']);
				$okay = self::checkFunction($key, $value, $function);			
			}//special

			if($okay == FALSE)return false;

			if($spec['size']){
				//we just make sure the value is not greater than the size defined in the spec				
				$size = trim($spec['size']);
				$okay = self::checkSize($key, $value, $size);			
			}//size

			if($okay == FALSE)return false;

			if($spec['value']){
				//if there is only one value we expect to see then we can explicitly state that value in the fieldspec
				$v = trim($spec['value']);
				$okay = self::checkValue($key, $value, $v);			
			}//size

			if($okay == FALSE)return false;

                
        }//fieldValidation
        
        static private function formatByFunction($key, $value, $function){
            /*we accept a variable by REFERENCE so it can be edited without all the messy returning of variables.
            This helps the function conform to the standard format of returning boolean values to determine 
            success or failure*/
            $value = trim($value);
            $key = trim($key);

            switch($function){
                    case"USERINFO":
                            utils::lowerAll($value);	
                            utils::capitalFirst($value);	
                            return true;	
                    break;

                    case "PHONE":
                    case "LANDLINE":			
                    case "MOBILE":															
                            utils::removeSpaces($value);	
                            return true;			
                    break;
                    case "EMAIL":
                            return true;
                    break;
                    case "POSTCODE":				
                            utils::removeSpaces($value);	
                            utils::upperAll($value);	
                            return true;
                    break;
                    case "CARDNUMBER":	
                            return true;	
                    break;
                    case "CARDHOLDER":
                            utils::upperAll($value);		
                            return true;	
                    break;				
                    case"CVVS":
                    case"DATE":
                    case"TIME":
                            return true;
                    break;

            default;
                    debug::output(__FUNCTION__, $key, $value);
            return false;	
            }//switch

            debug::output(__FUNCTION__, $key, $value);
            return false;		


        }//checkFunction
        
        static private function checkRequired($key, $value, $required, $default){
        /*if the value is required then it must either A: have a value or B have a default otherwise it fails*/
        /*if the value is NOT required then it does not need to have a default value and should always allow*/


                switch($required){
                        case"Y":
                                if( (empty($value)) && (empty($default) && $value != '0' )  ){			
										
                                         self::$errors[$key] = "Please complete this field";
										
										
                                        return false;
                                }
                                else{	         
                                        return true;
                                }
                        break;
                        case"N":

                                return true;
                        break;
                        case"M":

                                $key = substr($key,0,-1);
                                if( (empty($value)) && (empty($default)) ){					

                                        self::$multiple[$key][] = 'E';					
                                        return false;
                                }
                                else{		
                                        self::$multiple[$key][] = 'F';						
                                        return true;
                                }
                        break;
                        default;
                        debug::output(__FUNCTION__, $key, $value);
                }//switch


                debug::output(__FUNCTION__, $key, $value);
                return false;
        }//checkRequired
        
        static private function checkValue($key, $value, $v){
			
                if($value != $v){
                        self::$errors[$key] = '$value was passed, $v is required';
                        return false;
                }
                else{
                        return true;
                }

        }//checkValue
        
        static private function checkSize($key, $value, $size){
                //$value = '\''.str_replace('.','',$value).'\'';
                $length = strlen(trim($value));

                if($length > $size){
                        self::$errors[$key] = "$value cannot be more than $size characters in length";
                        return false;
                }else{				
                        return true;
                }

                debug::output(__FUNCTION__, $key, $value);
                return false;
        }//checkSize
        

    }//validator
?>
