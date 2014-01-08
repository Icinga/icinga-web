<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-present Icinga Developer Team.
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


class Reporting_JasperSchedulerModel extends JasperConfigBaseModel {

    /**
     * @var string
     */
    private $__uri = null;

    /**
     * @var JasperSoapMultipartClient
     */
    private $__client = null;

    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);

        $this->__uri = $this->getParameter('uri');

        $this->__client = $this->getParameter('client');

        if (!$this->__client instanceof JasperSoapMultipartClient) {
            throw new AppKitModelException('Model needs a JasperSoapMultipart client to get work');
        }

        if (!$this->__uri) {
            throw new AppKitModelException('Parameter uri is mandatory');
        }

        if (!$this->checkUri($this->__uri)) {
            throw new AppKitModelException('URI does not match jasper config. Possible security issue!');
        }
    }

    public function getScheduledJobs() {
        $re = $this->__client->getReportJobs($this->__uri);
        $out = array();

        if (is_array($re)) {
            foreach($re as $stdclass) {

                if (isset($stdclass->previousFireTime)) {
                    $stdclass->previousFireTime = $this->context->getTranslationManager()->_d($stdclass->previousFireTime, 'date-tstamp');
                }

                if (isset($stdclass->nextFireTime)) {
                    $stdclass->nextFireTime = $this->context->getTranslationManager()->_d($stdclass->nextFireTime, 'date-tstamp');
                }

                $out[] = (array)$stdclass;
            }
        }
        
        return $out;
    }

    public function getJobDetail($job_id) {
        $out = $this->__client->getJob($job_id);
        
        if (is_array($out->parameters)) {
            foreach ($out->parameters as $parameter) {
                if (preg_match('/\d{4}-\d{2}-\d{2}T?\d{2}:\d{2}:\d{2}/', $parameter->value)) {
                    $tstamp = strtotime($parameter->value);
                    $parameter->value = date('Y-m-d H:i:s', $tstamp);
                }
            } 
        }
        
        return $out;
    }

    public function deleteJob($job_id) {
        $this->__client->deleteJob($job_id);
        return true;
    }
    
    public function editJob($json_document) {
        
        $data = json_decode($json_document);
        $schedulerJob = new JasperSchedulerJob($data);
        $params = $schedulerJob->getSoapStruct();
        
        $re = null;
        
        try {
            if ($data->id) {
                $re = @$this->__client->updateJob($params);
            } else {
                $re = $this->__client->scheduleJob($params);
            }
        } catch(SoapFault $e) {
            throw $e; // Just for debugging withing method: rethrow for productive use
        }
        
        return true;
    }
    
}