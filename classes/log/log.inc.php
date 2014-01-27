<?php

	class log{
	
            /*
             * MySQL connection resource.
             *
             * @type resource
             */
            protected $log;

            static public $instance;
            static public $instance_f = array();
            
            
		protected $root = '';
		protected $path = '';
	
		function __construct(){
			
			$this->root = '/home/';
			
			
			
		}//constructor
		
                
        //factory
	static public function f($class)
            {

           
            if(empty(self::$instance_f[$class])){
                            $classname = "log_".$class;
                            $i = new $classname;
              
                         
                          self::$instance_f[$class] = $i;
              return $i;
            }else{
              return self::$instance_f[$class];
            }

	}

	//instance
	static public function i($class)
	{

            if(!isset(self::$instance))
		{

                   
			$classname = "log_".$class;
                        self::$instance = new $classname;

			

		}

		return self::$instance;
	}
                
	
	}//log

?>