<?php
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

        $success = false;
        $this->resource = new Net_SSH2($this->host,$this->port);
        

        switch ($this->authType) {
            case 'none':
                $success = $this->resource->login($this->username);
                break;

            case 'password':
                
                $success = $this->resource->login($this->username,$this->password);
                break;

            case 'key':
                if (!is_readable($this->pubKeyLocation)) {
                    throw new ApiAuthorisationFailedException("SSH public key not found/readable at the specified location");
                }

                if (!is_readable($this->privKeyLocation)) {
                    throw new ApiAuthorisationFailedException("SSH private key not found/readable at the specified location");
                }
                $key = new Crypt_RSA();
                if($this->password)
                    $key->loadKey($this->password);
                $key->loadKey(file_get_contents($this->privKeyLocation));
                $success = $this->resource->login($this->username,$key);
                break;

            default:
                throw new ApiInvalidAuthTypeException("Unknown authtype ".$this->authType);
        }

        if (!$success || !is_object($this->resource)) {
            throw new ApiAuthorisationFailedException("SSH auth for user ".$this->username." failed (using authtype ".$this->authType.') :'.print_r($this->resource->getErrors(),true));
        }

        $this->connected = true;
    }

    public function onDisconnect($reason,$message,$language) {
        $this->connected = false;
    }

    /**
    *	Blocking doesn't quite work with ssh2, so this rather ugly method is used to read
    *	console output. Read is stopped when "username@host:" is reached
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
                
                $this->privKeyLocation = $settings["privKey"];
                break;

            default:
                throw new ApiInvalidAuthTypeException("Unknown auth type ".$this->authType);
        }
    }

    protected function checkSSH2Support() {
       
      
     
    }

}
