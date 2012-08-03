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
 * @author Christian Doebler <christian.doebler@netways.de>
 */
class Web_Icinga_ApiSimpleDataProviderModel extends IcingaWebBaseModel {

    const MODE_NAMED_KEYS   = 'namedKeys';
    const MODE_ARRAY_KEYVAL = 'arrayKeyValue';

    private $configAll      = array();
    private $config         = array();
    private $resultColumns  = array();
    private $template       = null;
    private $mode           = array();

    /**
     * @var AgaviTranslationManager
     */
    private $tm             = null;

    /**
     *
     * Enter description here ...
     * @var IcingaApiSearch
     */
    private $apiSearch      = false;

    private $filter         = false;

    private $filterSet      = false;

    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);

        $this->configAll = AgaviConfig::get('modules.web.simpledataprovider');
        $this->apiSearch = $this->getContext()->getModel('Icinga.ApiContainer', 'Web')->createSearch();
        
        /**
         * @todo Use new Implementation (Doctrine Query)
         */ 
        IcingaPrincipalTargetTool::applyApiSecurityPrincipals($this->apiSearch);

        $this->tm = $this->getContext()->getTranslationManager();
    }

    public function setSourceId($srcId = false) {
        if (array_key_exists($srcId, $this->configAll)) {
            $this->config = $this->configAll[$srcId];

            if (isset($this->config['xtemplate_code'])) {
                $this->template = $this->config['xtemplate_code'];
            }

            if (isset($this->config['result_type'])) {
                $this->mode = $this->config['result_type'];
            } else {
                $this->mode = self::MODE_ARRAY_KEYVAL;
            }

            $this->setSearchTarget($this->config['target']);

            foreach($this->config['result_columns'] as $coldef) {
                if (isset($coldef['field'])) {
                    $this->resultColumns[$coldef['field']]=$coldef;
                }
            }
            $this->setResultColumns(array_keys($this->resultColumns));
        }

        return $this;
    }

    public function setFilter($filter = false) {
        if (is_array($filter)) {
            $this->filter = $filter;
            $this->applyFilter();
        }

        return $this;
    }

    private function applyFilter() {
        if (array_key_exists('filter', $this->config) && $this->config['filter'] !== false && is_array($this->config['filter'])) {
            $filterDefs = (array_key_exists('column', $this->config['filter'])) ? array($this->config['filter']) : $this->config['filter'];
            foreach($filterDefs as $filter) {
                $apiFilter = array($filter['column'], $filter['value']);

                if (array_key_exists('match_type', $filter)) {
                    array_push($apiFilter, constant($filter['match_type']));
                }

                $this->setSearchFilter(array($apiFilter));
            }
            $this->config['filter'] = false;
        }

        if (array_key_exists('user_filters', $this->config) && $this->config['user_filters'] !== false && $this->filter !== false) {
            $filterDefs = $this->config['user_filters'];
            foreach($this->filter as $key => $value) {
                if (array_key_exists($key, $filterDefs)) {
                    $filter = array($filterDefs[$key]['column'], $value);

                    if (array_key_exists('match_type', $filterDefs[$key])) {
                        array_push($filter, constant($filterDefs[$key]['match_type']));
                    }

                    $this->setSearchFilter(array($filter));
                }
            }
            $this->config['user_filters'] = false;
        }

        $this->filterSet = true;
        return $this;
    }

    public function setOrder() {
        if (array_key_exists('order', $this->config) && $this->config['order'] !== false) {
            $orderDefs = (array_key_exists('column', $this->config['order'])) ? array($this->config['order']) : $this->config['order'];
            foreach($orderDefs as $currentDef) {
                if (array_key_exists('direction', $currentDef)) {
                    $this->setSearchOrder($currentDef['column'], $currentDef['direction']);
                } else {
                    $this->setSearchOrder($currentDef['column']);
                }
            }
        }

        return $this;
    }

    public function setLimit() {
        if (array_key_exists('limit', $this->config) && $this->config['limit'] !== false && is_array($this->config['limit'])) {
            $limitDefs = $this->config['limit'];

            if (array_key_exists('length', $limitDefs)) {
                $this->setSearchLimit($limitDefs['start'], $limitDefs['length']);
            } else {
                $this->setSearchLimit($limitDefs['start']);
            }
        }

        return $this;
    }

    private function rewriteColumn($key, &$val) {

        if (isset($this->resultColumns[$key]['type'])) {
            switch (strtolower($this->resultColumns[$key]['type'])) {
                case 'url':
                    if (isset($val) && strlen($val)) {
                        $val = (string)AppKitXmlTag::create('a', $val)
                               ->addAttribute('href', $val)
                               ->addAttribute('target', '_blank');
                    }

                    break;

                case 'boolean':
                case 'bool':
                    $val = ((bool)$val==true) ? $this->tm->_('True') : $this->tm->_('False');
                    break;

                case 'timestamp':
                case 'datetime':
                    $check = strtotime($val);
                    if ($check <= 0) {
                        $val = '(null)';
                    } else {
                        $val = $this->tm->_d($val, 'date-tstamp');
                    }
                    break;

                case 'hoststate':
                    $val = (string)IcingaHostStateInfo::Create((int)$val)->getCurrentStateAsHtml();
                    break;

                case 'servicestate':
                    $val = (string)IcingaServiceStateInfo::Create((int)$val)->getCurrentStateAsHtml();
                    break;

                case 'checktype':
                    $val = $this->tm->_(IcingaConstantResolver::activeCheckType($val));

                    break;

                case 'float':
                    $val = sprintf('%.2f', $val);
                    break;
            }
        }

        return $val;
    }

    private function prepareOutput(/*IcingaApiResult*/ $result) {
        $out = array();
        foreach($result as $row) {
            $tmp = array();
            $test = array();
            foreach($row->getRow() as $key=>$val) {

                $key = strtoupper($key);

                if (in_array($key, $test)) {
                    continue;
                } else {
                    $test[] = $key;
                }

                if ($this->mode == self::MODE_ARRAY_KEYVAL) {
                    $tmp[] = array(
                                 'key' => $this->tm->_($key),
                                 'val' => $this->rewriteColumn($key, $val)
                             );
                } else {
                    $tmp[$key] = utf8_decode($this->rewriteColumn($key, $val));
                }
            }
            $out[] = $tmp;
        }

        return $out;
    }

    public function fetch() {
        $result = false;

        if ($this->filterSet === false) {
            $this->applyFilter();
        }

        $this->setOrder();
        $this->setLimit();
        $result = $this->prepareOutput($this->apiSearch->fetch());

        return $result;
    }

    public function getTemplateCode() {
        if ($this->template !== null && strlen($this->template)) {
            return $this->template;
        }

        return false;
    }

    /*
     * API WRAPPERS
     */
    private function setSearchTarget($target) {
        $this->apiSearch->setSearchTarget(constant($target));
        return $this;
    }

    private function setSearchFilter($filter, $value = false, $defaultMatch = IcingaApiConstants::MATCH_EXACT) {
        if ($defaultMatch != IcingaApiConstants::MATCH_EXACT && defined($defaultMatch)) {
            $defaultMatch = constant($defaultMatch);
        }

        $this->apiSearch->setSearchFilter($filter, $value, $defaultMatch);
        return $this;
    }

    private function setResultColumns($column) {
        $this->apiSearch->setResultColumns($column);
        return $this;
    }

    private function setSearchOrder($column, $direction = 'asc') {
        $this->apiSearch->setSearchOrder($column, $direction);
        return $this;
    }

    private function setSearchLimit($start, $length = false) {
        $this->apiSearch->setSearchLimit($start, $length);
        return $this;
    }

}

?>
