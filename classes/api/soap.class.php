<?php

	class soap{
		
		private $response = '';
		
		public function __construct(){
			
		}
		
		public function open($path){
			$sc = new SoapClient($path);
			return $sc;
		}
		
		
		
	}

?>