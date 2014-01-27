<?php
	class log_file extends file  {
		
		
		
		public function __construct($path){
			parent::__construct();
			$this->path = $this->root.'sms/archive/log'.$path;
		}
		
		
		
		private function cleanData($data){
			$data = htmlspecialchars_decode($data);
			return $data;
		}
		
		public function packet_overflow($contents){
			$data = $this->captureString($contents);	
			
			$filename = time().'.txt';
			
			$path = $this->path.$filename;
			
			if(parent::fopen($path, 'a')){			
				parent::fwrite($data);
				return true;
			}else{
				return false;
			}
			
			
		}
		
		public function output_buffer($contents){
			$data = $this->cleanData($contents);
			
			$filename = date('Ymd', time()).'.txt';
				
			$path = $this->path.$filename;
				
			parent::fopen($path, 'a');
				
			parent::fwrite($data);
			
		}
		
		public function request($call, $data, $c){
			
			$data = $this->captureString($data);						
			
			$filename = time().'-'.$call.'-request-'.$c.'.txt';
			
			$path = $this->path.$filename;
			
			parent::fopen($path, 'a');
			
			parent::fwrite($data);
			
		}
		
		private function captureString($array){
			
			ob_start();
			
			foreach($array as $key=>$value){
				if(is_object($value))continue;
				echo $key.' = '.$value.",";
			}
			
			
			$string = ob_get_contents();
			
			
			ob_end_clean();
			
			return $string;
			
		}
		
		
		public function response($call, $data, $c){
			$data = $this->cleanData($data);
				
			$filename = time().'-'.$call.'-response-'.$c.'.xml';
				
			$path = $this->path.$filename;
				
			parent::fopen($path, 'a');
				
			parent::fwrite($data);
				
		}
		
	}
?>