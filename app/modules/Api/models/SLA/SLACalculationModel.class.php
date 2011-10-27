<?php

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