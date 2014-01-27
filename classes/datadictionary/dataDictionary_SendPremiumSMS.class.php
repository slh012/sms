<?php
class  dataDictionary_SendPremiumSMS extends dataDictionary_TextAnywhere{
	
		
	
		
		public function __construct(){
			
			
			
		}//constructor
		
		
	
		public function fieldSpec(){
						 
			$this->fieldSpec['clientBillingReference'] = array('required'=>'Y',
										'type'=>'STRING',																					
										'size'=>'128');
			
			$this->fieldSpec['clientMessageReference'] = array('required'=>'Y',
					'type'=>'STRING',
					'size'=>'128');
			
			$this->fieldSpec['rbid'] = array('required'=>'Y',
					'type'=>'STRING',
					'size'=>'32');
			
						
			$this->fieldSpec['body'] = array('required'=>'Y',
					'type'=>'STRING',
					'size'=>'160');
			
			$this->fieldSpec['validity'] = array('required'=>'Y',
					'type'=>'LONG',
					'size'=>'1');
			
			
			
			$this->fieldSpec['CharacterSetID'] = array('required'=>'Y',
					'type'=>'INT',
					'size'=>'1');			
		
			
			$this->fieldSpec['statusNotificationUrl'] = array('required'=>'Y',
					'type'=>'STRING',
					'size'=>'2048');
			
			
		}		
		
		
}//DataDictionary_Adfero
?>