<?
require_once(dirname(dirname(__FILE__)). '/lib/doctrine/lib/Doctrine.php');

spl_autoload_register(array('Doctrine', 'autoload'));

class doctrineDeployTool {
	private $modelPath = null;

	private $dbtype = "mysql";
	private $dbuser = null;
	private $dbpass = null;
	private $dbhost = "localhost";
	private $dbname = null; 
	
	private $createDB = false;
	private $target = null;
	private $cliActions = null;
	private $cli = false;
	private $stdIn = null;
	private $connected = false;
	private $MigrationPath = null;
	
	public static $POSSIBLE_ACTIONS = array("dropDB"=>true,"importDB"=>true,"exportDB"=>true,"dumpData"=>true,"migrate"=>true,"importData"=>true,"createMigrationClasses"=>true);
	public static $OPTS = array("createDB","dropDB","exportDB","importDB","models:","target:","migrationPath:","dumpData","migrate","importData","createMigrationClasses","dbtype:","dbuser:","dbhost:","dbname:","dbpass:");

	public function setModelPath($path) {
		$this->modelPath = $path;
	}
	public function setDbType($type) {
		$this->dbtype = $type;
	}
	public function setDbUser($user) {
		$this->dbuser = "user";
	}
	public function setDbPass($pass) {
		$this->dbpass = $pass;
	}
	public function setDbHost($host) {
		$this->dbhost = $host;	
	}
	public function setDbName($name) {
		$this->dbname = $name;
	}  
	public function setCreateDB($bool) {
		$this->createDB = (boolean) $bool;
	}
	public function setTarget($target) {
		$this->target = $target;
	}
	public function setCliActions(array $actions) {
		$this->cliActions = $actions;
	}
	public function setCli($bool) {
		$this->cli = $bool;
	}
	public function setConnected($bool) {
		$this->isConnected = $bool;
	}
	public function setMigrationPath($path) {
		$this->migrationPath = $path;
	}
	
	public function getModelPath() {
		return $this->modelPath;
	}
	public function getDbType() {
		return $this->dbtype;
	}
	public function getDbUser() {
		return $this->dbuser;
	}
	public function getDbPass() {
		return $this->dbpass;
	}
	public function getDbHost() {
		return $this->dbhost;
	}
	public function getDbName() {
		return $this->dbname;
	}
	public function getCreateDB() {
		return $this->createDB;
	}	
	public function getTarget() {
		return $this->target;
	}
	public function getCliActions() {
		return $this->cliActions;
	}
	public function isCli() {
		return $this->cli;
	}
	public function isConnected() {
		return $this->connected;
	}
	public function getMigrationPath() {
		return $this->migrationPath;
	}
	
	private function bootstrap() {
		require_once(dirname(dirname(__FILE__)). '/lib/doctrine/lib/Doctrine.php');
		spl_autoload_register(array('Doctrine', 'autoload'));		
	}
	
	private function initFromCli($args) {

		$this->setCli(true);
		$this->stdIn = fopen("php://stdin","r");

		// read options
		
		$vars = getopt("",self::$OPTS);
		if((isset($vars["dumpData"]) || isset($vars["createMigrationClasses"])) && (isset($vars["import"]) || isset($vars["migrate"]))) {
			$this->stdOut("Can't perform an import and an export operation at the same time");
			exit(1);
		}
		$this->setCliActions(array_intersect_key(self::$POSSIBLE_ACTIONS,$vars));
		
		if(isset($vars["models"]))
			$this->setModelPath($vars["models"]);
		if(isset($vars["migrationPath"]))
			$this->setMigrationPath($vars["migrationPath"]);
		if(isset($vars["target"]))
			$this->setTarget($vars["target"]);
		if(isset($vars["createDB"]))
			$this->setCreateDB(true);
			
		$dbSettings = array("dbtype","dbuser","dbhost","dbname","dbpass");
		foreach($dbSettings as $setting) {
			if(isset($vars[$setting]))
				$this->{$setting} = $vars[$setting];	
		}
	}
	
	public function __construct($argv = null) {

		if(isset($argv)) {
			$this->initFromCli($argv);
		} 
		if($this->isCli()) 
			$this->performCliActions();
	}
	
	protected function performCliActions() {
		$actions = $this->getCliActions();
		if(empty($actions))
			$this->error("No actions defined!");
		
		
		if(isset($actions["migrate"]))
			$this->performMigrate();
	
		if(isset($actions["dumpData"]))
			$this->dumpData();
		
		if(isset($actions["importData"]))
			$this->importData();
	
		if(isset($actions["createMigrationClasses"]))
			$this->createMigration();
		
		if(isset($actions["exportDB"]))
			$this->exportDB();

		if(isset($actions["importDB"]))
			$this->importDB();
	
		if(isset($actions["dropDB"]))
			$this->dropDatabase();
	}
	
	public function performMigrate($create = false) {
		// $target is migrationpath 
		if(!$this->getMigrationPath())
			$this->error("No migrationPath defined via --migrationPath!");
		
		if($this->isCli())
			$this->askForDB();
		$conn = $this->connect();
		if($create)
			$conn->createDatabase();

		$migration = new Doctrine_Migration($this->getMigrationPath());
		try {
			$migration->migrate();
		} catch(Exception $e) {
			$this->stdOut("The following error occured during migration ".$e->getMessage());
			$prompt = false;
			$this->stdIn($prompt,"Proceed (y/n)?","n");
			if($prompt != "y")
				exit(1);
		}
	}
	
	public function dumpData() {
		if($this->isCli())
			$this->askForDB();
		$this->connect();
		// $target is dump target	
		if(!$this->getTarget())
			$this->error("No dump target defined via --target!");

		if(!$this->getModelPath()) 
			$this->error("No path to models defined via --models!");

		
		if(!file_exists($this->getTarget()))
			mkdir($this->getTarget());
			
		Doctrine_Core::loadModels($this->getModelPath()."/generated");
		Doctrine_Core::loadModels($this->getModelPath());
		Doctrine_Core::dumpData($this->getTarget(),true);
	}
	
	public function dropDatabase() {
		if($this->isCli())
			$this->askForDB();
		$conn =  $this->connect();
		$conn->dropDatabase();
	}
	
	public function importData() {
		if($this->isCli())
			$this->askForDB();
		$this->connect();
		// $target is dump file
		if(!$this->getTarget())
			$this->error("No dump target defined via --target!");
		if(!$this->getModelPath())
			$this->error("No model path defined via --models!");

		Doctrine_Core::loadModels($this->getModelPath()."/generated");
		Doctrine_Core::loadModels($this->getModelPath());
		Doctrine_Core::loadData($this->getTarget());
	}
	
	public function createMigration() {
		// $target is mirgrationPath
		if(!$this->getMigrationPath()) 
			$this->error("No migrationPath defined via --migrationPath!");
				
		if(!$this->getModelPath())
			$this->error("No model path defined via --models!");
		
		if($this->isCli())
			$this->askForDB();
		$conn = $this->connect();
		if(!file_exists($this->getMigrationPath()))
			mkdir($this->getMigrationPath());
		Doctrine_Core::generateMigrationsFromModels($this->getMigrationPath(),$this->getModelPath());
	}
	
	protected function exportDB() {
		if(!$this->getTarget())
			$this->error("No export target defined via --target!");
		if(!$this->getModelPath())
			$this->error("No model path defined via --models!");
		
		if(file_exists($this->getTarget())) // we don't want to remove an existing directory
			$this->error("Target already exists!");	
		mkdir($this->getTarget());
		$origTarget = $this->getTarget(); 
		mkdir($this->getTarget()."/migration");
		mkdir($this->getTarget()."/dump");

		$this->setMigrationPath($this->getTarget()."/migration");
		$this->createMigration();
		
		$this->setTarget($this->getTarget()."/dump");
		$this->dumpData();
		$this->setTarget($origTarget);				
	
		system("tar -czf ".$this->getTarget().".tar.gz ".$this->getTarget());
		system("rm -r ".$this->getTarget());
	}
	
	protected function importDB() {
		if(!$this->getTarget())
			$this->error("No export target defined via --target!");
		if(!$this->getModelPath())
			$this->error("No model path defined via --models!");
		
		if(!file_exists($this->getTarget()) || substr($this->getTarget(),-7) != '.tar.gz')
			$this->error("Wrong dbDump!");
		// Create temporary directory with content of the export file
		$tmpDir = "dbDump_tmp".str_shuffle("ab3325ewg");
		mkdir($tmpDir);
		system("tar -zxf ".$this->getTarget()." -C ".$tmpDir);
		// to the import
		$path = substr($this->getTarget(),0,-7) ;
		$this->setMigrationPath($tmpDir."/".$path."/migration");
		$this->setTarget($tmpDir."/".$path."/dump");
		$this->performMigrate(true);
		$this->importData();
		// remove directory
		system("rm -r ".$tmpDir);
	}
	
	protected function connect() {
		if($this->isConnected())
			return $this->__conn;	
		$dsn = $this->getDbType()."://".$this->getDbUser().":".$this->getDbPass()."@".$this->getDbHost()."/".$this->getDbName();
		$this->__conn = Doctrine_Manager::connection($dsn);
		return $this->__conn;
	}
	
	protected function askForDB($force = false) {
		if($this->dbtype && $this->dbuser && $this->dbpass && $this->dbhost && $this->dbname && !$force)
			return true;
			
		$this->stdIn($this->dbtype,"DB Type (mysql): ", "mysql");
		$this->stdIn($this->dbuser,"DB User: ");
		$this->stdIn($this->dbpass,"DB Pass: ");
		$this->stdIn($this->dbhost,"DB Host (localhost): ","localhost");
		$this->stdIn($this->dbname,"DB Name: ");
	}
	
	public function stdIn(&$var,$prompt = null,$default = null) {
		if($prompt)
			$this->stdOut($prompt,false);
			
		if($this->isCli())  
			$var = trim(fread($this->stdIn,64));
		if(!$var)
			$var = $default;
	}
	
	public function stdOut($string,$nl = true) {
		if($this->isCli()) 
			printf("\x1b[2;34m%s\x1b[m".($nl ? "\n" : ''),$string);
		else 
			echo $string."<br/>";
	}
	
	public function __destruct() {
		if(!$this->isCli() && $this->stdIn)
			fclose($this->stdIn);	
	}
	
	public function printCliUsage() {
		$this->stdOut("\nDoctrine deployment tool");
		$this->stdOut("Usage: php ./doctrineDeployTool.php [OPTIONS]");
		$this->stdOut("\nPossible options:");
		
		$this->stdOut("\n*******************EXPORT FUNCTIONS**************************");
		$this->stdOut("\ndumpData --models [PATH TO MODELS] --target [DUMP TARGET PATH");
		$this->stdOut("\t\tCreates a datadump of all tables defined by the models found in --models");
		$this->stdOut("\ncreateMigrationClasses --models [PATH TO MODELS] --migrationPath [DUMP TARGET MIGRATION CLASSES]");
		$this->stdOut("\t\tCreates migration classes from all models in --models");

		$this->stdOut("\n*******************IMPORT FUNCTIONS**************************");
		$this->stdOut("\nimportData --target [DUMP TARGET FILE]");
		$this->stdOut("\t\tImports the dump found at --target");
		$this->stdOut("\nmigrate --migrationPath [PATH TO MIGRATION CLASSES] (--createDB)");
		$this->stdOut("\t\tPerforms a migration to the given migration classes. If createDB is set, a new DB will be created");
		
		$this->stdOut("\n******************MACRO FUNCTIONS****************************");
		$this->stdOut("\nexportDB --target [Export folder] --models");
		$this->stdOut("\t\tCreates both a structure and a datadump for the given models");
		$this->stdOut("\nimportDB --target [Export file] --models");
		$this->stdOut("\t\tImports a database export");
		$this->stdOut("\n");
		$this->stdOut("Import and export functions are mutually exclusive");
	}
	
	public function error($msg,$returnCode = 1) {

		$this->printCliUsage();	
		$this->stdOut("\nError: ".$msg);
		exit($returnCode);
	}
}

new doctrineDeployTool($argv);

?>