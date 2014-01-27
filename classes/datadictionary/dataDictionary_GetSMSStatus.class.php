<?php
class  dataDictionary_GetSMSStatus extends dataDictionary_TextAnywhere{
	
		
	
		
		public function __construct(){
			
			
			
			$this->fieldSpec['clientMessageReference'] = array('required'=>'Y',
					'type'=>'STRING',
					'size'=>'128');
			
			
			
		}//constructor
		
		
	
		
		
		
}//dataDictionary_GetSMSStatus
?>