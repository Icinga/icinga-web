<?php

class AppKitExtJsonDocument extends AppKitArrayContainer {

	const PROPERTY_ID				= 'idProperty';
	const PROPERTY_ROOT				= 'root';
	const PROPERTY_TOTAL			= 'totalProperty';
	const PROPERTY_SUCCESS			= 'successProperty';
	const PROPERTY_FIELDS			= 'fields';
	const PROPERTY_META				= 'metaData';
	const PROPERTY_SORTINFO			= 'sortInfo';
	const PROPERTY_START			= 'start';
	const PROPERTY_LIMIT			= 'limit';
	const PROPERTY_NOMETA			= 'no-metadata';
	
	protected $meta		= array ();
	protected $rows		= array ();
	protected $fields	= array ();
	protected $doc		= array ();
	protected $defaults	= array ();


	public function  __construct() {
		$this->initArrayContainer($this->rows);
		$this->resetDoc();
		$this->docDefaults();
	}

	public function setMeta($key, $val=null) {
		$this->meta[$key] = $val;
	}

	public function setDefault($key, $val=null) {
		$this->defaults[$key] = $val;
	}

	public function setSuccess($success=true) {
		$this->setDefault(self::PROPERTY_SUCCESS, $success);
	}

	public function setSortinfo($field, $direction='asc') {
		$this->setMeta(self::PROPERTY_SORTINFO, array(
			'direction'	=> strtolower($direction),
			'field'		=> $field
		));
	}

	public function hasField($name, array $options=array()) {

		if (isset($options['mapping'])) {
			$name = $options['mapping'];
		}

		$options['name'] = $name;

		if (!array_key_exists('sortType', $options)) {
			$options['sortType'] = 'asText';
		}

		$this->fields[$name] = $options;
		return true;
	}
	
	public function applyFieldsFromDoctrineRelation(Doctrine_Table $table) {
		
		foreach ($table->getColumns() as $column=>$meta) {
			$options = array (
				'sortType' => AppKitExtDataInterface::doctrineColumn2ExtSortType($meta['type'])
			);

			if (isset($meta['primary']) && $meta['primary'] == true) {
				$this->setMeta(self::PROPERTY_ID, $column);
			}
			
			$this->hasField($column, $options);
		}
		
	}
	
	public function addDataCollection(Doctrine_Collection $collection) {
		foreach ($collection as $record) {
			$this->offsetSet(null, $record->toArray());
		}
	}

	public function offsetSet($offset, $value) {

		if ($offset !== null) {
			throw new AppKitExtJsonDocumentException('$offset must be <null> - always!');
		}

		if (!is_array($value)) {
			throw new AppKitExtJsonDocumentException('$value must be an associative array!');
		}

		$diff = array_diff_key($value, $this->fields);

		if (is_array($diff) && count($diff)>0) {
			throw new AppKitExtJsonDocumentException('$value keys does not match field data set!');
		}

		// Store needs id maybe
		$this->addIDField($value);

		parent::offsetSet(null, $value);
	}

	private function addIDField(array &$value) {
		$idf = $this->meta[self::PROPERTY_ID];
		if (!array_key_exists($idf, $this->fields)) {
			$this->hasField($idf);
		}

		if (!array_key_exists($idf, $value)) {
			$value[$idf] = (count($this->rows) +1);
		}
	}

	public function setData(array $data) {
		foreach ($data as $row) {
			$this->offsetSet(null, $row);
		}
	}

	public function resetDoc() {
		$this->setSuccess(false);
		$this->setMeta(self::PROPERTY_TOTAL, 0);
		$this->doc = array ();
		$this->rows = array ();
	}

	public function getDoc() {
		if (count($this->doc)<1) {
			$this->buildDoc();
		}
		return $this->doc;
	}

	public function getJson() {
		return json_encode($this->getDoc());
	}

	public function  __toString() {
		return $this->getJson();
	}

	protected function buildDoc() {
		$doc =& $this->doc;
		
		if (isset($this->meta[self::PROPERTY_NOMETA]) && $this->meta[self::PROPERTY_NOMETA] == true) {
			
		}
		else {
		
		$doc[self::PROPERTY_META] = array ();

		$meta =& $doc[self::PROPERTY_META];

		foreach ($this->meta as $k=>$v) {
			$meta[$k] = $v;
		}

		$meta[self::PROPERTY_FIELDS] = array_values($this->fields);

		if ($this->defaults[self::PROPERTY_TOTAL] == 0) {
			$this->setDefault(self::PROPERTY_TOTAL, count($this->rows));
		}

		foreach ($this->defaults as $k=>$v) {
			if (isset($this->meta[$k])) {
				$doc[$this->meta[$k]] = $v;
			}
			else {
				$doc[$k] = $v;
			}
		}
		
		}

		$doc[$this->meta[self::PROPERTY_ROOT]] = $this->rows;
	}

	protected function docDefaults() {
		$this->setMeta(self::PROPERTY_ID, 'id');
		$this->setMeta(self::PROPERTY_ROOT, 'rows');
		$this->setMeta(self::PROPERTY_SUCCESS, 'success');
		$this->setMeta(self::PROPERTY_TOTAL, 'total');
		// $this->setMeta(self::PROPERTY_SORTINFO, new stdClass());
		$this->setSuccess(false);
		$this->setDefault(self::PROPERTY_TOTAL, 0);
	}

}

class AppKitExtJsonDocumentException extends AppKitException {}

?>
