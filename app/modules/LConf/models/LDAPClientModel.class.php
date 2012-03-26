<?php
/**
 * Client Model class for ldap
 * Builds a connection, handles filtering and provides an interface
 * for communication with the ldap Server.
 * Automatically saves itself to the store on destruction.
 *
 *
 * @author jmosshammer
 *
 */

class LConf_LDAPClientModel extends IcingaLConfBaseModel {
    /**
     * ID of the connection, needed for storing
     * @var string
     */
    private $id = 0;
    /**
     * Filtergroup class for search queries
     * @var ldapFilterGroupModel
     */
    private $filter = false;
    /**
     * Schema Validator - not implemented yet
     * @var unknown_type
     */
    private $schemaValidator = false;
    /**
     * ConnectionModel class which holds information about the connection
     * like Host, BaseDN, credentials, etc.
     * @var ldapConnectionModel
     */
    private $connectionModel = false;

    /**
     * Instance of LDAPHelperModel for misc. result formatting operations
     * @var LConf_LDAPHelperModel
     */
    private $helper = null;
    /**
     * The connection resource over which communication is handled
     * @var resource
     */
    private $connection		= false;
    /**
     * ldap_options, see @link http://de2.php.net/manual/en/ref.ldap.php
     * @var array
     */
    private $ldap_options = array (
            LDAP_OPT_REFERRALS			=> 0,
            LDAP_OPT_DEREF				=> LDAP_DEREF_NEVER,
            LDAP_OPT_PROTOCOL_VERSION	=> 3
    );

    /**
     * Attributes that describe the dn according to RFC4514/RFC4519
     *
     * RFC 4514, Section 3
     * http://www.ietf.org/rfc/rfc4514.txt?number=2253
     */
    public static $dnDescriptors = array('cn','l','st','o','ou','c','street','dc','uid');

    private static $clientInstances = array();
    /**
     * The current working dir
     * @var string
     */
    private $cwd = false;
    /**
     * BaseDN
     * TODO: Check if needed
     * @var string
     */
    private $baseDN = false;
    /**
     * Flag that indicates whether the class should store itself when destructed
     * @var boolean
     */
    private $dontStoreFlag = false;

    public function setId($id) {
        $this->id = $id;
    }
    public function setFilter(lConf_LDAPFilterGroupModel  $filter) {
        $this->filter = $filter;
    }
    public function setSchemaValidator(LConf_LDAPConnectionModel $schemaValidator) {
        $this->validator = $validator;
    }
    public function setConnectionModel(LConf_LDAPConnectionModel $connection) {
        $this->connectionModel = $connection;
    }
    public function setConnection($connection) {
        $this->connection = $connection;
    }
    public function setCwd($cwd) {
        return $this->cwd = $cwd;
    }
    public function setBaseDN($dn) {
        $this->baseDN = $dn;
    }

    public function getId() {
        return $this->id;
    }
    public function getFilter() {
        return $this->filter;
    }
    public function getSchemaValidator() {
        return $this->validator;
    }
    public function getConnectionModel() {
        return $this->connectionModel;
    }
    public function getConnection() {
        return $this->connection;
    }
    public function getCwd() {
        return $this->cwd;
    }
    public function getBaseDN() {
        return $this->baseDN;
    }

    public function __construct(LConf_LDAPConnectionModel $connection = null) {

        if($connection)
            $this->setConnectionModel($connection);
    }
    /**
     * Destroys the class and stores it if the dontStoreFlag is not set
     * @return void
     */
    public function __destruct() {
        if(!$this->dontStoreFlag)
            $this->toStore();

        if(is_resource($this->getConnection()))
            ldap_close($this->getConnection());
    }

    /**
     * Connects (or reconnects if from store) to the ldap server
     * @return void
     */
    public function connect() {
        if(!extension_loaded("ldap"))
            throw new AppKitException("Please install the php-ldap extension and restart your webserver");

        $connConf = $this->getConnectionModel();
        $this->helper = AgaviContext::getInstance()->getModel("LDAPHelper","LConf");
        $ldaps = $connConf->isLDAPS() ? 'ldaps://' : '';

        $connection = ldap_connect($ldaps.$connConf->getHost(),$connConf->getPort());
        if(!is_resource($connection))
            throw new AgaviException("Could not connect to ".$connConf->getConnectionName());

        $this->setConnection($connection);
        $this->applyldapOptions();
        if($connConf->usesTLS()) { //enable TLS if marked
            if(!@ldap_start_tls($connection))
                throw new Exception("Connection via TLS could not be established!");
        }
        $this->doDefaultBind();

        // if the class is unserialized we don't want to set the cwd
        if(!$this->getCwd())
            $this->setDefaultCwd();

        if(!$this->getId())
            $this->generateId();
    }

    /**
     * Generates an unique id for the client
     * @return string
     */
    private function generateId() {
        $this->setId(AgaviToolkit::uniqid("ldap_conn_"));
    }

    private function applyldapOptions() {
        foreach ($this->ldap_options as $opt_id=>$opt_val) {
            ldap_set_option($this->getConnection(), $opt_id, $opt_val);
        }
    }

    private function doDefaultBind() {
        $connConf = $this->getConnectionModel();

        $this->bindTo($connConf->getBindDN(),$connConf->getBindPass());
    }

    public function bindTo($dn,$pass) {
        $connection = $this->getConnection();
        if(!is_resource($connection))
            throw new AgaviException("Connection is not a resource");

        if(@ldap_bind($connection,$dn,$pass) == false) {
            throw new AgaviException("Bind to ".$dn." failed: ".$this->getError());
        }
    }


    /**
     * Sets the default CWD from the connectionModel
     * @return void
     */
    public function setDefaultCwd() {
        $connConf = $this->getConnectionModel();
        $dn = $connConf->getBaseDN();
        if(!$dn) // no BaseDN given, guess the Base dir
            $dn = $this->helper->suggestBaseDNFromName($connConf->getBindDN());
        $this->setBaseDN($dn);
        $this->setCwd($dn);
    }

    /**
     * Adds a new Property $newParams to $dn.
     * $newParams must be an associative array with the fields
     * 'property' and 'value'
     *
     * returns an array with the new properties on success, else throws an
     * Instance of AgaviException with the Errormessage.
     *
     * @TODO: Yep. ldap_mod_add would make life easier. Reading the complete api, too.
     * @param string $dn
     * @param array $newParams
     * @return array $properties
     */
    public function addNodeProperty($dn,$newParams) {
        // if we only have a single entry, encapsulate it in an array
        // so we don't need to differ between them and multiple entries
        if(@$newParams["property"])
            $newParams = array($newParams);

        $connId = $this->getConnection();
        $properties = $this->getNodeProperties($dn);
        $this->helper->cleanResult($properties);

        foreach($newParams as $parameter) {
            $newProperty = strtolower($parameter["property"]);
            $newValue = $parameter["value"];

            if(!isset($properties[$newProperty])) { // property doesn't exist
                $properties[$newProperty] = array();
            } else if(!is_array($properties[$newProperty])) {
                // property already exists
                $swap = $properties[$newProperty];
                $properties[$newProperty] = array($swap);
            }
            $properties[$newProperty][] = $newValue;
        }

        if(!@ldap_modify($connId,$dn,$properties)) {
            throw new AgaviException("Could not modify ".$dn. ":".$this->getError());
        }
        return $properties;
    }

    public function addNode($parentDN,$parameters) {
        if(!$parameters)
            throw new AgaviException("No parameters given!");

        $dn = $parentDN;
        //always wrap to array
        if(isset($parameters["property"]))
            $parameters = array($parameters);
        $params = array();
        foreach($parameters as $parameter) {
            if(!isset($params[$parameter["property"]]) )
                $params[$parameter["property"]] = $parameter["value"];
            else {
                $params[$parameter["property"]] = array($params[$parameter["property"]]);
                $params[$parameter["property"]][] = $parameter["value"];
            }
            if(in_array(strtolower($parameter["property"]),self::$dnDescriptors))
                $dn = $parameter["property"]."=".$this->helper->escapeString($parameter["value"]).",".$dn;
        }
        $connId = $this->getConnection();

        if(!@ldap_add($connId,$dn,$params)) {
            throw new AgaviException("Could not add ".$dn. ":".$this->getError());
        }
        return $params;
    }

    public function removeNodes($dnList,$killAliases = true) {
        $dns = $dnList;
        $connId = $this->getConnection();
        if(!is_array($dns))
            $dns = array($dns);
        $errors = "";
        foreach($dns as $dn) {
            if(!$dn)
                continue;
            if(!$this->recursiveRemoveNode($dn,$killAliases)) {
                $errors .= "<br/>".$dn.": ".$this->getError();
            }
        }

        if($errors != "")
            throw new AgaviException("Errors occured: ".$errors);
    }

    public function recursiveRemoveNode($dn,$killAliases = true) {
        $list = $this->listDN($dn,false,true);
        $this->helper->cleanResult($list);
        if($list) {
            $result = true;
            foreach($list as $subEntries) {
                $result = $result && $this->recursiveRemoveNode($subEntries["dn"]);
            }
        }
        if($killAliases) {
            if($aliases = $this->getReferencesToNode($dn)) {
                foreach($aliases as $key=>$alias) {
                    if(!is_array($alias))
                        continue;
                    $this->removeNodes(array($alias["dn"]));
                }
            }
        }

        return @ldap_delete($this->getConnection(),$dn);
    }

    public function searchEntries($filter,$base = null,array $addAttributes = array()) {
        $filterString = $filter->buildFilterString();

        if(!$base)
            $base = $this->getBaseDN();
        $searchAttrs = array_merge(array("dn"),$addAttributes);

        $result = @ldap_search($this->getConnection(),$base,$filterString,$searchAttrs);
        return $result ? ldap_get_entries($this->getConnection(),$result) : null;
    }

    public function getReferencesToNode($dn) {
        $ctx = $this->getContext();
        $dnToCheck = $dn;

        $filterGroup = $ctx->getModel("LDAPFilterGroup","LConf");
        $objectClassFilter =  $ctx->getModel("LDAPFilter","LConf",array("objectclass","alias",false,"exact"));
        $aliasTargetFilter = $ctx->getModel("LDAPFilter","LConf",array("aliasedobjectname",$dnToCheck,false,"exact"));
        $filterGroup->addFilter($objectClassFilter);
        $filterGroup->addFilter($aliasTargetFilter);
        $result = $this->searchEntries($filterGroup,$this->getBaseDN());

        if(isset($result["count"]))
            return $result;
        return false;
    }

    /**
     * returns the ldap_entries for cwd
     * @return array
     */
    public function listCurrentDir() {
        $connConf = $this->getConnectionModel();
        $markAsAlias = false;
        $basedn = $this->getCwd();
        if(preg_match('/ALIAS=Alias of:/',$this->getCwd())) {
            $basedn = str_replace("ALIAS=Alias of:","",$this->getCwd());
            /**
             * This is necessary to avoid id problems in the web interface.
             * Aliased elements start with a "*", an 4.digit id and again, a "*"
             *
             */
            $result = $this->listDN($basedn);
            foreach($result as $key=>&$vals) {
                if(!is_int($key))
                    continue;
                $vals["dn"] = "*".rand(1000,9999)."*".$vals["dn"];
            }
            return $result;
        } else return $this->listDN($basedn);
    }

    public function listDN($dn,$resolveAlias = true,$ignoreFilter = false) {
        $filter = "objectclass=*";


        $result = @ldap_list($this->getConnection(),$dn,$filter,array("dn","objectclass","aliasedobjectname","modifyTimestamp","description"));

        $entries = @ldap_get_entries($this->getConnection(),$result);
        if($resolveAlias)
            $entries = $this->helper->resolveAliases($entries);
        if($this->getFilter() && !$ignoreFilter) {

            $searchResult = $this->searchEntries($this->getFilter(),null,array("dn","objectclass","aliasedobjectname","modifyTimestamp","description"));
            $entries = $this->helper->filterTree($entries,$searchResult);
        }

        return $entries;
    }

    /**
     * Returns the properties of a node $dn
     *
     * @param string $dn
     * @return array
     */
    public function getNodeProperties($dn,$fields=array(),$checkInheritance = false) {
        $connection = $this->getConnection();
        $result = @ldap_read($connection,$dn,"objectclass=*",$fields);
        if(!$result)
            return array();
        $entries = ldap_get_entries($connection,$result);
        if($checkInheritance) {
            $inherited = AgaviConfig::get('modules.lconf.inheritance');
            if($inherited) {
                $inh_keys =  array_keys($inherited);
                foreach($entries[0]["objectclass"] as $key=>$val) {
                    if(in_array($val,$inh_keys)) {
                        $this->addInheritedProperties($entries[0],$dn,$inherited[$val]);
                    }
                }

            }
        }
        if(isset($entries[0]))
            return $entries[0];
        else
            return null;
    }

    protected function addInheritedProperties(array &$entries, $dn, $inheritance) {
        $baseDn = $this->getBaseDN();
        $strippedDn = substr($dn,0,-1*(strlen($baseDn)+1));
        $dnParts = explode(",",$strippedDn);
        $connection = $this->getConnection();
        /**
         * Hangle down the dn structure
         */

        for($i=count($dnParts)-1;$i>=1;$i--) {
            $dn = $dnParts[$i];
            $baseDn = $dn.",".$baseDn;
            // check if an inherited object is above this object
            $result = ldap_get_entries($connection,@ldap_read($connection,$baseDn,"objectclass=*",array()));
            foreach($result[0]["objectclass"] as $key=>$obj) {

                // Check if node is inherited
                $isInherited = false;
                foreach($inheritance as $inhKey=>$value) {
                    if(preg_match("/".$inhKey."/",$obj)) {
                        $isInherited = true;
                        $obj = $inhKey;
                        break;
                    }
                }
                if(!$isInherited)
                    continue;
                
                
                foreach($inheritance[$obj]["attributes"] as $inhAttributes=>$value) {
                    $hasOverwrite = false;
                    if(isset($value["overwrite"]) && $value["overwrite"] == true)
                        $hasOverwrite = true;

                    foreach($result[0] as $attribute=>$value) {

                        if(!preg_match("/".$inhAttributes."/",$attribute))
                            continue;

                        unset($value["count"]);
                        foreach($value as &$attr) {
                            $attr = array("inherited"=>true,"value"=>$attr,"dn"=>$baseDn);
                        }
                        // Copy attributes
                        if(!isset($entries[$attribute])) {
                            $entries[$attribute] = $value;
                        } else {
                            if(!$hasOverwrite)
                                array_push($entries[$attribute],$value);
                        }
                    }
                }
            }
        }
    }

    /**
     * Modifies a node $dn so that it's parameters will match $newParams
     * $newParams must be an associative array with the fields
     * 'id'	 		an id with the format %KEYNAME%_%ENTRYNR%
     * 'property' 	the new property name
     * 'value' 		the new value name
     *
     * @TODO: Yep. like in the add function ldap_mod_replace would make life easier.
     * @TODO: Split it up!
     * @param string $dn
     * @param string $newParams
     * @return array
     */
    public function modifyNode($dn, $newParams) {
        if(isset($newParams["id"]))
            $newParams = array($newParams);

        $connId = $this->getConnection();
        $properties = $this->getNodeProperties($dn);
        $properties = $this->helper->formatToPropertyArray($properties);

        $idRegexp = "/^(.*)_(\d*)$/";
        $affectsDN = false;

        foreach($newParams as $parameter) {

            // ignore inherited params
            if(isset($parameter["parent"]))
                if($parameter["parent"] != "")
                    continue;

            $idElems = array();
            preg_match($idRegexp,$parameter["id"],$idElems);
            if(count($idElems) != 3) {
                throw new AppKitException("Invalid ID given to modifyNode ".$parameter["id"]);
            }
            $curProperty = $idElems[1];
            $curIndex = $idElems[2];
            if(!isset($properties[$curProperty]))
                continue;
            if(is_array($properties[$curProperty])) {

                $properties[$curProperty][$curIndex] = $parameter["value"];
                if(in_array($curProperty,self::$dnDescriptors))
                    $affectsDN = $curProperty."=".$parameter["value"];
            } else {
                $properties[$curProperty] = $parameter["value"];
                if(in_array($curProperty,self::$dnDescriptors))
                    $affectsDN = $curProperty."=".$parameter["value"];
            }
        }
        // if the dn is affected, the node must be moved instead of being modified
        if($affectsDN) {
            $dnToPreserve = explode(",",$dn,2);
            $dnToPreserve = $dnToPreserve[1];
            // add new node and remove old
            $newDN = $affectsDN.",".$dnToPreserve;


            $this->rechainAliasesForNode($dn,$newDN);
            if(!@ldap_add($connId,$newDN,$properties)) {
                throw new AgaviException("Could not modify ".$dn. ":".$this->getError());
            }
            // recursive clone
            if($childs = $this->listDN($dn)) {
                foreach($childs as $key=>$child) {
                    if(!is_int($key))
                        continue;

                    $this->cloneNode((isset($child["aliasdn"]) ? $child["aliasdn"] : $child["dn"]),$newDN);
                }
            }
            $this->removeNodes($dn);
        } else {

            if(!@ldap_modify($connId,$dn,$properties)) {
                throw new AgaviException("Could not modify ".$dn. ":".$this->getError());
            }
        }
        return $dn;
    }

    public function rechainAliasesForNode($dn,$newDN) {
        // Rechain aliases
        if($aliases = $this->getReferencesToNode($dn)) {
            foreach($aliases as $key=>$alias) {
                if(!is_array($alias))
                    continue;
                $splittedAlias = explode(",",$alias["dn"],2);
                /**
                 *  for some reason, he doesn't like modifying aliasedobjectname via modifyNode...
                 *  That's why it's done the more comprehensive way
                 */
                $this->addNode($splittedAlias[1],array(
                        array("property"=>"objectclass","value"=>"extensibleObject"),
                        array("property"=>"objectclass","value"=>"alias"),
                        array("property"=>"aliasedObjectName","value"=>$newDN)
                ));
                /**
                 *  It doesn't matter if the new alias creation has completed or not, as the old alias
                 *  is useless eitherway. That's why there's no check
                 */
                $this->removeNodes(array($alias["dn"]));
            }
        }

    }

    /**
     * @TODO: Yep. like in the add function ldap_mod_replace would make life easier.
     * @param unknown_type $dn
     * @param unknown_type $remParams
     */
    public function removeNodeProperty($dn,$remParams) {
        if(!is_array($remParams))
            $remParams = array($remParams);

        $connId = $this->getConnection();
        $properties = $this->getNodeProperties($dn);

        $this->helper->cleanResult($properties);
        $idRegexp = "/^(.*)_(\d*)$/";
        foreach($remParams as $parameter) {
            $idElems = array();
            preg_match($idRegexp,$parameter,$idElems);
            if(count($idElems) != 3) {
                throw new AppKitException("Invalid ID given to removeProperty ".$parameter);
            }
            $curProperty = $idElems[1];
            $curIndex = $idElems[2];
            if(is_array($properties[$curProperty])) {
                unset($properties[$curProperty][$curIndex]);
                if(count($properties[$curProperty]) == 0)
                    $properties[$curProperty] = array();
            } else
                $properties[$curProperty] = array();
        }
        foreach($properties as &$arr) {
            if(is_array($arr))
                $arr = 	 array_values($arr);
        }
        if(!@ldap_modify($connId,$dn,$properties)) {
            throw new AgaviException("Could not modify ".$dn. ":".$this->getError());
        }
        return null;
    }

    public function expandAlias($nodeDN) {
        $connId = $this->getConnection();

        $sourceProperties = $this->getNodeProperties($nodeDN);
        if(!is_array($sourceProperties))
            throw new AppKitException("Could not find alias $nodeDN");

        $targetDN = $sourceProperties["aliasedobjectname"][0];
        $paramToPreserve = explode(",",$nodeDN,2);
        $name = explode("=",$paramToPreserve[0]);
        $this->cloneNode($targetDN,$paramToPreserve[1]);
    }

    public function cloneNode($sourceDN, $targetDN,$sourceConnId = null,$newName = null) {
        $connId = $this->getConnection();
        $sourceProperties = $this->getNodeProperties($sourceDN);
        $targetProperties = $this->getNodeProperties($targetDN,array("dn"));
        $paramToPreserve = explode(",",$sourceProperties["dn"],2);
        $this->helper->cleanResult($sourceProperties);
        $newDN = $newName.",".$targetDN;
        if(!$newName) {
            $paramToPreserve = $paramToPreserve[0];
            $newDN = $paramToPreserve.",".$targetDN;
        }
        // check if it's on the same level
        if($this->listDN($newDN)) {
            $ctr = 0;
            do { // Increase copy counter if there is already a copy of this node
                $paramToChange = explode("=",$paramToPreserve,2);
                $newValue = ("copy_of".(($ctr) ? "(".$ctr.")" : '')."_").$paramToChange[1];
                $finalParamToPreserve = $paramToChange[0]."=".$newValue;
                $newDN = $finalParamToPreserve.",".$targetDN;

                $sourceProperties[$paramToChange[0]][0] = $newValue;
                $ctr++;
            } while($this->listDN($newDN));
        }

        $connId = $this->getConnection();
        /**
         * Check if we're performing a copy/paste to another connection
         */
        if(!$sourceConnId || $sourceConnId == $this->getId()) {
            if(!@ldap_add($connId,$newDN,$sourceProperties)) {
                throw new AgaviException("Could not add ".$newDN. ":".$this->getError());
            }
        } else {

            $client = LConf_LDAPClientModel::__fromStore($sourceConnId,$this->getContext()->getStorage());
            if(!$client)
                throw new AgaviException("Target connection not found!");
            $id = $client->getConnection();
            $existCheck = $this->getNodeProperties($newDN);
            if(!empty($existCheck))
                throw new AgaviException("<br/>DN alredy exists");
            if(!@ldap_add($id,$newDN,$sourceProperties)) {
                throw new AgaviException("Could not add ".$newDN. ":".$this->getError());
            }
        }
        // recursive clone
        if($childs = $this->listDN($sourceDN)) {
            foreach($childs as $key=>$child) {
                if(!is_int($key))
                    continue;

                $this->cloneNode((isset($child["aliasdn"]) ? $child["aliasdn"] : $child["dn"]),$newDN);
            }
        }
    }

    public function moveNode($sourceDN, $targetDN,$sourceConnId = null) {
        $this->cloneNode($sourceDN,$targetDN,$sourceConnId);
        if(!$sourceConnId || $sourceConnId == $this->getId()) {
            $this->rechainAliasesForNode($sourceDN,$targetDN);
        }
        $this->removeNodes(array($sourceDN));
    }

    public function searchSnippetOccurences($snippet,$unique = false,$isRegExp = false) {
        $searcher = $this->getContext()->getModel("LDAPSimpleSearch","LConf",array("client"=>$this,"unique"=>$unique));
        return $searcher->search($snippet);
    }

    public function searchReplace($from,$to,array $fields,$sissyMode = false) {
        $filter = $this->getFilter();
        $entries_to_check = $this->searchEntries($filter,$this->getBaseDN(),$fields);
        $mods = array();

        foreach($entries_to_check as $entry) {
            foreach($fields as $fieldToCheck) {
                if(!isset($entry[$fieldToCheck]))
                    continue;
                $field = $entry[$fieldToCheck];
                if(!is_array($field))
                    $field = array($field);

                foreach($field as $key=>$value) {
                    if(!is_int($key))
                        continue;
                    $matches = array();
                    if(!preg_match("/".$from."/",$value))
                        continue;
                    $mods[] = array(
                            "dn"=>$entry["dn"],
                            "nr" => $key,
                            "field" => $fieldToCheck,
                            "original" => $value,
                            "new" => preg_replace("/".$from."/",$to,$value)
                    );
                }
            }
        }
        if($sissyMode)
            return $this->buildChangesBox($mods);
        // Changed DNs must be stored
        $changedDNs = array();
        $errors = array();
        foreach($mods as $mod) {
            $dn = $mod["dn"];
            while(isset($changedDNs[$dn])) {
                $dn = $changedDNs[$dn];
            }
            try {
                $resultDN = $this->modifyNode($dn,array(
                        "id" => $mod["field"]."_".$mod["nr"],
                        "property" => $mod["field"],
                        "value" => $mod["new"]
                ));

                if($resultDN != $dn)
                    $changedDNs[$dn] = $resultDN;
            } catch(Exception $e) {
                $errors[] = $mod["dn"]."-".$mod["field"]." Can't replace ".
                        $mod["original"]." with ".$mod["new"]." : ".ldap_error($this->getConnection());
            }
        }
        if(!empty($errors))
            return json_encode($errors);
        else
            return "success";
    }

    protected function buildChangesBox(array $mods) {
        $string = "<div class='lconf_infobox'>";
        if(empty($mods)) {
            $string .= "No changes would be made.";
        } else {
            $string .=" <ul>";
            foreach($mods as $mod) {
                $string .= "<li>";
                $string .= "In DN=".$mod["dn"]." : Rename field ".$mod["field"]." from ".$mod["original"]." to ".$mod["new"];
                $string .= "</li>";
            }
            $string .=" </ul>";
        }

        $string .= "</div>";
        return $string;
    }

    public function toStore() {
        $clSerialized = serialize($this);
        // 0 char  as used in serialisation caueses postgre to truncate the data
        $clSerialized = base64_encode($clSerialized);
        $storage = $this->getContext()->getStorage();
        $storage->write("Icinga.ldap.client.".$this->getId(),$clSerialized);

    }

    static public function __fromStore($id,AgaviStorage $storage) {
        if(isset(self::$clientInstances[$id]))
            return self::$clientInstances[$id];
        $clSerialized = $storage->read("Icinga.ldap.client.".$id);
        // recraete original representation of 0x00 char
        $clSerialized = base64_decode($clSerialized);
        $cl = unserialize($clSerialized);
        self::$clientInstances[$id] = $cl;
        return $cl;
    }

    public function disableStoring() {
        $this->dontStoreFlag = true;
    }
    public function enableStoring() {
        $this->dontStoreFlag = true;
    }

    public function __sleep() {
        $this->_connectionModel = false;
        if($this->getConnectionModel())
            $this->_connectionModel = $this->connectionModel->__toArray(true);

        /* AgaviModel __sleep()*/
        $this->_contextName = $this->context->getName();

        return array('id','baseDN','_connectionModel','_contextName','connection','ldap_options','cwd');

    }

    public function __wakeup() {
        $this->context = AgaviContext::getInstance($this->_contextName);
        unset($this->_contextName);
        // rebuild filters

        // rebuild connection-class

        if($this->_connectionModel) {
            $this->connectionModel = $this->getContext()
                    ->getModel("LDAPConnection","LConf",array($this->_connectionModel));
            $this->_connectionModel = null;
        }
        // and finally (and hopefuly)- reconnect!
        $this->connect();
        $this->applyldapOptions();
    }


    public function getError() {
        if(is_resource($this->getConnection()))
            return "<br/>LDAP Error:<br/><pre class='lconf_infobox'><code>".ldap_error($this->getConnection())."</code></div>";
    }


}


?>
