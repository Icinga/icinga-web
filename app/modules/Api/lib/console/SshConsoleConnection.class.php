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

class ApiSSHNotInstalledException extends AppKitException {};
class ApiInvalidAuthTypeException extends AppKitException {};
class ApiCommandFailedException extends AppKitException {};


class SshConsoleConnection extends BaseConsoleConnection {
    private $connected = false;
    private $host = null;
    private $port = 22;
    private $authType = "password";
    private $pubKeyLocation = "";
    private $privKeyLocation = "";
    private $password = null;
    private $username;
    private $resource;
    private $terminal;
    protected $stdout;
    protected $stderr;
    protected $methods =  array('hostkey'=>'ssh-rsa');
    public function isConnected() {
        return $connected;
    }
    public function connect() {
        if ($this->connected) {
            return true;
        }
        AppKitLogger::verbose("Connecting to ssh instance %s:%s",$this->host,$this->port);

        $success = false;
        $this->resource = new Net_SSH2($this->host,$this->port);
        

        switch ($this->authType) {
            case 'none':
                AppKitLogger::verbose("No-auth login with %s",$this->username);
                $success = $this->resource->login($this->username);
                break;

            case 'password':
                AppKitLogger::verbose("Password login with %s",$this->username);                
                $success = $this->resource->login($this->username,$this->password);
                break;

            case 'key':
                AppKitLogger::verbose("Pub-Key login with ssh key at %s",$this->privKeyLocation);
                if (!is_readable($this->privKeyLocation)) {
                    throw new ApiAuthorisationFailedException("SSH private key not found/readable at the specified location");
                }
                $key = new Crypt_RSA();
                if($this->password)
                    $key->setPassword($this->password);
                $key->loadKey(file_get_contents($this->privKeyLocation));
                $success = $this->resource->login($this->username,$key);
                break;

            default:
                throw new ApiInvalidAuthTypeException("Unknown authtype ".$this->authType);
        }
        AppKitLogger::verbose("Login success: %s",$success);
        if (!$success || !is_object($this->resource)) {
            throw new ApiAuthorisationFailedException("SSH auth for user ".$this->username." failed (using authtype ".$this->authType.') :'.print_r($this->resource->getErrors(),true));
        }

        $this->connected = true;
    }

    public function onDisconnect($reason,$message,$language) {
        $this->connected = false;
    }

    /**
    *   Blocking doesn't quite work with ssh2, so this rather ugly method is used to read
    *   console output. Read is stopped when "username@host:" is reached
    **/
    private function readUntilFinished($cmdString) {
        
        return $this->resource->read('/'.$this->username.'@\w*?:/',NET_SSH2_READ_REGEX);    

    }

    public function exec(Api_Console_ConsoleCommandModel $cmd) {
        $this->connect();
        $cmdString = $cmd->getCommandString();
        $out = $this->resource->exec($cmdString . '; echo -n "|$?"');
        $lines = preg_split('/\|/', $out);
        $ret = (int) array_pop($lines);
        $out = implode('|', $lines);
        $cmd->setOutput($out);
        $cmd->setReturnCode($ret);
    }

    public function __construct(array $settings = array()) {
        $settings = $settings["auth"];
        
        $this->host = $settings["host"];
        $this->port = $settings["port"];
        $this->authType = $settings["method"];
        $this->setupAuth($settings);
    }

    protected function setupAuth(array $settings) {
        

        switch ($this->authType) {
            case 'none':
                $this->username = $settings["user"];
                break;

            case 'password':
                $this->password = $settings["password"];
                $this->username = $settings["user"];
                break;

            case 'key':
                if (isset($settings["password"])) {
                    $this->password = $settings["password"];
                }

                $this->username = $settings["user"];
                
                $this->privKeyLocation = $settings["private-key"];
                break;

            default:
                throw new ApiInvalidAuthTypeException("Unknown auth type ".$this->authType);
        }
    }

    protected function checkSSH2Support() {
       
      
     
    }

}
