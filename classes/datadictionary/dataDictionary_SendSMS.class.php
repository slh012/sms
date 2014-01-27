<?php
class  dataDictionary_SendSMS extends dataDictionary_TextAnywhere{
	
		
	
		
		public function __construct(){
			
			$this->fieldSpec['clientBillingReference'] = array('required'=>'Y',
										'type'=>'STRING',																					
										'size'=>'128');
			
			$this->fieldSpec['clientMessageReference'] = array('required'=>'Y',
					'type'=>'STRING',
					'size'=>'128');
			
			$this->fieldSpec['originator'] = array('required'=>'Y',
					'type'=>'STRING',
					'size'=>'32');
			
			$this->fieldSpec['destinations'] = array('required'=>'Y',
					'type'=>'STRING',
					'size'=>'2298');//1000 numbers plus 998 commas
			
			$this->fieldSpec['body'] = array('required'=>'Y',
					'type'=>'STRING',
					'size'=>'918'); //6*153 (message parts x number of characters per part
			
			$this->fieldSpec['validity'] = array('required'=>'Y',
					'type'=>'LONG',
					'size'=>'1');
			
			$this->fieldSpec['CharacterSetID'] = array('required'=>'Y',
					'type'=>'INT',
					'size'=>'1');
			
			$this->fieldSpec['replyMethodID'] = array('required'=>'Y',
					'type'=>'INT',
					'size'=>'1');
			
			$this->fieldSpec['CharacterSetID'] = array('required'=>'Y',
					'type'=>'INT',
					'size'=>'1');
			
			$this->fieldSpec['replyMethodID'] = array('required'=>'Y',
					'type'=>'INT',
					'size'=>'1');
			
			
			
			$this->fieldSpec['statusNotificationUrl'] = array('required'=>'Y',
					'type'=>'STRING',
					'size'=>'2048');
					
		}//constructor
		
		
	
		
		
		
}//DataDictionary_Adfero
?>