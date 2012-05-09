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


class JasperSchedulerJob {
    const XMLNS_XSD = 'http://www.w3.org/2001/XMLSchema';
    
    const TRIGGER_SIMPLE = 'recurrence-simple';
    const TRIGGER_CALENDAR = 'recurrence-calendar';
    const TRIGGER_NONE = 'recurrence-none';
    
    private static $mapBasicFields = array (
        'id' => 1, 
        'version' => 0, 
        'label' => null, 
        'description' => null,
        'reportUnitURI' => null, 
        'baseOutputFilename' => null
    );
    
    private static $mapJobSimpleTrigger = array (
        'id' => 0, 
        'version' => 0, 
        'timezone' => null, 
        'startDate' => null,
        'endDate' => null, 
        'occurrenceCount' => -1, 
        'recurrenceInterval' => null,
        'recurrenceIntervalUnit' => null
    );
    
    private static $mapJobCalendarTrigger = array (
        'id' => 0, 
        'version' => 0, 
        'timezone' => null, 
        'startDate' => null,
        'endDate' => null,
        'minutes' => null,
        'hours' => null,
        'daysType' => null,
        'weekDays' => null,
        'monthDays' => null,
        'months' => null
    );
    
    private static $mapRepositoryDestination = array (
        'id' => 0,
        'version' => 0,
        'folderURI' => null,
        'sequentialFilenames' => false,
        'overwriteFiles' => false,
        'outputDescription' => null,
        'timestampPattern' => null
    );
    
    private static $mapMailNotification = array (
        'id' => 0,
        'version' => 0,
        'toAddresses' => null,
        'subject' => null,
        'messageText' => null,
        'resultSendType' => 'SEND',
        'skipEmptyReports' => false
    );
    
    
    
    public function __construct(stdClass $data) {
        $this->__data = $data;
    }
    
    public function getSoapStruct() {
        $struct = array ();
        
        $this->buildSoapStruct($this->__data, $struct);
        
//         var_dump($struct);
//         die;
        
        return $struct;
    }
    
    private function buildSoapStruct(stdClass $data, &$target=null) {
        $ref = new ReflectionObject($data);
        
        /*
         * BASIC
         */
        $this->processFields($target, $data, self::$mapBasicFields);
        
        /*
         * TRIGGER
         */
        if ($data->trigger == self::TRIGGER_SIMPLE) {
            
            $target['simpleTrigger'] = array ();
            $target['calendarTrigger'] = null;
            
            $this->processFields(
                $target['simpleTrigger'], 
                $data->simpleTrigger, 
                self::$mapJobSimpleTrigger
            );
            
        } elseif ($data->trigger == self::TRIGGER_CALENDAR) {
            $target['simpleTrigger'] = null;
            $target['calendarTrigger'] = array ();
            
            $this->processFields(
                $target['calendarTrigger'],
                $data->calendarTrigger,
                self::$mapJobCalendarTrigger
            );
            
            if (!$data->calendarTrigger->months[0]) {
                $data->calendarTrigger->months = null;
            }
            
            if (!$data->calendarTrigger->weekDays[0]) {
                $data->calendarTrigger->weekDays = null;
            }
        }
        
        /*
         * OUTPUT TYPES
         */
        if ($ref->hasProperty('outputFormats')) {
            $target['outputFormats'] = array();
            $this->processOutputTypes($target['outputFormats'], $data->outputFormats);
        } else {
            throw new JasperSchedulerJobException('OutputFormats not defined');
        }
        /*
         * REPOSITORY DESTINATION
         */
        $target['repositoryDestination'] = array();
        $this->processFields(
            $target['repositoryDestination'], 
            $data->repositoryDestination, 
            self::$mapRepositoryDestination
         );
        
        /*
         * PARAMETERS
         */
        $target['parameters'] = array ();
        $this->processParameters($target['parameters'], $data->parameters);
        
        /*
         * MAIL NOTIFICATION
         */
        $target['mailNotification'] = array ();
        $this->processFields($target['mailNotification'], $data->mailNotification, self::$mapMailNotification);
        
        if (!$data->mailNotification->toAddresses[0]) {
            $target['mailNotification'] = null;
        }
        
        return true;
    }
    
    private function processOutputTypes(array &$target, stdClass $data) {
        foreach ((array)$data as $outputFormat=>$garbage) {
            $target[] = $outputFormat;
        }
    }
    
    private function processParameters(array &$target, stdClass $data) {
        foreach ((array)$data as $paramKey=>$paramValue) {
            
            if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $paramValue)) {
                $tstamp = strtotime($paramValue);
                $paramValue = new SoapVar(date('c', $tstamp), SOAP_ENC_OBJECT, 'dateTime', self::XMLNS_XSD);
            }
            
            $target[] = array(
                'name' => $paramKey,
                'value' => $paramValue
            );
        }
    }
    
    private function processFields(array &$target, stdClass $data, array $attributeMap) {
        $ref = new ReflectionObject($data);
        foreach ($attributeMap as $attributeName=>$attributeDefault) {
            $value = null;
            
            if ($ref->hasProperty($attributeName)) {
                $property = $ref->getProperty($attributeName);
                $value = $property->getValue($data);
            } elseif ($attributeDefault !== null) {
                $value = $attributeDefault;
            }
            
            if ($value || is_bool($value) || is_numeric($value)) {
                $target[$attributeName] = $value;
            }
        }
    }
}

class JasperSchedulerJobException extends AppKitException {}