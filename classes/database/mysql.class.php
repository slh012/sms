<?php

class mysql implements database_interface
{
	/*
	 * MySQL connection resource.
	 *
	 * @type resource
	 */
	protected $mysql;

	static public $instance;
	static public $instance_f = array();

	private function __construct()
	{

	}

	//factory
	static public function f($connection)
	{

        list($host,$user,$pass,$db)=explode(':',$connection);
        if(empty(self::$instance_f[$connection])){
          $i=new mysql;
                      $i->connect($host,$user,$pass,$db, true);
                      self::$instance_f[$connection] = $i;
          return $i;
        }else{
          return self::$instance_f[$connection];
        }

	}

	//instance
	static public function i($connection='')
	{

            if(!isset(self::$instance))
		{

                    list($host,$user,$pass,$db)=explode(':',$connection);
			self::$instance=new mysql;

			self::$instance->connect($host,$user,$pass,$db);

		}

		return self::$instance;
	}

	public function connect($host,$user,$pass,$db, $newlink = false)
	{

                $this->mysql=mysql_connect($host,$user,$pass, $newlink);

                if(!is_resource($this->mysql))
		{

                    throw new dbException(mysql_error($this->mysql),mysql_errno($this->mysql));
		}

		if(!mysql_select_db($db,$this->mysql))
		{

                    throw new dbException(mysql_error($this->mysql),mysql_errno($this->mysql));
		}
	}

	public function escape_string($str)
	{
		return mysql_real_escape_string($str);
	}

	public function query($sql)
	{
		
		$q=mysql_query($sql,$this->mysql);
		if($q===false)
		{
			throw new dbException(mysql_error($this->mysql)."\nSQL: $sql",mysql_errno($this->mysql));
		}

		return $q;
	}

	public function unbuffered_query($sql)
	{
		$q = mysql_unbuffered_query($sql, $this->mysql);
		if ($q === false)
		{
			throw new dbException(mysql_error($this->mysql) . "\nSQL: $sql", mysql_errno($this->mysql));
		}

		return $q;
	}

	public function last_error()
	{
		return mysql_error($this->mysql);
	}

	public function fetch_row($result)
	{
		if (!is_resource($result))
		{
			debug_print_backtrace();
		}
		return mysql_fetch_row($result);
	}

	public function fetch_object($result)
	{
		if(!is_resource($result))
		{
			debug_print_backtrace();
		}
		return mysql_fetch_object($result);
	}

	public function fetch_array($result,$result_type=MYSQL_ASSOC)
	{
		if(!is_resource($result))
		{
			debug_print_backtrace();
		}

		return mysql_fetch_array($result,$result_type);
	}

	public function last_id()
	{
		return mysql_insert_id($this->mysql);
	}

	public function affected_rows()
	{
		return mysql_affected_rows($this->mysql);
	}

	public function num_rows($r)
	{
		return mysql_num_rows($r);
	}

	public function seek($r,$i)
	{
		return mysql_data_seek($r,$i);
	}

	public function free_result($r)
	{
		return mysql_free_result($r);
	}
	public function close()
	{

		return mysql_close($this->mysql);
	}
}
?>