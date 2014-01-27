<?php
class  dataDictionary_TextAnywhereAPIResponse extends dataDictionary_TextAnywhere{
	
		
	
		
		public function __construct(){
			
			$this->fieldSpec['Code'] = array('required'=>'N',
					'type'=>'INT',
					'size'=>'3');
			
			$this->fieldSpec['Description'] = array('required'=>'N',
					'type'=>'STRING',
					'size'=>'128');
			
			
			
		}//constructor
		
		
	
		
		
		
}//DataDictionary_Adfero
?>