<?php

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
                self::$mapJobSimpleTrigger);
            
        } elseif ($data->trigger == self::TRIGGER_CALENDAR) {
            $target['simpleTrigger'] = null;
            $target['calendarTrigger'] = array ();
        }
        
        /*
         * OUTPUT TYPES
         */
        $target['outputFormats'] = array();
        $this->processOutputTypes($target['outputFormats'], $data->outputFormats);
        
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
            $target['mailNotification']['toAddresses'] = null;
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