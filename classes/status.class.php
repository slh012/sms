<?php

define('CRITICAL','Critical');
define('WARNING','Warning');
define('GOOD','Good');


class status
{
	protected $fields=array();
	protected $values=array();
	
	static $instance;

	private function __construct()
	{                
		$this->values['date']=date('Y-m-d');
		$this->values['start']=date('H:i:s');
	}

	public static function instance()
	{
		if(!is_object(self::$instance))
		{
			self::$instance=new status;
		}
		return self::$instance;
	}

	public function save()
	{
		self::$instance=null;
		
	}

	public function status($status)
	{
		$this->values['status']=$status;
	}

	public function data($field,$value)
	{
		
		$this->values[$field]=$value;
			
	}

	
	public function __destruct()
	{
		$this->values['end']=date('H:i:s');
		$logdb = new log_database();
		try
		{
			
            $logdb->logSMSStatus($this->values);           
			//throw new dbException('PACKET OVERFLOW',1153);
		}
		catch(dbException $e){                    
					
			if ($e->getCode() == 1153 ){
			//max_allowed_packet
		
			$logf = new log_file('/packet_overflow/');  
							
			if ($logf->packet_overflow($this->values))
			{
								
				$this->values['log_message'] = "LOG TEXT OVER MySQL max_packet_size, WRITTEN TO ".$logf->path;	
				
				
				$logdb->logSMSStatus($this->values);
				
			}
			else
			{
				$this->values['log_message'] = "TRIED TO CREATE LOG FILE AND FAILED ".$logf->path;
			}

			$this->status(CRITICAL);

			$logdb->logSMSStatus($this->values);
                                
			}
			else
			{
				debug::output($e->getMessage());
				$this->status(WARNING);
			}

		}
	}
}

?>