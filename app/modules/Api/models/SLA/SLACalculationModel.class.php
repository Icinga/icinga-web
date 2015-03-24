<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2015 Icinga Developer Team.
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


class Api_SLA_SLACalculationModel extends IcingaApiBaseModel {
    private $defaultStartDate;    
    
    
    public function initialize(AgaviContext $ctx, AgaviParameterHolder $p) {
        parent::initialize($context,$p);
        $time = $p->getParameter("default_timespan",AgaviConfig::get("modukle.api.sla_settings,default_timespan"));
    
        $this->defaultStartDate = @strtotime($time);
        if($this->defaultStartDate === false) {
            $ctx->getLoggerManager()->log("Warning: Invalid SLA default timespan "+$time+" provided, using -90 days");
            $this->defaultStartDate = @strtotime("-90 days");    
        }
        if($this->defaultStartDate === false) {
            throw new AppKitException("Couldn't set sla default_timespan");
        }
    }

    public function getAvailabiltyForObjects($objects,Api_SLA_SLAFilterModel $filter = null) {
        if(!$filter)
            $filter = $this->getContext()->getModel ("SLA.SLAFilterModel","SLA");
        
        $filter->setObjectId($objects);  
        $query = IcingaDoctrine_Query::create("icinga")
            ->select("*")
            ->from("IcingaSlahistory sla");
        
        $filter->apply($query);
    }
    
    
}