<?php 
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2013 Icinga Developer Team.
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


class IcingaDoctrineQueryFilterChain implements IcingaIDoctrineQueryFilter {
    
    /**
     * @var ArrayObject
     */
    private $filters = null;
    
    private $postRun = false;
    
    private $preRun = false;
    
    public function __construct() {
        $this->filters = new ArrayObject();
    }
    
    public function add(IcingaIDoctrineQueryFilter $filter) {
        $this->filters[] = $filter;
    }
    
    public function remove(IcingaDoctrineQueryFilterChain $filter) {
        foreach ($this->filters as $fid=>$checkFilter) {
            if ($checkFilter === $filter) {
                $this->filters->offsetUnset($fid);
                return true;
            }
        }
        return false;
    }
    
    public function hasFilters() {
        return $this->filters->count() ? true : false;
    }
    
    public function preQuery(Doctrine_Query_Abstract $query) {
        foreach ($this->filters as $filter) {
            $filter->preQuery($query);
        }
        $this->preRun = true;
    }
    
    public function postQuery(Doctrine_Query_Abstract $query) {
        foreach ($this->filters as $filter) {
            $filter->postQuery($query);
        }
        $this->postRun = true;
    }
    
    public function canExecutePre() {
        return !$this->preRun && $this->hasFilters();
    }
    
    public function canExecutePost() {
        return !$this->postRun && $this->hasFilters();
    }
}

