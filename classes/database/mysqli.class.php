<?php

class mysqli implements database_interface
{
	/*
	 * mysqli connection resource.
	 *
	 * @type resource
	 */
	protected $mysqli;

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
          $i=new mysqli;
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
			self::$instance=new mysqli;

			self::$instance->connect($host,$user,$pass,$db);

		}

		return self::$instance;
	}

	public function connect($host,$user,$pass,$db, $newlink = false)
	{

                $this->mysqli=mysqli::__construct($host,$user,$pass, $newlink);

                if(!is_resource($this->mysqli))
		{

                    throw new dbException(self::error($this->mysqli),self::errno($this->mysqli));
		}

		if(!mysqli_select_db($db,$this->mysqli))
		{

                    throw new dbException(self::error($this->mysqli),self::errno($this->mysqli));
		}
	}

	public function escape_string($str)
	{
		return mysqli_escape_string($str);
	}

	public function query($sql)
	{

		$q=mysqli_query($sql,$this->mysqli);
		if($q===false)
		{
			throw new dbException(mysqli_error($this->mysqli)."\nSQL: $sql",mysqli_errno($this->mysqli));
		}

		return $q;
	}

	public function unbuffered_query($sql)
	{
		$q = mysqli_unbuffered_query($sql, $this->mysqli);
		if ($q === false)
		{
			throw new dbException(mysqli_error($this->mysqli) . "\nSQL: $sql", mysqli_errno($this->mysqli));
		}

		return $q;
	}

	public function last_error()
	{
		return mysqli_error($this->mysqli);
	}

	public function fetch_row($result)
	{
		if (!is_resource($result))
		{
			debug_print_backtrace();
		}
		return mysqli_fetch_row($result);
	}

	public function fetch_object($result)
	{
		if(!is_resource($result))
		{
			debug_print_backtrace();
		}
		return mysqli_fetch_object($result);
	}

	public function fetch_array($result,$result_type=mysqli_ASSOC)
	{
		if(!is_resource($result))
		{
			debug_print_backtrace();
		}

		return mysqli_fetch_array($result,$result_type);
	}

	public function last_id()
	{
		return mysqli_insert_id($this->mysqli);
	}

	public function affected_rows()
	{
		return mysqli_affected_rows($this->mysqli);
	}

	public function num_rows($r)
	{
		return mysqli_num_rows($r);
	}

	public function seek($r,$i)
	{
		return mysqli_data_seek($r,$i);
	}

	public function free_result($r)
	{
		return mysqli_free_result($r);
	}
	public function close()
	{

		return mysqli_close($this->mysqli);
	}
}
?>