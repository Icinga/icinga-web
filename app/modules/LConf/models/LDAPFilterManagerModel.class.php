<?php


class LConf_LDAPFilterManagerModel extends IcingaBaseModel {
	static protected $filterTranslation = array(
		"1" => "exact",
		"2" => "startswith",
		"3" => "endswith",
		"4" => "contains"
	);
	
	public function getFilters() {
		$user = $this->getContext()->getUser();
		$uid = $user->getNsmUser()->get('user_id');
		
		$query = Doctrine_Query::create()
				->select('*')
				->from("LconfFilter lf")
				->innerJoin("lf.NsmUser user")
				->where("user.user_id = ?  OR lf.filter_isglobal = 1",$uid);
		return $query->execute()->toArray();
	}

	public function getFilterById($id) {
		$user = $this->getContext()->getUser();
		$uid = $user->getNsmUser()->get('user_id');
		$query = Doctrine_Query::create()
				->select('*')
				->from("LconfFilter lf")
				->innerJoin("lf.NsmUser user")
				->where("user.user_id = ? OR lf.filter_isglobal = 1",$uid)
				->andWhere("lf.filter_id = ?",$id);
		return $query->execute()->toArray();		
	}
	
	public function removeFilters(array $filterIds) {
		$user = $this->getContext()->getUser();
		$uid = $user->getNsmUser()->get('user_id');
		$query = Doctrine_Query::create()
			->select('*')
			->from("LconfFilter lf")
			->innerJoin("lf.NsmUser user")
			->where("user.user_id = ?",$uid)
			->andWhereIn("lf.filter_id ",$filterIds);
		$result = $query->execute();
		foreach($result as $model)
			$model->delete();
	}
	
	public function addFilter($name,$json) {
		$user = $this->getContext()->getUser();
		$uid = $user->getNsmUser()->get('user_id');
		$model = new LconfFilter();
			$model->set("user_id",$uid);
			$model->set("filter_name",$name);
			$model->set("filter_json",$json);
		$model->save();		
	}
	
	public function modifyFilter($id,$json,$name = null) {
		$user = $this->getContext()->getUser();
		$uid = $user->getNsmUser()->get('user_id');
		$model = Doctrine_Query::create()
			->select('*')
			->from("LconfFilter lf")
			->innerJoin("lf.NsmUser user")
			->where("user.user_id = ?",$uid)
			->andWhere("lf.filter_id = ? ",$id)->execute()->getFirst();
		if(!$model)
			throw new Exception("Invalid id provided!");
		
		if($name)
			$model->set("filter_name",$name);
		$model->set("filter_json",$json);
		$model->save();
	}

	public function getFilterAsLDAPModel($id) {
		$filter = $this->getFilterById($id);
		if(!$filter)
			throw new Exception("Filter ".$id." not found!");

		$filterDesc = json_decode($filter[0]["filter_json"],true);
		return $this->buildFilterGroup("AND",$filterDesc["AND"],false);
	}
		
	protected function parseFilterType($filterGrp,$subFilter,$negated = false) {
		if(isset($subFilter["AND"])) 
			$filterGrp->addFilter($this->buildFilterGroup("AND",$subFilter["AND"],$negated));			
		else if(isset($subFilter["OR"]))
			$filterGrp->addFilter($this->buildFilterGroup("OR",$subFilter["OR"],$negated));
		else if(isset($subFilter["NOT"]))
			$filterGrp->addFilter($this->buildFilterGroup("NOT",$subFilter["NOT"],$negated));
		else if(isset($subFilter["REFERENCE"]))
			$filterGrp->addFilter($this->resolveReference($subFilter["REFERENCE"],$negated));
		else if(isset($subFilter["filter_attribute"]))
			$filterGrp->addFilter($this->buildFilter($subFilter));
	}
	
	protected function buildFilterGroup($type, array $elems,$negated = false) {
		$filterGrp = $this->getContext()->getModel("LDAPFilterGroup","LConf",array($type,$negated));
		foreach($elems as $subFilter) {
			$this->parseFilterType($filterGrp,$subFilter,$negated);
		}
		return $filterGrp;
	}
	
	protected function addNegatedFilters(array $filter,$group,$negated = false) {
		$negated = !$negated;
		foreach($filter as $subFilter) {
			$this->parseFilterType($group,$subFilter,$negated);
		}
	}
	
	protected function resolveReference($referenceArray, $negated = false) {
		$id = $referenceArray["referenceId"];
		$filter = $this->getFilterById($id);
		if(!$filter) {
			$this->getContext()->getLoggerManager()->log("[WARNING] LConf Filter ".$id." does not exist!");
			return $this->getContext()->getModel("LDAPFilterGroup","LConf");
		}	
		$filterDesc = json_decode($filter[0]["filter_json"],true);
		return $this->buildFilterGroup("AND",$filterDesc["AND"],$negated);
	}
	
	protected function buildFilter(array $filter) {
		$filter_type = self::$filterTranslation[$filter["filter_type"]]; 
		$negated = (boolean) @$filter["filter_negated"];
		$key = $filter["filter_attribute"];
		$value = $filter["filter_value"];
		$filter = $this->getContext()->getModel("LDAPFilter","LConf",array($key,$value,$negated,$filter_type));
		return $filter;
	}
}
