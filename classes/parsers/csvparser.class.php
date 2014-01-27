<?php
class csvparser extends parser
{
	protected $delimiter;
	protected $header;
	protected $row_counter;
	protected $column_count;


	public function __construct($filename, $has_header=true, $row_size=0, $delemiter = ',')
	{
		parent::__construct($filename);

		$this->setDelimiter($delemiter);
		$this->setRowSize($row_size);
		$this->row_counter=0;

		//$this->header=($has_header)?fgetcsv($this->fh,$this->row_size,$this->delimiter):array();
		if ($has_header)
		{
			$this->header = array_map('trim', fgetcsv($this->fh,$this->row_size,$this->delimiter));
			$this->column_count = count($this->header);
		}
		else
		{
			$this->header = array();
			$this->column_count = 0;
		}
	}

	// fix issue with malformed csv files with header rows not having matching data cols
	public function addOneMoreCol($name)
	{
		$this->header[] = $name;
		$this->column_count++;
	}

	public function setDelimiter($del)
	{
		$this->delimiter=$del;
	}

	public function getCurrentRowNum()
	{
		return $this->row_counter;
	}

	public function rewind()
	{
		$this->row_counter=0;
		rewind($this->fh);
		/*
		 * As we have a header row, need to advance one line.
		 */
		if(!empty($this->header))
		{
			fgets($this->fh);
		}
	}

	public function current()
	{
		$this->row_counter++;

		$row = fgetcsv($this->fh, $this->row_size, $this->delimiter);

		if (!empty($this->header) && !empty($row))
		{
			if (count($row)==$this->column_count)
			{
				$row=array_combine($this->header, $row);
			}
			else
			{
				debug::output("CSV parser - row count mismatch (line: $this->row_counter)");
			}
		}
		return $row;
	}

	public function key()
	{
		return $this->row_counter;
	}
}

?>