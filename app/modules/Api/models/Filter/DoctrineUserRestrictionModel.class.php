<?php

/**
 * Doctrine query filter class that implements restrictions filtering 
 * on "simple" doctrine queries to work as an security layer for
 * Icinga.
 * 
 * @author mhein
 *
 */
class Api_Filter_DoctrineUserRestrictionModel extends IcingaApiBaseModel implements IcingaIDoctrineQueryFilter {
    
    /**
     * @var AppKitSecurityUser
     */
    private $user = null;
    
    /**
     * @var ArrayObject
     */
    private $availableModels = null;
    
    /**
     * @var ArrayObject
     */
    private $queryMap = null;
    
    /**
     * (non-PHPdoc)
     * @see AppKitBaseModel::initialize()
     */
    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);
        
        $this->availableModels = new ArrayObject();
        
        $this->queryMap = new ArrayObject(array(
            IcingaIPrincipalConstants::TYPE_HOSTGROUP => new IcingaDoctrineFilterMap('hostgroups.object', array(
                'hostgroup' => 'name1'
            )),
            
            IcingaIPrincipalConstants::TYPE_SERVICEGROUP => new IcingaDoctrineFilterMap('servicegroups.object', array(
                'servicegroup' => 'name1'
            )),
            
            IcingaIPrincipalConstants::TYPE_CUSTOMVAR_HOST => new IcingaDoctrineFilterMap('customvariablestatus', array(
                'cv_name' => 'varname',
                'cv_value' => 'varvalue'
            )),
            
            IcingaIPrincipalConstants::TYPE_CUSTOMVAR_SERVICE => new IcingaDoctrineFilterMap('customvariablestatus', array(
                            'cv_name' => 'varname',
                            'cv_value' => 'varvalue'
            )),
            
            IcingaIPrincipalConstants::TYPE_CONTACTGROUP => new IcingaDoctrineFilterMap('contactgroups.members.object', array(
                'contactname' => 'name1'
            ))
        ));
    }
    
    /**
     * The hook, private implemented. Call the filter
     * for every implemented DoctrinQueryFilterMap object
     * @param Doctrine_Query_Abstract $query
     */
    private function appendMap(Doctrine_Query_Abstract $query) {
        foreach ($this->availableModels as $model=>$startRelation) {
            $mapObject = $this->getMapObjectForModel($model);
            if ($mapObject instanceof IcingaDoctrineFilterMap) {
                $mapObject->appendTargetValuesFilter($startRelation, $this->getTargetValuesForModel($model), $query);
            }
        }
    }
    
    /**
     * Loads the principal target values from user into
     * a return collection.
     * @param unknown_type $model
     * @return Doctrine_Collection
     */
    private function getTargetValuesForModel($model) {
        $targetValues = new Doctrine_Collection('NsmTargetValue');
        
        if ($this->user->getNsmUser()->hasTarget($model)) {
            if ($model === IcingaIPrincipalConstants::TYPE_CONTACTGROUP) {
                $targetValues->add($this->createUserTargetValue());
                
            } else {
                $targetValues = $this->user->getNsmUser()->getTargetValues($model);
            } 
        }
        
        return $targetValues;
    }
    
    /**
     * Create an artificial TargetValue pair, matching the
     * the current user (if set)
     * @return NsmTargetValue
     */
    private function createUserTargetValue() {
        $targetValue = new NsmTargetValue();
        $targetValue->tv_key = 'contactname';
        $targetValue->tv_val = $this->user->getNsmUser()->user_name;
        return $targetValue;
    }
    
    /**
     * Returns a IcingaDoctrineFilterMap object by model name
     * @param string $model
     * @return IcingaDoctrineFilterMap
     */
    private function getMapObjectForModel($model) {
        if ($this->queryMap->offsetExists($model)) {
            return $this->queryMap->offsetGet($model);
        }
    }
    
    /**
     * User for the security context
     * @param AgaviUser $us
     */
    public function setUser(AgaviUser $us) {
        $this->user = $us;
    }
    
    /**
     * Use user of context for security context
     */
    public function setCurrentUser() {
        $this->user = $this->getContext()->getUser();
    }
    
    /**
     * Which models should be applied?
     * Value of IcingaIPrincipalConstants::TYPE_* constants
     * @param string $name
     */
    public function enableModel($name, $relation) {
        $this->availableModels[$name] = $relation;
    }
    
    /**
     * Which models should be applied?
     * Array of values of IcingaIPrincipalConstants::TYPE_* constants
     * @param array[string] $name
     */
    public function enableModels(array $names) {
        foreach ($names as $name=>$rel) {
            $this->enableModel($name, $rel);
        }
    }
    
    /**
     * (non-PHPdoc)
     * @see IcingaIDoctrineQueryFilter::preQuery()
     */
    public function preQuery(Doctrine_Query_Abstract $query) {
        // PASS
    }
    
    /**
     * (non-PHPdoc)
     * @see IcingaIDoctrineQueryFilter::postQuery()
     */
    public function postQuery(Doctrine_Query_Abstract $query) {
        $this->appendMap($query);
    }
}

?>