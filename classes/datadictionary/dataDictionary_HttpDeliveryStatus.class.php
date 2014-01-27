<?php
class  dataDictionary_HttpDeliveryStatus extends dataDictionary_TextAnywhere{
	
		

		
		public function __construct(){
			
			$this->fieldSpec['destination'] = array('required'=>'Y',
					'type'=>'STRING',
					'size'=>'15');
			
			$this->fieldSpec['messagestatuscode'] = array('required'=>'Y',
					'type'=>'STRING',
					'size'=>'3');
			
			$this->fieldSpec['clientmessagereference'] = array('required'=>'Y',
					'type'=>'STRING',
					'size'=>'128');
			
			$this->fieldSpec['messagereference'] = array('required'=>'Y',
					'type'=>'STRING',
					'size'=>'255');
			
			$this->fieldSpec['partcurrent'] = array('required'=>'Y',
					'type'=>'STRING',
					'size'=>'1');		
			
			$this->fieldSpec['parttotal'] = array('required'=>'Y',
					'type'=>'STRING',
					'size'=>'1');	

				
			
		}//constructor
		
		
	
		
		
		
}//DataDictionary_Adfero
?>