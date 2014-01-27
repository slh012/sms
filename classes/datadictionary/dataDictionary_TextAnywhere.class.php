<?php
	class dataDictionary_TextAnywhere{
	
		public $fieldSpec = array();
		
		public function __construct(){
			
			$this->fieldSpec['returnCSVString'] = array('required'=>'Y',
										'type'=>'BOOLEAN'																					
										);
			
			$this->fieldSpec['externalLogin'] = array('required'=>'Y',
					'type'=>'STRING',
					'size'=>'64');
			
			$this->fieldSpec['password'] = array('required'=>'Y',
					'type'=>'STRING',
					'size'=>'64');
			
		}//constructor
		
		
	
			
	
	}
?>