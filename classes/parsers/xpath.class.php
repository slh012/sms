<?php

class xpath{

	private $doc;
	private $xml = '';
	private $xpath;

	public function __construct($data)
	{
			
		
		
		//$this->xml = file_get_contents($data);
		$this->xml = $data;

		$this->doc = new DOMDocument("1.0", "ISO-8859-1");
                //$this->doc->resolveExternals=true;
                //$this->doc->DOMDocumentType = '/data01/home/spi8er/flat_file_processing/archive/WTG/WTGData.dtd';
                
		$this->doc->loadXML($this->xml);
		
		// Not interested in white space
		$this->doc->preserveWhiteSpace = false;			
		
		// create structure
		$this->xpath = new DOMXPath($this->doc);
		
		
	}
	
	function killXml($newPage,$domObj,$xmlString)
	{

		unset($xmlString);
		
		unset($domObj);

	}
	
	function getXpathObject()
	{
	
		return $this->xpath;
	}
	
	function getDOMObject()
	{
	
		return $this->doc;
	}
	function search($strQuery,$context="none")
	{
	
		if($context != "none")
		{
			$result = $this->xpath->query($strQuery,$context);
		}
		else
		{
			//echo $strQuery."\n";
			$result = $this->xpath->query($strQuery);
		}
		
		if($result->length == 0)
		{						
			debug::output('Missing XML data for query: \''.$strQuery.'\'');
			return false;
		}
		
		return $result;
	}
	
	function getXml()
	{
		return $this->xml;

	}
	
	

}

?>