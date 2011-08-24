<?php
/**
 * Model that parses logfiles and returns it as an array that can be used (for example) for ExtJS DataStores
 *
 * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
 */
class AppKit_LogParserModel extends AppKitBaseModel {
    private $currentMaxCount;

    public function initialize(AgaviContext $context, array $params = array()) {
        parent::initialize($context,$params);

    }

    public function parseLog($name,$start=0,$end=100,$dir = "desc") {
        $files = $this->getLogFilesByName($name);
        $stringToParse = $this->getEntriesFromFiles($files,$start,$end,$dir);
        return array("total"=>$this->currentMaxCount,"result"=>$this->parseString($stringToParse,$end));

    }

    protected function getEntriesFromFiles(array $files,$start=0,$dir = "desc") {
        if ($dir == "desc") {
            sort($files);
        } else {
            rsort($files);
        }

        $base = $this->getLogDir();
        $string = "";
        $completeCount = 0;
        foreach($files as $file) {
            $content = file_get_contents($base."/".$file);

            if (($count = substr_count($content,"\n[")+1) < $start) {
                $completeCount += $count;
                $start -= $count;
                continue;
            }

            $completeCount += $count;
            $string .= $content;
        }

        if ($start) {
            $string = preg_replace("/(.*\n)/","",$string,$start);
        }

        $this->currentMaxCount = $completeCount;
        return $string;
    }

    protected function parseString($str,$end = 100) {
        $line = preg_split("/^(\[)|(\n(?:\[))/",$str,$end,PREG_SPLIT_NO_EMPTY);

        if (isset($line[$end])) {
            unset($line[$end]);
        }

        $result = array();
        foreach($line as $logEntry) {
            $partResult = array();
            preg_match_all('/^(?<TIME>.*?\]) *?\[(?<SEVERITY>.*?)\] *?(?<MESSAGE>.*)/',$logEntry,$partResult);
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
