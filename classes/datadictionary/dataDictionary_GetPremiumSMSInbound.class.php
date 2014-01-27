<?php
class  dataDictionary_GetPremiumSMSInbound extends dataDictionary_TextAnywhere{
	
		
	
		
		public function __construct(){
			
			
			
		}//constructor
		
		
	
		public function fieldSpec(){
						 
			$this->fieldSpec['shortCode'] = array('required'=>'Y',
										'type'=>'STRING',																					
										'size'=>'32');
			
			$this->fieldSpec['keyword'] = array('required'=>'Y',
					'type'=>'STRING',
					'size'=>'64');
			
			
			
			
		}		
		
		
}//DataDictionary_Adfero
?>