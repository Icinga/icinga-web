<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2012 Icinga Developer Team.
// All rights reserved.
// 
// icinga-web is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
// 
// icinga-web is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with icinga-web.  If not, see <http://www.gnu.org/licenses/>.
// -----------------------------------------------------------------------------
// {{{ICINGA_LICENSE_CODE}}}


/**
 * Doctrine query filter class to use the object ids collected by
 * AppKit_Credential_AggregatorModel and apply them to any query
 * 
 * @author mhein
 * @package IcingaWeb
 * @subpackage Api
 *
 */
class Api_Filter_UserObjectIdModel extends IcingaApiBaseModel 
implements IcingaIDoctrineQueryFilter {
    
    /**
     * Maximum items in a SQL IN operation
     * 
     * 1000 is a default oracle restriction
     */
    const MAX_OPERATOR_ELEMENTS = 1000;
    
    /**
     * @var AppKit_Credential_AggregatorModel 
     */
    private $aggregator = null;
    
    /**
     * Fields where we restrict the object ids to
     * @var array
     */
    private $fields = array();
    
    /**
     * Initialize the model, add the aggregator instance to us
     * @param AgaviContext $context
     * @param array $parameters
     * @throws AppKitModelException
     */
    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);
        
        $this->aggregator = 
            $this->getContext()->getModel('Credential.Aggregator', 'AppKit');
        
        if ($this->hasParameter('target_fields')) {
            $fields = $this->getParameter('target_fields');
            
            if (is_string($fields)) {
                $this->setFieldsAsString($fields);
            } elseif (is_array($fields)) {
                $this->setFields($fields);
            } else {
                throw new AppKitModelException("Var type for target_fields"
                        + "parameter not understood");
            }
        }
    }
    
    /**
     * Interface method, just bypass
     * @param Doctrine_Query_Abstract $query
     */
    public function preQuery(Doctrine_Query_Abstract $query) {
        // PASS
    }
    
    /**
     * Appending our object ids
     * @param Doctrine_Query_Abstract $query
     */
    public function postQuery(Doctrine_Query_Abstract $query) {
        if ($this->canApply() === true) {
            if(count($this->getObjectIds()) < 1) {
                $query->andWhere("1 = 2"); 
                 AppKitLogger::verbose("Query is now : %s ",$query->getSqlQuery());
            } else {
                $binds = $this->getObjectIds();

                $template = $this->createQueryTemplate(count($binds));

                $parts = array();

                $all_binds = array();

                foreach ($this->getFields() as $field) {
                    $parts[] = str_replace('{field_name}', $field, $template);
                    $all_binds = array_merge($all_binds, $binds);
                }
                AppKitLogger::verbose("Adding andWhere: %s for parameters %s",'('. implode(' OR ', $parts). ')',$all_binds);
                $query->andWhere('('. implode(' OR ', $parts). ')', $all_binds);
                AppKitLogger::verbose("Query is now : %s ",$query->getSqlQuery());
            }
        }
    }
    
    /**
     * Set a list of target fields as comma separated list
     * @param string $list
     */
    public function setFieldsAsString($list) {
        $this->fields = AppKitArrayUtil::trimSplit($list);
    }
    
    /**
     * Set fields
     * @param array $list
     */
    public function setFields(array $list) {
        $this->fields = $list;
    }
    
    /**
     * Getter for target fields
     * @return array
     */
    public function getFields() {
        return $this->fields;
    }
    
    /**
     * Count fields
     * @return integer
     */
    public function countFields() {
        return count($this->getFields());
    }
    
    /**
     * Checks if we can safely apply the credentials
     * 
     * @return boolean
     */
    public function canApply() {
        AppKitLogger::verbose("Testing canApply: for %s objects and %s countfields",$this->countFields(),count($this->getObjectIds()));
        if ($this->countFields() > 0) {
            AppKitLogger::verbose("Extender can be applied on this query");
            return true;
        }
        AppKitLogger::verbose("Extender *cannot* be applied on this query");
        return false;
    }
    
    /**
     * Creates a sql template with corresponding binds. The field
     * is substituted to {field_name} and must be changed before
     * executing
     * @param integer $count
     * @return string
     */
    private function createQueryTemplate($count) {
        
        $parts = array();
        
        while ($count>0) {
            
            $repeat = $count % self::MAX_OPERATOR_ELEMENTS;
            
            if ($repeat === 0) {
                $repeat = self::MAX_OPERATOR_ELEMENTS;
            }
            
            $parts[] = '{field_name} IN (?'. str_repeat(',?', $repeat-1). ')';
            
            $count -= $repeat;
        }
        
        return '('. implode(' OR ', $parts). ')';
    }
    
    /**
     * Array for object ids
     * @return array
     */
    private function getObjectIds() {
        return array_keys($this->aggregator->getObjectIds());
    }
    
}