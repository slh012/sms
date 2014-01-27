<?php

abstract class parser implements Iterator
{
	/*
	 * file to parse - file handle
	 * @resource
	 */
	protected $fh;
	protected $row_size;


	abstract public function getCurrentRowNum();


	public function __construct($filename)
	{
		if(empty($filename)||!is_file($filename))
		{
			$cwd=getcwd();
			throw new parserException("NO SUCH FILE: $filename ($cwd) - {$_SERVER['SCRIPT_NAME']}");
		}

		if(!filesize($filename))
		{
			$cwd=getcwd();
			throw new parserException("FILESIZE 0 BYTES: $filename ($cwd) - {$_SERVER['SCRIPT_NAME']}");
		}

		$this->fh=fopen($filename,'r');
		if(!is_resource($this->fh))
		{
			$cwd=getcwd();
			throw new parserException("INVALID RESOURCE: $filename ($cwd) - {$_SERVER['SCRIPT_NAME']}");
		}

		$this->row_size=0;
	}

	public function setRowSize($size)
	{
		$this->row_size=abs((int)$size);
	}

	public function next()
	{
		return !(feof($this->fh)!==false);
	}

	public function valid()
	{
		if(!$this->next())
		{
			fclose($this->fh);
			return false;
		}
		return true;
	}
}

?>