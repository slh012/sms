<?php
class  dataDictionary_GetSMSInbound extends dataDictionary_TextAnywhere{
	
		
	
		
		public function __construct(){
			
			
			
		}//constructor
		
		
	
		public function fieldSpec(){
						 
			$this->fieldSpec['number'] = array('required'=>'Y',
										'type'=>'STRING',																					
										'size'=>'32');
			
			$this->fieldSpec['keyword'] = array('required'=>'Y',
					'type'=>'STRING',
					'size'=>'64');
			
			
			
			
		}		
		
		
}//DataDictionary_Adfero
?>