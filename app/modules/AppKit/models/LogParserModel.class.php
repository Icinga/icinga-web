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
 * Model that parses logfiles and returns it as an array that can be used (for example) for ExtJS DataStores
 *
 * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
 */
class AppKit_LogParserModel extends AppKitBaseModel {
    private static $LOG_FORMAT = "[%s] [%s] %s";
    private $entriesRead = 0;
    private $total = 0;
    public function initialize(AgaviContext $context, array $params = array()) {
        parent::initialize($context,$params);

    }

    public function parseLog($name,$start=0,$end=100,$dir = "desc") {
        $files = $this->getLogFilesByName($name);
        $stringToParse = $this->getEntriesFromFiles($files,$start,$end,$dir);
        return array("total"=>$this->total,"result"=>$this->parseLogEntries($stringToParse,$end));

    }

    protected function getEntriesFromFiles(array $files,$start=0,$end=15,$dir = "desc") {
        if ($dir == "desc") {
            rsort($files);
        } else {
            sort($files);
        }
        $string = array();
        $base = $this->getLogDir();
        $this->entriesRead = 0;
        $this->total = 0;

        foreach($files as $file) {
            $currentLog = $base."/".$file;
            $this->readFileDesc($currentLog,$start,$end,$this->entriesRead >= $end,$string);
            

        }
        return $string;
    }

   
    
    private function readFileDesc($log, &$start,$end, $justCount = false,array &$string) {
        $handle = fopen($log,"r");
        if(!$handle)
            return array();
        $pos = -1;

        while (!feof($handle)) {
            if ($f = fgets($handle)) {
                if($f[0] == "[")
                    $this->total++;
            }
        }
        if($justCount)
            return array();
        fseek($handle,0,SEEK_SET);
        $currentLine = "";
        
        while (($line = fgets($handle)) !== false) {
            
            if($line[0] == "[") {
                if($currentLine != "" && $start < 0) {
                    $string[] = $currentLine;
                    $this->entriesRead++;
                }
                $currentLine = "";
                if($start >= -1)
                    $start--; 
            }
            $currentLine .= $line;
            if(count($string) >= $end-1)
                break;
        }
        if($currentLine != "" && $start < 0) {
            $string[] = $currentLine;
            $this->entriesRead++;
        }
        fclose($handle);
        return $string;
    }

    private function readFileAsc($log, $start,$end) {
        return array();
    }

    public function sortByTime($x,$y) {
        if(!isset($x["Time"]) || !isset($y["Time"]) ||
           !isset($x["Time"][0]) || !isset($y["Time"][0]))
           return 0;
        $tX = strtotime($x["Time"][0]);
        $tY = strtotime($y["Time"][0]);
        if($tX == $tY)
            return 0;
        else if($tX < $tY)
            return 1;
        else
            return -1;
    }

    protected function parseLogEntries(array $str,$end = 100) {


        $result = array();
        foreach($str as  $logEntry) {
            $partResult = array();
            $match = preg_match_all('/^\[(?<TIME>.*?)\] *?\[(?<SEVERITY>.*?)\] *?(?<MESSAGE>.*)/',$logEntry,$partResult);
            if($match)
                $result[] = array("Time"=>$partResult["TIME"],"Severity"=>$partResult["SEVERITY"],"Message"=>$partResult["MESSAGE"]);
        }
        
        return $result;
    }

    public function getLogAsArray($logname, $pattern = null) {
        $availableLogs = $this->getLogListing();

        if (!in_array($availableLogs[$logname])) {
            return array();
        }

        if ($pattern) {
            $this->pattern = $pattern;
        }

        return $this->parseLog($logname);
    }

    protected function getLogDir() {
        return AppKitAgaviUtil::replaceConfigVars(AgaviConfig::get('org.icinga.appkit.log_path'));
    }

    public function getLogFilesByName($name) {
        $logDir = $this->getLogDir();
        $files = scandir($logDir);
        $result = array();
        foreach($files as $file) {
            if (preg_match("/^".$name.".*/",$file)) {
                $result[] = $file;
            }
        }
        return $result;
    }

    public function getLogListing() {
        $logDir = $this->getLogDir();
        $files = scandir($logDir);
        $availableLogFiles = array();

        foreach($files as $file) {
            if (substr($file,-3) == 'log') {
                $file = preg_replace("/^([\w\-]+)\-\d{4}\-\d{2}\-\d{2}.log$/", "\\1",$file);

                if (!isset($availableLogFiles[$file])) {
                    $availableLogFiles[$file] = $file;
                }
            }

        }

        return $availableLogFiles;
    }
}

?>
