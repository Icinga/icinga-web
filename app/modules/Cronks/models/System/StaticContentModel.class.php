<?php
/**
 * @author Christian Doebler <christian.doebler@netways.de>
 */
class Cronks_System_StaticContentModel extends ICINGACronksBaseModel
{

	/*
	 * API variables
	 */
	private $api = false;
	private $templateData = array();

	/*
	 * XML variables
	 */
	private $dom = false;
	private $xmlData = array();

	/**
	 * class constructor
	 * @param	void
	 * @return	Cronks_System_StaticContentModel
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function __construct () {
		$this->api = AppKitFactories::getInstance()->getFactory('IcingaData');
	}	

	/**
	 * main function to generate content
	 * @param	string			$xmlFile			absolute filename of XML template
	 * @return	unknown_type
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function getContent ($xmlFile) {
		$this->getTemplateData($xmlFile);
		$this->fetchData();
		$content = $this->processTemplates();

		return $content;
	}

	/*
	 * XML processing
	 */

	/**
	 * loads XML template and converts its data into an array
	 * @param	string			$file				absolute filename of XML template
	 * @return	void
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	private function getTemplateData ($file) {
		$xml = file_get_contents($file);
		$this->dom = new DOMDocument();
		$this->dom->preserveWhiteSpace = false;
		$this->dom->loadXML($xml);

		$this->xmlData = $this->convertDom($this->dom->getElementsByTagName('template')->item(0));
	}

	/**
	 * checks whether XML node for child nodes
	 * @param	DOMElement		$element			element to check for child nodes
	 * @return	boolean								true if element has children otherwise false
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	private function hasChildren (DOMElement &$element) {
		$hasChildren = false;
		if ($element->hasChildNodes()) {
			foreach ($element->childNodes as $node) {
				if ($node->nodeType == XML_ELEMENT_NODE) {
					$hasChildren = true;
					break;
				}
			}
		}

		return $hasChildren;
	}

	/**
	 * converts XML into an associative array
	 * @param	DOMElement		$element			XML node to convert into associative array
	 * @return	array								converted XML data								
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	private function convertDom (DOMElement &$element) {
		$data = array();

		if ($element->hasChildNodes()) {
			foreach ($element->childNodes as $child) {
				if ($child->nodeType == XML_ELEMENT_NODE) {
					$index = '__BAD_INDEX__';
					if ($child->hasAttribute('name')) {
						$index = $child->getAttribute('name');
					} elseif ($child->nodeName == 'datasource') {
						$index = count($data);
					} else {
						$index = $child->nodeName;
					}

					if ($this->hasChildren($child)) {
						$data[$index] = $this->convertDom($child);
					} else {
						$data[$index] = $child->textContent;
					}
				}
			}
		}

		return $data;
	}

	/*
	 * data fetching
	 */

	/**
	 * calls methods for data retrieval
	 * @param	void
	 * @return	boolean								true on successful retrieval of data otherwise false
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	private function fetchData () {
		$success = true;

		if (array_key_exists('datasources', $this->xmlData) && is_array($this->xmlData['datasources'])) {
			foreach ($this->xmlData['datasources'] as $dataSource) {
				switch ($dataSource['source_type']) {
					case 'IcingaApi':
						if (!($success = $this->fetchTemplateData($dataSource))) {
							break;
						}
						break;

					default:
						throw new Cronks_System_StaticContentModelException('fetchData(): invalid source_type in datasource!');
						$success = false;
						break;
				}
			}
		}

		if (!$success) {
			$this->templateData = array();
		}

		return $success;
	}

	/**
	 * fetches data via IcingaApi
	 * @param	array			$dataSource			query settings for Api
	 * @return	boolean								true on successful retrieval of data otherwise false
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	private function fetchTemplateData ($dataSource) {
		$success = true;

		if (!array_key_exists('id', $dataSource)) {

			throw new Cronks_System_StaticContentModelException('fetchTemplateData(): no id in datasource!');
			$success = false;

		} else {

			$apiSearch = $this->api->API()->createSearch()->setResultType(IcingaApi::RESULT_ARRAY);
			if (!array_key_exists('target', $dataSource)) {

				throw new Cronks_System_StaticContentModelException('fetchTemplateData(): no target in datasource!');
				$success = false;

			} else {

				// set search target
				$apiSearch->setSearchTarget(constant($dataSource['target']));

				// set result columns
				if (array_key_exists('columns', $dataSource)) {
					$columns = explode(',', $dataSource['columns']);
					foreach ($columns as $currentColumn) {
						$apiSearch->setResultColumns(trim($currentColumn));
					}
				}

				// set search type
				if (array_key_exists('search_type', $dataSource)) {
					$apiSearch->setSearchType(constant($dataSource['search_type']));
				}

				// set search filter
				if (array_key_exists('filter', $dataSource) && array_key_exists('columns', $dataSource['filter'])) {
					foreach ($dataSource['filter'] as $filter) {
						if (!array_key_exists('column', $filter)) {
							throw new Cronks_System_StaticContentModelException('fetchTemplateData(): no column defined in filter definition!');
							$success = false;
						}
						if ($success && !array_key_exists('value', $filter)) {
							throw new Cronks_System_StaticContentModelException('fetchTemplateData(): no value defined in filter definition!');
							$success = false;
						}

						if ($success) {
							$filterData = array(
								trim($filter['column']),
								trim($filter['value'])
							);

							if (array_key_exists('match_type', $filter)) {
								array_push($filterData, trim($filter['match_type']));
							}

							$apiSearch->setSearchFilter(array($filterData));
						}
					}
				}

				// execute query and fetch result
				if ($success) {
					// add access control to query
					$secureSearchModels = array(
						'IcingaHostgroup',
						'IcingaServicegroup',
						'IcingaHostCustomVariablePair',
						'IcingaServiceCustomVariablePair'
					);
					IcingaPrincipalTargetTool::applyApiSecurityPrincipals(
						$secureSearchModels,
						$apiSearch
					);

					// fetch data
					$apiRes = $apiSearch->fetch()->getAll();

					// set function
					if (array_key_exists('function', $dataSource)) {
						$function = $dataSource['function'];
					} else {
						$function = false;
					}

					// set result data
					$this->templateData[$dataSource['id']] = array(
						'data'			=> $apiRes,
						'function'		=> $function,
					);
				}

			}

		}

		return $success;
	}

	/*
	 * template parsing
	 */

	/**
	 * calls the template generator for each template
	 * @param	void
	 * @return	string								processed content
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	private function processTemplates () {
		$content = null;
		$subContent = array();

		if (array_key_exists('template_code', $this->xmlData)) {
			if (is_array($this->xmlData) && array_key_exists('MAIN', $this->xmlData['template_code'])) {
				$templates = $this->xmlData['template_code'];

				foreach ($templates as $tplId => $tpl) {
					if ($tplId != 'MAIN') {
						$subContent[$tplId] = $this->createTemplateContent($tpl);
					}
				}

				$content = $this->createTemplateContent($templates['MAIN']);
			} else {
				throw new Cronks_System_StaticContentModelException('processTemplates(): no template "MAIN" defined!');
			}
		} else {
			throw new Cronks_System_StaticContentModelException('processTemplates(): no template_code defined!');
		}

		return $content;
	}

	/**
	 * generates content from template and fetched data
	 * @param	string			$content			content template
	 * @return	string								processed content
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	private function createTemplateContent ($content) {
		// fetch repeating variables from template and call substitution routine
		$variablePattern = '/\${([A-Za-z0-9_\-]+):repeat}/s';
		preg_match_all($variablePattern, $content, $templateVariables);
		$content = $this->substituteRepeatingTemplateVariables($content, $templateVariables);

		// fetch remaining variables from template and call substitution routine
		$variablePattern = '/\${([A-Za-z0-9_\-]+):([A-Z_]+)(:.*)?}/s';
		preg_match_all($variablePattern, $content, $templateVariables);
		$content = $this->substituteTemplateVariables($content, $templateVariables);

		return $content;
	}

	/**
	 * substitutes repeating template variables by fetched data
	 * @param	string			$content			template
	 * @param	array			$templateVariables	template variables extracted via preg_match_all
	 * @return	string								processed template
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	private function substituteRepeatingTemplateVariables ($content, $templateVariables) {
		// determine number of repeating sub templates
		$numRepeatDefinitions = count($templateVariables[0]);

		for ($x = 0; $x < $numRepeatDefinitions; $x++) {
			$id = $templateVariables[1][$x];
			$variablePattern = '/\${' . $id . ':repeat}(.*)\${' . $id . ':repeat_end}/s';
			preg_match_all($variablePattern, $content, $templateSubVariables);
			$numSubTemplates = count($templateSubVariables[0]);

			// loop through sub templates and create sub content
			$subContent = null;
			for ($y = 0; $y < $numSubTemplates; $y++) {
				$subContentTemplate = $templateSubVariables[1][$y];

				$variablePattern = '/\${([A-Za-z0-9_\-]+):([A-Z_]+)(:.*)?}/s';
				preg_match_all($variablePattern, $subContentTemplate, $subTemplateSubVariables);
				$numSubTemplateSubVariables = count($subTemplateSubVariables[0]);

				$numDataResults = count($this->templateData[$id]['data']);
				for ($dataOffset = 0; $dataOffset < $numDataResults; $dataOffset++) {
					for ($z = 0; $z < $numSubTemplateSubVariables; $z++) {
						$subContent .= $this->substituteTemplateVariables($subContentTemplate, $subTemplateSubVariables, $dataOffset);
					}
				}

				// substitute template variable by generated sub content
				$content = $content = str_replace(
					$templateSubVariables[0][$y],
					$subContent,
					$content
				);
			}

		}

		return $content;
	}

	/**
	 * substitutes template variables by fetched data
	 * @param	string			$content			template
	 * @param	array			$templateVariables	template variables extracted via preg_match_all
	 * @return	string								processed template
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	private function substituteTemplateVariables ($content, $templateVariables, $offset = 0) {
		// determine number of found template variables
		$numMatches = count($templateVariables[0]);

		// replace template variables by found values
		for ($x = 0; $x < $numMatches; $x++) {
			$id = $templateVariables[1][$x];
			$column = $templateVariables[2][$x];
			$outputWrapperFunction = $templateVariables[3][$x];

			// determine template values and set them
			$substitution = null;
			if (array_key_exists($id, $this->templateData)) {
				$templateData = $this->templateData[$id];
				$substitution = $templateData['data'][$offset][$column];
			}

			// apply function
			if ($templateData['function'] !== false) {
				$substitution = $this->applyFunction(
					$substitution,
					$templateData['function']
				);
			}

			// apply output wrapper
			if (!empty($outputWrapperFunction)) {
				$funcDef = explode(':', $outputWrapperFunction);
				eval("\$funcInstance = $funcDef[1]::getInstance();");

				$funcCall = str_replace('__VALUE__', $substitution, $funcDef[2]);
				eval("\$substitution = \$funcInstance->$funcCall;");
			}

			$content = str_replace(
				$templateVariables[0][$x],
				$substitution,
				$content
			);
		}

		return $content;
	}

	/**
	 * applies post-processing function to fetched value
	 * @param	mixed			$value					value to post-process
	 * @param	array			$function				function definition
	 * @return	mixed									processed value
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	private function applyFunction ($value, array $function) {
		if (array_key_exists('name', $function)) {
			switch ($function['name']) {
				case 'round':
					if (array_key_exists('param', $function)) {
						$precision = (int)$function['param'];
					} else {
						$precision = 0;
					}
					$value = round($value, $precision);
					break;
			}
		} else {
			throw new Cronks_System_StaticContentModelException('applyFunction(): no function name defined!');
		}

		return $value;
	}

}

class Cronks_System_StaticContentModelException extends AppKitException {}

?>