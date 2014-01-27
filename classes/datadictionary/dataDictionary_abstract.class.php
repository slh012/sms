<?php

	abstract class dataDictionary_abstract{

			public $tablename = '';
                        public $dbname = '';
			public $fieldSpec = array();
			public $primaryKey = array();
			public $orderby = '';


		public function __construct(){



		}//constructor



		//abstract public function fieldSpec();//fieldSpec

		/*


		$this->fieldSpec[COLUMN NAME] = array('required'=>'Y',
										'function'=>'SEE BELOW',
										'type'=>'SEE BELOW'
										'size'=>'AS IN DATABASE',
										'default'=>'AS IN DATABASE');


		function exmples
		USERINFO
		DOB
		PHONE
		EMAIL
		POSTCODE
		CARDNUMBER
		CARDHOLDER
		CVVS

		type exmples
		STRING
		INT
		INTEGER
		NUMERIC
		TIME
		DATE
		ENUM
		*/



	}//

?>