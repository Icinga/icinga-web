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
	private $apiData = array();

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
	 * main function to generate HTML content
	 * @param	string			$xmlFile			absolute filename of XML template
	 * @return	unknown_type
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function getContent ($xmlFile) {
		$this->getTemplateData($xmlFile);
		$this->fetchData();
		$html = $this->getHtmlContent();
		return true;
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
					} elseif ($child->nodeName == 'parameter') {
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
						if (!($success = $this->fetchApiData($dataSource))) {
							break;;
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
			$this->apiData = array();
		}

		return $success;
	}

	/**
	 * fetches data via IcingaApi
	 * @param	array			$dataSource			query settings for Api
	 * @return	boolean								true on successful retrieval of data otherwise false
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	private function fetchApiData ($dataSource) {
		$success = true;

		if (!array_key_exists('id', $dataSource)) {

			throw new Cronks_System_StaticContentModelException('fetchApiData(): no id in datasource!');
			$success = false;

		} else {

			$apiSearch = $this->api->API()->createSearch();
			if (!array_key_exists('target', $dataSource)) {

				throw new Cronks_System_StaticContentModelException('fetchApiData(): no target in datasource!');
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
							throw new Cronks_System_StaticContentModelException('fetchApiData(): no column defined in filter definition!');
							$success = false;
						}
						if ($success && !array_key_exists('value', $filter)) {
							throw new Cronks_System_StaticContentModelException('fetchApiData(): no value defined in filter definition!');
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
					$apiRes = $apiSearch->fetch();
					$this->apiData[$dataSource['id']] = $apiRes;
				}

			}

		}

		return $success;
	}

	/*
	 * template parsing
	 */

	/**
	 * generates HTML content from template and fetched data
	 * @param	void
	 * @return	string								HTML content
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	private function getHtmlContent () {
		$html = false;

		if (array_key_exists('template_code', $this->xmlData)) {
			$html = $this->xmlData['template_code'];

			// TODO: continue here
		} else {
			throw new Cronks_System_StaticContentModelException('getHtmlContent(): no template_code defined!');
		}

		return $html;
	}

}

class Cronks_System_StaticContentModelException extends AppKitException {}

?>