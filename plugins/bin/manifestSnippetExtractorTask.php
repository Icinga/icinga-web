<?php

require_once "manifestBaseClass.php";

/**
 * Extracts "snippets", textual information that are marked for the export
 * 
 * @author jmosshammer <jannis.mosshammer@netways.de>
 *
 */

class ManifestSnippetExtractorTask extends manifestBaseClass {

    private $toFile = null;
	private $extractorMarks = array();	
	private $extracted = array();
	
    public function setTofile($target) {
    	$this->toFile = $target;
    }
  	
  	public function addExtractorMark(SimpleXMLElement $name,SimpleXMLElement $file) {
    	$this->extractorMarks[] = array("name" => (String) $name, "file" => (String) $file);
    }
    public function setXMLObject(SimpleXMLElement $xml) {
    	$this->xmlObject = $xml;
    }
    
	public function getTofile() {
		return $this->toFile;
	}
	public function getExtractorMarks() {
		return  $this->extractorMarks;
	}

    public function main() {
    	parent::main();
		$this->fetchExtractorMarks();
		$this->extractMarks();
		$this->writeToFile();
	}
	
	protected function fetchExtractorMarks() {
		$xml = $this->getXMLObject();
		$marks = $xml->Files->Extractor;
		if(!$marks)
			return null;
		foreach($marks->children() as $mark) {
			$markName = $mark["mark"];
			$file = $mark;
			$this->addExtractorMark($markName,$file);
		}
	}
	
	/**
	 * Searches for extractor marks and (PLUGIN[]) and extracts them
	 * 
	 * @throws Buildexception if the marked file doesn't exist
	 */
	public function extractMarks() {
		$str = "";
		$marks = $this->getExtractorMarks();
		if(!$marks)
			return null;
		foreach($marks as $mark) {
			$name = $mark["name"];
			$file = $mark["file"];
			if(!file_exists($file))
				throw new BuildException("Extractor error: Marked file ".$file." doesn't exist!");
			$content = file_get_contents($file);
			$matches = array();
			preg_match("/.*PLUGIN\[".$name."\].*[\r\n]+([\w\W\r\n]+)[\r\n]+.*?PLUGIN\[".$name."\].*?[\r\n]+/",$content,$matches);
			if(count($matches) < 2) 	
				echo "\Warning: Couldn't find a match for mark ".$name." in file ".$file;
				
			$this->extracted[] = array(
				// replace the absolute path with the %PATH_Icing% token
				"mark" => $name,
				"file"=>str_replace($this->project->getUserProperty("PATH_Icinga"),"%PATH_Icinga%",$file),
				"content" => $matches[1]
			);
		}
	}
	
	protected function writeToFile() {
		$target = $this->getTofile();
		file_put_contents($target,serialize($this->extracted));	
	}
}

?>
