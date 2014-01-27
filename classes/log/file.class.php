<?php
	class file {

                /*
                * File connection resource.
                *
                * @type resource
                */
		protected $file;
	
		static public $instance;
                
		static private $handle;
		static public $path = '';
		
		public function __construct(){
			$this->root = '/home/';
		}
		
                //instance
                static public function i()
                {

                    if(!isset(self::$instance))
                        {                           
                                self::$instance=new file;                               

                        }

                        return self::$instance;
                }
                
                static public function is_dir($filename){
                    return is_dir($filename);                    
                }
                
                static public function mkdir($pathname, $mode = 0777, $recursive = false){
                    if (!mkdir($pathname, $mode, $recursive)) {
                        debug::output('Failed to create folders...'.$pathname);
                        return false;
                    }else{
                        return true;
                    }
                }
                
		static public function fwrite($string){
			if (fwrite(self::$handle, $string) === FALSE) {
				debug::output('Cannot write to filename: '.self::$path);
				return false;
			}else{
                            return true;
                        }
		}
                
                static public function fputcsv($array){
                    array_push($array, "\n");
                    $list =  array( array_keys($array), $array);                    
                    foreach ($list as $fields) {
                        fputcsv(self::$handle, $fields);
                    }
                    
                }
		
		static public function fopen($path, $mode){
			self::$path = $path;
			if(!self::$handle = fopen(self::$path, $mode)){
				debug::output('Cannot write to filename: '.self::$path);
				return false;
			}else{
                            return true;
                        }
		}
                
                static public function fclose(){
                    return fclose(self::$handle);
                }
		
		
		
	}
?>