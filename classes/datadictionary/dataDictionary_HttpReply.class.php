<?php
class  dataDictionary_HttpReply extends dataDictionary_TextAnywhere{
	
		
	
		
		public function __construct(){
			
			$this->fieldSpec['originator'] = array('required'=>'Y',
					'type'=>'STRING',
					'size'=>'32');
			
			$this->fieldSpec['destination'] = array('required'=>'Y',
					'type'=>'STRING',
					'size'=>'13');
			
			$this->fieldSpec['clientmessagereference'] = array('required'=>'N',
					'type'=>'STRING',
					'size'=>'128');
			
			$this->fieldSpec['body'] = array('required'=>'Y',
					'type'=>'STRING',
					'size'=>'160');
			
			$this->fieldSpec['date'] = array('required'=>'Y',
					'type'=>'STRING',
					'size'=>'10');		
			
			$this->fieldSpec['time'] = array('required'=>'Y',
					'type'=>'STRING',
					'size'=>'8');	

			$this->fieldSpec['rbid'] = array('required'=>'Y',
					'type'=>'STRING',
					'size'=>'32');		
			
		}//constructor
		
		
	
		
		
		
}//DataDictionary_Adfero
?>