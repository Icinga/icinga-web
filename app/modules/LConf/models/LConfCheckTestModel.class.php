<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of LConfCommandCheckModel
 *
 * @author jmosshammer
 */
class LConf_LConfCheckTestModel extends IcingaBaseModel {

    public function tryResourceCfgParse(IcingaConsoleInterface $console,array &$tokens) {
        try {
            $cmd = $this->getContext()->getModel("Console.ConsoleCommand","Api");
            $cmd->setCommand("cat");
            $cmd->stdinFile("lconf_test_resource");
            $console->exec($cmd);
            $output = $cmd->getOutput();
            $res = array();
            $output = preg_replace("/^#.*\n/m","",$output);
            $output = preg_match_all("/([0-9A-Za-z\$ \/_]+)[ \t]*=[ \t]*([0-9A-Za-z\$ \/_]+)/"/*[ \t]*?=[ \t]*?[A-Za-z0-9]+/"*/,$output,$res,PREG_SET_ORDER);
            foreach($res as $cfgEntry) {
                $tokens[trim($cfgEntry[1])] = trim($cfgEntry[2]);
            }
        } catch(Exception $e) {
            // ignore any errors, maybe resource.cfg is not allowed to be read
        }
        

    }

    public function testCheck($commandLine,$tokens) {
        $this->getContext()->getModel("Console.ConsoleInterface","Api");
        $cfg = AgaviConfig::get("modules.lconf.lconfTestCheck",array());

        $instance = (isset($cfg["checkInstance"]) ? $cfg["checkInstance"] : null);
        try {
            $console = $this->getContext()->getModel("SudoConsoleInterfaceDecorator","LConf",array(
                "icingaInstance" => $instance
            ));
        } catch(ApiUnknownInstanceException $e) {
            return array(
                "success"       => false,
                "returnCode"    => -1,
                "output"        => "Configuration error: Instance ".$instance." is not defined",
                "hint"          => ""
            );
        }
        

        
        $this->tryResourceCfgParse($console, $tokens);
        $cmd = $this->getContext()->getModel("ConsoleCommand","LConf");
        foreach($tokens as $token=>$replacement) {
            $commandLine = str_replace($token,$replacement,$commandLine);
        }
        $pathInfo = pathinfo($commandLine);
        if($pathInfo["dirname"] == ".") {
            $hosts = $console->getHostName();
            $commandLine = str_replace("*","",
                AccessConfig::expandSymbol(
                    "lconf_test_libexec",
                    "x",
                    $hosts[0]
                )
            ).$commandLine;
        }
        
        $user = null;
        if(isset($cfg["checkUser"]))
            $user = $cfg["checkUser"];
        $console->setSudoUser($user);
        $cmd->setCommand($commandLine);
        $result = array(
            "success"       => false,
            "returnCode"    => -1,
            "output"        => "",
            "hint"          => ""
        );
        try {
            $console->exec($cmd);
            $result["returnCode"] = $cmd->getReturnCode();
            if($result["returnCode"] == 127) {
                $result["success"] = false;
                $result["output"] = "Permission denied for user ".$user;
            } else {
                $result["success"] = true;
                $result["output"] = $cmd->getOutput();
                if($result["output"] == "" && $result["returnCode"] == 1) {
                    $result["hint"] = "
                        Check returned warning, but no output. <br/>
                        It could be that your icinga-web user wasn't able to perform
                        the check as the user '".$user."'. It's best to check your server
                        log if your plugin isn't expected to produce an empty output
                    ";
                }
            }
            
        } catch(Exception $e) {
            $result["success"] = false;
            $result["output"] = "An exception occured: <br/> ".$e->getMessage();
            
        }
        return $result;
    }
}
