<?php
class  dataDictionary_AirDB extends dataDictionary_TextAnywhere{
	
		
	
		
		public function __construct(){
			
			$this->fieldSpec['ICAO'] = array('required'=>'N',
										'type'=>'STRING',																					
										'size'=>'64');
			
			$this->fieldSpec['IATA'] = array('required'=>'N',
					'type'=>'STRING',
					'size'=>'64');
			
			
			$this->fieldSpec['Place'] = array('required'=>'N',
					'type'=>'STRING',
					'size'=>'255');
					
			$this->fieldSpec['State'] = array('required'=>'N',
					'type'=>'STRING',
					'size'=>'255');

			$this->fieldSpec['Airport'] = array('required'=>'N',
					'type'=>'STRING',
					'size'=>'255');
					
			$this->fieldSpec['Country'] = array('required'=>'N',
					'type'=>'STRING',
					'size'=>'255');
			
			
		}//constructor
		
		
	
			
		
		
}//dataDictionary_AirDB
?>