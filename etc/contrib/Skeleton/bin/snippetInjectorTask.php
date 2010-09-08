<?php
require_once "phing/Task.php";
/**
 * Injects snippets to it's target file
 * @author jmosshammer <jannis.mosshammer@netways.de>
 *
 */
class snippetInjectorTask extends Task {
	protected $file;
		
	public function init()	{
	} 
	/**
	 * (non-PHPdoc)
	 * @see lib/phing/classes/phing/Task#main()
	 */
	public function main() {
		if(!file_exists($this->file))
			return false;
		$snippets = unserialize(file_get_contents($this->file));
		if(!$snippets) {
 			echo ("Invalid snippet file");
 			return null;
		}
	 	
		foreach($snippets as $snippet) {
			$file = str_replace("%PATH_Icinga%",$this->project->getUserProperty("PATH_Icinga"),$snippet["file"]);
			$mark;
			$name = $snippet["mark"];
			$ext = preg_replace("/.*\.(\w{1,5})$/","$1",$file); // check the comment type
			switch($ext) {
				case 'txt':
				case 'ini':	
				case 'pl':
					$mark = "#MODULE[".$name."]";
					break;
				case 'xml':
				case 'html:':
					$mark = "<!-- MODULE[".$name."] -->";
					break;
				case 'css':
				case 'php':
				case 'js':
					$mark = "/*MODULE[".$name."]*/";
					break;
				default:
					throw new BuildException("Unknown filetype ".$ext);
			}
			$snippetFileContent = file_get_contents($file);
			// remove previous snippets
			$snippetFileContent = preg_replace("/.*MODULE\[".$name."\].*[\r\n]+([\w\W\r\n]+)[\r\n]+.*?MODULE\[".$name."\].*?[\r\n]+/","",$snippetFileContent);
			
			// append this snippet
			$snippetText = "\n".$mark."\n".$snippet["content"]."\n".$mark."\n";
			
			// save original file
			$actionQueue = actionQueueTask::getInstance();
			$actionQueue->registerFile($file);
			file_put_contents($file,$snippetFileContent.$snippetText);
		}
	}
	
	public function setFile($file) {
		$this->file = $file;
	}
}
