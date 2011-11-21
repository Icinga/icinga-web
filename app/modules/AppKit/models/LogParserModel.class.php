<?php
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

    protected function getEntriesFromFiles(array $files,$start=0,$end=100,$dir = "desc") {
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
            $string = array_merge($string, $this->readFileDesc($currentLog,$start,$end,$this->entriesRead >= $end));

        }
        return $string;
    }

    private function readFileDesc($log, &$start,$end, $justCount = false) {
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
        fseek($handle,$pos,SEEK_END);
        $string = array();
        while (false !== ($char = fgetc($handle))) {

            if(($char == "\n" ) && ($start-- <= 0)) {
                  
                $line = fgets($handle);
                if($line !== false) {
                    if($line[0] == "[") {
                        $string [] = $line;
                        $this->entriesRead++;
                    } else if(count($string) > 0) {
                        $string[count($string)-1] .= " ".$line;
                    }
                    if($this->entriesRead == $end)
                        break;
                }
            }
            $start = $start < 0 ? 0 : $start; // prevent underflow
            fseek($handle,--$pos,SEEK_END);
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
