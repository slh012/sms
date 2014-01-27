<?php

interface database_interface
{
	public function connect($host,$user,$password,$db_name);
	public function query($sql);
	public function fetch_object($result);
	public function fetch_array($result);
	public function last_id();
	public function escape_string($string);
	public function affected_rows();
	public function num_rows($result);
	public function seek($result,$row);
	public function free_result($result);
}
?>