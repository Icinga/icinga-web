<?php
require_once "phing/Task.php";
/**
 * Exports the meta data of a cronk (i.e. actions, templates, validation, etc.)
 * @author jmosshammer <jannis.mosshammer@netways.de>
 *
 */
class cronkMetaExtractorTask extends Task {
	/**
	 * the path to cronk.xml
	 * @var string
	 */
	protected $file;
	
	public function getFile() {
		return $this->file;
	}
	public function setFile($file) {
		$this->file = $file;
	}
	
	public function init() {
	}
	/**
	 * Reads the corresponding actions and templates of the cronk (if any) from cronk.xml
	 * and creates two references "cronkTemplate" and "cronkAction" which can be used to access these files
	 */
	public function main() {
		$templates = array();
		$actionPath = "";
		$action = null;
		$module = null;
		$cronkName = $this->project->getUserProperty("cronkName");
		
		// Setup and load the DOM of the cronk
		$DOM = new DOMDocument("1.0","UTF-8");
		$DOM->load($this->getFile());
		$DOMSearcher = new DOMXPath($DOM);
		$DOMSearcher->registerNamespace("default","http://agavi.org/agavi/1.0/config");
		$DOMSearcher->registerNamespace("ae","http://agavi.org/agavi/config/global/envelope/1.0");
		
		$cronkTemplates = $DOMSearcher->query("//ae:parameter[@name='".$cronkName."']//ae:parameter[@name='template']");
		if($cronkTemplates->length > 0) {
			foreach($cronkTemplates as $template) {
				$templates[] = $template->nodeValue;		
			}
		}
		// fetch action and module
		$action = $DOMSearcher->query("//ae:parameter[@name='".$cronkName."']//ae:parameter[@name='action']")->item(0);
		$module = $DOMSearcher->query("//ae:parameter[@name='".$cronkName."']//ae:parameter[@name='module']")->item(0);
		$actionName = str_replace(".","/",$action->nodeValue);

		// add agavi action,validation, view and templates
		$cronkFS = new FileSet();
		$cronkFS->setDir($this->project->getUserProperty("PATH_Icinga")."/app/modules/".$module->nodeValue);
			$cronkIncludes = "actions/".$actionName."Action.class.php"; 		
			$cronkIncludes .= ",templates/".$actionName."*.class.php"; 		
			$cronkIncludes .= ",validate/".$actionName.".xml"; 		
			$cronkIncludes .= ",views/".$actionName."*.class.php"; 		
		
		$cronkFS->setIncludes($cronkIncludes);
		
		// add templates 
		$templateFs = new FileSet();
		$templateFs->setDir($this->project->getUserProperty("PATH_Icinga")."/app/modules/Cronks/data/xml/");
		$includes = "";
		$first = true;
		foreach($templates as $template) {
			$includes .= ($first ? '' : ',').$template.".xml";
		}
		// export to phing
		$templateFs->setIncludes($includes);
		$this->project->addReference("cronkTemplates",$templateFs);
		$this->project->addReference("cronkAction",$cronkFS);
		
	}
	
}