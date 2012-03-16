<?php

class API_Views_ApiDQLViewModel extends IcingaBaseModel {
    private $dqlViews;
    private $view;
    private $viewParameters;

    /**
     *
     * @var NsmUser
     */
    private $user;
    /**
     * @var Doctrine_Connection
     */
    private $connection;
    /**
     *
     * @var IcingaDoctrine_Query
     */
    private $currentQuery = null;
    private $mergeDependencies = array();


    private static $bufferedResults = array();

    public function getResultFromViewBuffer($viewName) {
        
        if(isset(self::$bufferedResults[$viewName])) {
            return self::$bufferedResults[$viewName];
        }
        return null;
    }

    public function initialize(AgaviContext $ctx, array $parameters = array()) {
        parent::initialize($ctx, $parameters);

        $this->dqlViews = include AgaviConfigCache::checkConfig(AgaviToolkit::expandDirectives('%core.module_dir%/Api/config/views.xml'));
        $this->view = $parameters["view"];
        $this->viewParameters = isset($parameters["parameters"]) ? $parameters["parameters"] : array();
        $this->validateTarget();
        $connection = "icinga";

        if($this->view["connection"]) {
            $connection = $this->view["connection"];
        }
        $this->connection = $ctx->getDatabaseConnection($connection);
        $this->user = $this->getContext()->getUser()->getNsmUser();

        
        $this->parseBaseDQL();
        $this->parseDQLExtensions();
        $this->parseDependencies();
        

    }

    public function getResult() {
        AppKitLogger::verbose("Processing query %s ",$this->currentQuery->getSqlQuery());

        $result = $this->currentQuery->execute(null,Doctrine_Core::HYDRATE_SCALAR);
        
        $normalizedResult = array();
        foreach($result as $row) {
            $normalizedRow = array();
            foreach($row as $field=>$value) {
                $field = explode("_",$field,2);
                $normalizedRow[$field[1]] = $value;
            }
            $normalizedResult[] = $normalizedRow;
        }

        self::$bufferedResults[$this->view["name"]] = $normalizedResult;
        if($this->view["base"])
            self::$bufferedResults[$this->view["base"]] = $normalizedResult;

        AppKitLogger::verbose("Result for view %s : %s",$this->view["name"],$normalizedResult);
        $this->applyMerger($normalizedResult);

        return $normalizedResult;
    }

    private function enableFilter($field) {
        if(!isset($this->view["filter"][$field])) {
            return $field;
        }
        $filterDefinition = $this->view["filter"][$field];
        $this->applyDQLCalls($this->currentQuery,$filterDefinition["calls"]);
        
        foreach($filterDefinition["calls"] as $key=>$value) {
            if($value["type"] == "resolve") {
                $field = $value["arg"];
            }
        }
        return $field;
    }

    public function addWhere($field,$operator,$value) {
         $value = $this->connection->quote($value);

         $field = $this->enableFilter($field);
         $field = $this->getAliasedTableFromDQL($field);
         $this->currentQuery->addWhere("$field $operator $value");
         AppKitLogger::verbose("Query after addWhere extension %s ", $this->currentQuery->getSqlQuery());
         
    }

    private function applyMerger(&$result) {
        foreach($this->mergeDependencies as $merger) {
            $view = $merger->getView();
            $result = $merger->merge($result);
        }
    }

    private function validateTarget() {
        AppKitLogger::verbose("Template uses view %s for data retrieval",$this->view);
        if(!isset($this->dqlViews[$this->view])) {
            $target = $this->view;
            AppKitLogger::fatal("Target %s not found in views, check your template or create a view %s in views.xml",$target,$target);
            throw new AgaviException("Target $target not found in views, check your template or create a view $target in views.xml");
        }

        $this->view = $this->dqlViews[$this->view];        
    }

    private function parseBaseDQL() {
        $prefix = $this->connection->getPrefix();
        $query = $this->view["baseQuery"];

        $query = $this->replaceTokens($query);
        $this->createDQL($query);
    }

    private function parseDQLExtensions() {
        $prefix = $this->connection->getPrefix();
        if(!empty($this->view["extend"])) {
            foreach($this->view["extend"] as $extender) {
                $this->applyDQLCalls($this->currentQuery, $extender["calls"]);
            }
        }

    }

    private function createDQL($dql) {
        $query = IcingaDoctrine_Query::create();
        $query->setConnection($this->connection);
        AppKitLogger::verbose("Parsing DQL Query: %s ",$dql);
        $query->parseDqlQuery($dql);

        $this->applyCredentials($query);
        AppKitLogger::verbose("Query : %s", $query->getSqlQuery());
        $this->currentQuery = $query;
        
    }

    private function applyCredentials(IcingaDoctrine_Query &$query) {

        $models = array();
        AppKitLogger::verbose("Parsing credentials: %s",$this->view["credentials"]);
        $filter = $this->getContext()->getModel('Filter.DoctrineUserRestriction', 'Api');
        $filter->setCurrentUser();
        foreach($this->view["credentials"] as $credentialDefinition) {
            switch($credentialDefinition["type"]) {
                case "auto":
                    $models[$credentialDefinition["name"]] = $credentialDefinition["alias"];
                    break;
                case "custom":
                    AppKitLogger::verbose("Applying custom credential %s (%s)",$credentialDefinition["name"],$credentialDefinition["dql"]);
                    $this->applyCustomCredential(
                        $credentialDefinition["dql"],
                        $query,
                        $this->getCredentialValues($credentialDefinition["name"])
                    );
                    break;
                case "dql":
                    $this->applyDQLCalls($query,$credentialDefinition["calls"],
                        $this->getCredentialValues($credentialDefinition["name"]));
                   break;
               default:
                   $extender = $this->getContext()->getModel("Views.Extender.".ucfirst($credentialDefinition["type"])."Extender","Api");
                   $extender->extend($query,$credentialDefinition["params"]);
           }
        }
        $filter->enableModels($models);

        $query->addFilter($filter);   
    }

    public function getAliasedTableFromDQL($field) {
        $results = array();

        if(preg_match_all('/([A-Za-z_\.1-9]+?) AS '.$field.'/i',$this->currentQuery->getDql(),$results)) {
            return $results[1][0];

        } else return $field;
    }

    public function getQuery() {
        return $this->currentQuery;
    }

    private $dqlHistory = array();

    private function applyDQLCalls(IcingaDoctrine_Query $query,array $sequence, $targetValues = null) {

        if($targetValues !== null && empty($targetValues))
            return;
        AppKitLogger::verbose("Applying dql sequence %s",$sequence);

        foreach($sequence as $call) {
            if(in_array($call["arg"].$call["type"],$this->dqlHistory))
                continue;

            if($targetValues !== null)
                $arg = $this->replaceCredentialTokens($call["arg"], $targetValues);
            else
                $arg = $this->replaceTokens($call["arg"]);
            AppKitLogger::verbose("Applying call query->%s(%s)",$call["type"],$arg);
            $this->dqlHistory[] = $call["arg"].$call["type"];

            switch($call["type"]) {
                case 'select':
                    $query->addSelect($arg);
                    break;
                case 'innerjoin':
                case 'join':
                    $query->innerJoin($arg,null);
                    break;
                case 'leftjoin':
                    $query->leftJoin($arg,null);
                    break;
                case 'where':
                case 'andwhere':
                    $query->andWhere($arg);
                    break;
                case 'orwhere':
                    $query->orWhere($arg);
                    break;
                case 'limit':
                    $query->limit($arg);
                    break;
                case 'offset':
                    $query->offset($arg);
                    break;
                case 'groupby':
                    $query->addGroupBy($arg);
                    break;   
            }
        }
    }

    private function applyCustomCredential($dql,IcingaDoctrine_Query $query,array $targetValues) {

        if(empty($targetValues))
            return;
        $dql = $this->replaceCredentialTokens($dql,$targetValues);
        $query->parseDqlQuery($dql);
    }

    private function replaceCredentialTokens($dql,array $targetValues) {
        if(empty($targetValues))
            return;

        $targetArray = array();
        $valueArray = array();
        $keyTargetArray = array();

        foreach($targetValues as $target) {
            
            $valueArray[] =  "'".$target["tv_val"]."'";
            $targetArray[] =  "'".$target["tv_key"]-"'";
            $dql = preg_replace('/\${credential_key_x}/',"'".$target["tv_key"]-"'",$dql,1);
            $dql = preg_replace('/\${credential_val_x}/',"'".$target["tv_val"]-"'",$dql,1);
        }
        $dql = str_replace('${credential_value}', implode(",",$valueArray), $dql);
        $dql = str_replace('${credential_key}', implode(",",$targetArray), $dql);
        return $this->replaceTokens($dql);
    }

    private function getQueryTokens($query) {
        $tokens = array();
        
        preg_match_all('/\${(.+)}/',$query,$tokens);
        AppKitLogger::verbose("Replacing tokens %s ",$tokens);
        return array_unique($tokens[1]);
    }

    private function replaceTokens($query) {
        $tokens = $this->getQueryTokens($query);
        foreach($tokens as $token) {
            switch(strtolower($token)) {
                case 'prefix':
                    $query = str_replace('${prefix}', $this->connection->getPrefix(),$query);
                    AppKitLogger::verbose("Replaced prefix %s",$query);
                    break;
                case 'username':
                    $query = str_replace('${username}', $this->user->user_name,$query);
                    break;
                default:
                    $query = $this->resolveReferenceToken($token,$query);
            }
        }
        return $query;
    }

    private function resolveReferenceToken($token,$query) {
        $results = array();
        preg_match_all('/(?P<view>[^\.\}]+)\.(?P<field>[^\}]+)/',$token,$results);
        if(count($results["view"]) < 1) {
            AppKitLogger::warn("Invalid token %s found in view s%, ignoring",$token,$this->view["name"]);
            return;
        }
        for($i=0;$i<count($results["field"]);$i++) {
            // Check if it's a paremeter token
            if($results["view"][$i] == 'param') {
                if(isset($this->viewParameters[$results["field"][$i]]))
                    $replace = $this->viewParameters[$results["field"][$i]];
                else {
                    AppKitLogger::warn("Missing view parameter %s",$results["field"][$i]);
                    $replace = "''";
                }
                $query = str_replace('${'.$token.'}',$replace,$query);

                continue;
            }
            
            $field = $results["field"][$i];
            $view = $results["view"][$i];
            AppKitLogger::verbose(
                "View %s requires field %s from view %s",
                 $this->view["name"],
                 $field,
                 $view
            );

            $result = $this->getResultFromViewBuffer($view);
            if($result === null) {
                $result = $this->getContext()->getModel("Views.ApiDQLView","Api",array(
                    "view" => $view
                ))->getResult();
            }

            // aggregate values
            $resultVals = array();
            foreach($result as $entry) {
                $resultVals[] = is_int($entry[$field]) ?
                    $entry[$field] : "'".$entry[$field]."'";
            }
            if(empty($resultVals))
                $resultVals[] = "-99";
            $query = str_replace(
                    '${'.$view.'.'.$field.'}',
                    implode(",",$resultVals), $query);
        }
        return $query;
    }

    private function parseDependencies() {
        if(!isset($this->view["merge"])) {
            return;
        }
        foreach($this->view["merge"] as $mergeDependency) {
            $merger = $this->getContext()->getModel("Views.Merger.".ucfirst($mergeDependency["strategy"])."Merger","Api");
            $type = "left";
            if(isset($mergeDependency["type"]))
                $type = $mergeDependency["type"];
            AppKitLogger::verbose("Merge dependency detected: %s -> %s ",$this->view["name"],$mergeDependency["source"]);
            $merger->setup($mergeDependency["source"],$mergeDependency["field"],$type);
            $this->mergeDependencies[] = $merger;
        }
    }

    private function getCredentialValues($target) {
        if(!$this->user->hasTarget($target))
            return array();
        if($target != IcingaIPrincipalConstants::TYPE_CONTACTGROUP) {
            return $this->user->getTargetValues($target)->toArray();
        }

        $targetValue = new NsmTargetValue();
        $targetValue->tv_key = 'contactname';
        $targetValue->tv_val = $this->user->user_name;
        return array($targetValue);
    }
}