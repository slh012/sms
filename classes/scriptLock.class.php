<?php
/*
 * Script locking, to stop inserters running when parsers are running and visa versa.
 */
class scriptLock
{
	private $process_name;
	
	
	
	public function __construct()
	{
		$this->release_all();
		$this->process_name = trim(config::get('process_name'));
		if (empty($this->process_name))
		{
			die("Unable to find a process_name.  Set the process_name before continuing.");
		}
		
		$this->process_name = mysql::i()->escape_string($this->process_name);
		
		
		
		$q = mysql::i()->query("select * from script_locks_sms where process_name = '$this->process_name' ");
		$r = mysql::i()->fetch_object($q);
		if (is_object($r))
		{
			die("Script ($this->process_name) is currently locked.  Lock obtained: $r->date, $r->time\n");
		}

		

		mysql::i()->query("insert into script_locks_sms (process_name, date, time) values ('$this->process_name', curdate(), curtime())");
	}
	
	public function release_all()
	{
            
            mysql::i()->query("truncate script_locks_sms ");
		
	}
	
	public function release()
	{
            
            mysql::i()->query("delete from script_locks_sms where process_name = '$this->process_name' ");
		
	}
}
?>