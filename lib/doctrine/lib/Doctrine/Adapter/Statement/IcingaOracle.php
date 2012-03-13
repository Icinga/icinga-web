<?php
/*
 *  $Id$
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information, see
 * <http://www.doctrine-project.org>.
 */

/**
 * Oracle connection adapter statement class. With custom SQL reparsing to 
 * make sure icinga can be accessed properly
 * @package     Doctrine
 * @subpackage  Adapter
 * @author      Jannis Moßhammer <jannis.mosshammer@netways.de>
 * @author      Marius Hein <marius.hein@netways.de>
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 * @author      vadik56
 * @author      Miloslav Kmet <adrive-nospam@hip-hop.sk> 
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link        www.doctrine-project.org
 * @since       1.0
 * @version     $Rev$
 */
class Doctrine_Adapter_Statement_IcingaOracle implements Doctrine_Adapter_Statement_Interface
{

    /**
     * @var string $queryString         actual query string
     */
    public $queryString;
    
    /**
     * @var resource $connection        OCI connection handler
     */
    protected $connection;
    
    /**
     * @var resource $statement         OCI prepared statement
     */
    protected $statement;
    
    /**
     * @var integer $executeMode        OCI statement execution mode
     */
    protected $executeMode = OCI_COMMIT_ON_SUCCESS;

    /**
     * @var array $bindParams          Array of parameters bounded to a statement
     */
    protected $bindParams = array();

    /**
     * @var array $attributes           Array of attributes
     */
    protected $attributes = array();

    /**
     * @var array $ociErrors            Array of errors
     */
    protected $ociErrors = array();
    
    private $aliasMap = array();
    
    /**
     * the constructor
     * 
     * @param Doctrine_Adapter_Oracle $connection
     * @param string $query  Query string to be executed
     * @param integer $executeMode  OCI execute mode
     */
    public function __construct( Doctrine_Adapter_IcingaOracle $connection, $query, $executeMode)
    {
     
        $this->connection  = $connection->getConnection();
        $this->queryString  =$this->fixCrappyIcingaTables($query);
      
        $this->executeMode = $executeMode;
        $this->attributes[Doctrine_Core::ATTR_ERRMODE] = $connection->getAttribute(Doctrine_Core::ATTR_ERRMODE);

    
        $this->parseQuery();
    }
    
    
    
    private function fixCrappyIcingaTables($query) {

        if (!preg_match('/^\s*(SELECT|INSERT|UPDATE|DELETE)/i', $query)) {
            return $query;
        }
        
        
        $this->resolveIdFields($query); 
        $this->createAliasMap($query);
        $this->removeInvalidAliases($query);
        $this->fixOrderFields($query);
        $this->fixOrdersInSubqueries($query);
        $this->addFieldConversion($query);
        
        if(substr_count($query,"(") != substr_count($query,")"))  
            $query .= ")";
    
        return $query; 
    }

    private function removeInvalidAliases(&$query) {
        $query = preg_replace("/(ORDER BY) *([A-Za-z._0-9]+) +AS +[_A-Za-z0-9]+/i","$1 $2",$query);  
       // $query = preg_replace("/(FROM *\( *SELECT.*? *)ORDER BY .*?(\) *\w+ *WHERE.*)/","$1 $2",$query);
       
    }
    
    /**
     * Add TO_CHAR conversions for CLOB fields for oracle
     * @param string $query
     * @return string Parsed query
     */
    private function addFieldConversion(&$query) {
        $query = preg_replace("/notification_timeperiod_object_id/", "notif_timeperiod_object_id",$query);
        $query = preg_replace("/([^ ]*long_output)/i", "TO_CHAR($1)",$query);
        $query = preg_replace("/([^ ]*perfdata)/i", "TO_CHAR($1)",$query);
        $query = preg_replace("/([^ ]*logentry_data)/i", "TO_CHAR($1)",$query);
        return $query;
    }
    
    /**
     * Adding order parts from main query to subquery which delivers
     * the recordset
     * https://dev.icinga.org/issues/2215
     * @param string $query
     * @return string
     */
    private function fixOrdersInSubqueries(&$query) {
        list($mainTable, $mainAlias) = $this->getTableParts($query);
        
        $m = array();
        if (preg_match('/ORDER\s+BY\s+(.+)(;|$)/i', $query, $m)) {
            $fields = AppKitArrayUtil::trimSplit($m[1]);
            
            $pparse = new AppKitStringParanthesesParser($query, AppKitStringParanthesesParser::STRIP_PARANTHESES);
            if ($pparse->count()) {
                foreach ($pparse as $part) {
                    $m = array();
                    if (preg_match('/^\s*(SELECT\s+[\w\.\,\s]+\s+FROM\s+'. preg_quote($mainTable, '/'). '[^$]+)/', $part, $m)) {
                        $subQuery = $m[0];
                        list($subTable, $subAlias) = $this->getTableParts($subQuery);
                        if ($subTable == $mainTable && !preg_match('/ORDER BY/i', $subQuery)) {
                            $orderParts = array();
                            $newSub = $subQuery;
                            foreach ($fields as $field) {
                                $newOrder = preg_replace('/^\w+\./', $subAlias. '.', $field);
                                $newField = preg_replace('/\s+.+$/', '', $newOrder);
                                $orderParts[] = $newOrder;
                                $newSub = preg_replace('/^\s*(SELECT\s+(DISTINCT)?\s*)'. $subAlias. '/i', '\1 '. $newField. ', '. $subAlias, $subQuery);
                            }
                            $newSub = $newSub. ' ORDER BY '. implode(', ', $orderParts);
                            $query = preg_replace('/'. preg_quote($subQuery, '/'). '/', $newSub, $query);
                        }
                        break;
                    }
                }
            }
        }
        return $query;
    }
    
    /**
     * Extracts table name and corresponding alias from query (part)
     * @param string $queryPart
     * @return string[TABLE_NAME, ALIAS]
     */
    private function getTableParts($queryPart) {
        if (preg_match('/from (\w+)\s(\w+)\s/i', $queryPart, $m)) {
            return array($m[1], $m[2]);
        }
        
        return array(null, null);
    }
    
    /**
     * Search for all ORDER tags and check if they selected. If not
     * remove the error-prone statement part
     * @param string $query
     * @return integer Number of removed order parts
     */
    private function fixOrderFields(&$query) {
        $matches = array ();
        $re = 0;
        
        preg_match_all('/ORDER\s+BY\s+([\w\.]+)\s*(ASC|DESC)?/i', $query, $matches, PREG_SET_ORDER);
        
        foreach ($matches as $m) {
            $re = sprintf('/SELECT\s+.*(%s).*FROM/i', preg_quote($m[1]));
            if (!preg_match($re, $query)) {
                $query = str_replace($m[0], '', $query);
                $re++;
            }
        }
        
        return $re;
    }
    
    private function resolveIdFields(&$query) {
        
        $tableRefMapRegExpNoAlias = "/(FROM|JOIN) *(?<table>\w+) *(WHERE|INNER|JOIN|ON|ORDER|LIMIT|GROUP)/";   
        $tableRefMapRegExp = "/(FROM|JOIN) *(?<table>\w+) *(AS)? *(?<alias>\w+) */"; 
        $aliasedSelectExp = "/FROM *\( *SELECT.*?FROM *(?<table>\w+) .*\) *(?<alias>\w+) *WHERE/";

        $matches = array();
        $noAliasMatches = array();
        $aliasedSelectMatches = array();

        preg_match_all($tableRefMapRegExpNoAlias,$query,$noAliasMatches);
        preg_match_all($tableRefMapRegExp,$query,$matches);
        preg_match_all($aliasedSelectExp,$query,$aliasedSelectMatches);
        foreach($noAliasMatches["table"] as $entry) { 
            $matches["table"][] = $entry;
            $matches["alias"][] = $entry;
        }
        $matches["table"] = array_merge($matches["table"],$aliasedSelectMatches["table"]);
        $matches["alias"] = array_merge($matches["alias"],$aliasedSelectMatches["alias"]);
        
        for($i=0;$i<count($matches["table"]);$i++) {
            $table = $matches["table"][$i];
            $alias = $matches["alias"][$i];  
                
            $editedTable = $table;
            if(preg_match("/[^uai]s$/",$table))
                $editedTable = substr($table,0,-1);
            $editedTable = preg_replace("/ie$/","y",$editedTable);
           
            $id = $alias.".".$editedTable."_id";
           
            $query = preg_replace("/".$id."/",$alias.".id",$query);      
        }
        
    } 

    private function createAliasMap(&$query) {
        $ctr = 0; 
        $matches = array();
        $reg = "/\s+AS\s+(?<alias>\w+)(\s+|,)/";
        $this->aliasMap = array();
       
        preg_match_all($reg,$query,$matches);
        foreach($matches["alias"] as $alias) {
            if(preg_match("/DOCTRINE.*?/i",$alias))
                continue;
            $query = preg_replace("/(AS +)".$alias."/","AS f_".$ctr,$query,1);
            $this->aliasMap[("f_".($ctr++))] = $alias;
        }
        
        // AgaviContext::getInstance()->getLoggerManager()->log($query, AgaviLogger::DEBUG);
      
    }  
    /**
     * Bind a column to a PHP variable
     *
     * @param mixed $column         Number of the column (1-indexed) or name of the column in the result set.
     *                              If using the column name, be aware that the name should match
     *                              the case of the column, as returned by the driver.
     * @param string $param         Name of the PHP variable to which the column will be bound.
     * @param integer $type         Data type of the parameter, specified by the Doctrine_Core::PARAM_* constants.
     * @return boolean              Returns TRUE on success or FALSE on failure
     */
    public function bindColumn($column, $param, $type = null)
    {
        throw new Doctrine_Adapter_Exception("Unsupported");
    }

    /**
     * Binds a value to a corresponding named or question mark 
     * placeholder in the SQL statement that was use to prepare the statement.
     *
     * @param mixed $param          Parameter identifier. For a prepared statement using named placeholders,
     *                              this will be a parameter name of the form :name. For a prepared statement
     *                              using question mark placeholders, this will be the 1-indexed position of the parameter
     *
     * @param mixed $value          The value to bind to the parameter.
     * @param integer $type         Explicit data type for the parameter using the Doctrine_Core::PARAM_* constants.
     *
     * @return boolean              Returns TRUE on success or FALSE on failure.
     */
    public function bindValue($param, $value, $type = null)
    {
        /**
         * need to store the value internally since binding is done by reference
         */
        $this->bindParams[] = $value;
        $this->bindParam($param, $this->bindParams[count($this->bindParams) - 1], $type);
    }

    /**
     * Binds a PHP variable to a corresponding named or question mark placeholder in the 
     * SQL statement that was use to prepare the statement. Unlike Doctrine_Adapter_Statement_Interface->bindValue(),
     * the variable is bound as a reference and will only be evaluated at the time 
     * that Doctrine_Adapter_Statement_Interface->execute() is called.
     *
     * Most parameters are input parameters, that is, parameters that are 
     * used in a read-only fashion to build up the query. Some drivers support the invocation 
     * of stored procedures that return data as output parameters, and some also as input/output
     * parameters that both send in data and are updated to receive it.
     *
     * @param mixed $param          Parameter identifier. For a prepared statement using named placeholders,
     *                              this will be a parameter name of the form :name. For a prepared statement
     *                              using question mark placeholders, this will be the 1-indexed position of the parameter
     *
     * @param mixed $variable       Name of the PHP variable to bind to the SQL statement parameter.
     *
     * @param integer $type         Explicit data type for the parameter using the Doctrine_Core::PARAM_* constants. To return
     *                              an INOUT parameter from a stored procedure, use the bitwise OR operator to set the
     *                              Doctrine_Core::PARAM_INPUT_OUTPUT bits for the data_type parameter.
     *
     * @param integer $length       Length of the data type. To indicate that a parameter is an OUT parameter
     *                              from a stored procedure, you must explicitly set the length.
     * @param mixed $driverOptions
     * @return boolean              Returns TRUE on success or FALSE on failure.
     */
    public function bindParam($column, &$variable, $type = null, $length = null, $driverOptions = array())
    {
        if ($driverOptions || $length ) {
            throw new Doctrine_Adapter_Exception('Unsupported parameters:$length, $driverOptions');
        }

        if ($length === null) {
            $oci_length = -1;
        }
        $oci_type = SQLT_CHR;

        switch ($type) {
            case Doctrine_Core::PARAM_STR:
                $oci_type = SQLT_CHR;
            break;
        }

        if (is_integer($column)) {
            $variable_name = ":oci_b_var_$column";
        } else {
            $variable_name = $column;
        }
        //print "Binding $variable to $variable_name".PHP_EOL;
        $status = @oci_bind_by_name($this->statement, $variable_name, $variable, $oci_length, $oci_type);
        if ($status === false) {
           $this->handleError();
        }
        return $status;
    }

    /**
     * Closes the cursor, enabling the statement to be executed again.
     *
     * @return boolean              Returns TRUE on success or FALSE on failure.
     */
    public function closeCursor()
    {
        $this->bindParams = array();
        return oci_free_statement($this->statement);
    }

    /** 
     * Returns the number of columns in the result set 
     *
     * @return integer              Returns the number of columns in the result set represented
     *                              by the Doctrine_Adapter_Statement_Interface object. If there is no result set,
     *                              this method should return 0.
     */
    public function columnCount()
    {
        return oci_num_fields  ( $this->statement );
    }

    /**
     * Fetch the SQLSTATE associated with the last operation on the statement handle 
     *
     * @see Doctrine_Adapter_Interface::errorCode()
     * @return string       error code string
     */
    public function errorCode()
    {
        $oci_error = $this->getOciError();
        return $oci_error['code'];
    }

    /**
     * Fetch extended error information associated with the last operation on the statement handle
     *
     * @see Doctrine_Adapter_Interface::errorInfo()
     * @return array        error info array
     */
    public function errorInfo()
    {
        $oci_error = $this->getOciError();
        return $oci_error['message'] . " : " . $oci_error['sqltext'];
    }

    private function getOciError()
    {
        if (is_resource($this->statement)) {
            $oci_error = oci_error ($this->statement);
        } else {
            $oci_error = oci_error ();
        }

        if ($oci_error) {
            //store the error
            $this->oci_errors[] = $oci_error;
        } else if (count($this->ociErrors) > 0) {
            $oci_error = $this->ociErrors[count($this->ociErrors)-1];
        }
        return $oci_error;
    }

    /**
     * Executes a prepared statement
     *
     * If the prepared statement included parameter markers, you must either:
     * call PDOStatement->bindParam() to bind PHP variables to the parameter markers:
     * bound variables pass their value as input and receive the output value,
     * if any, of their associated parameter markers or pass an array of input-only
     * parameter values
     *
     *
     * @param array $params             An array of values with as many elements as there are
     *                                  bound parameters in the SQL statement being executed.
     * @return boolean                  Returns TRUE on success or FALSE on failure.
     */
    public function execute($params = null)
    {
        if (is_array($params)) {
            foreach ($params as $var => $value) {
                $this->bindValue($var+1, $value);
            }
        }
        
        $result = @oci_execute($this->statement , $this->executeMode );
     
        
        if ($result === false) {
            $this->handleError();
            return false;
         }
        return true;
    }

    /**
     * fetch
     *
     * @see Doctrine_Core::FETCH_* constants
     * @param integer $fetchStyle           Controls how the next row will be returned to the caller.
     *                                      This value must be one of the Doctrine_Core::FETCH_* constants,
     *                                      defaulting to Doctrine_Core::FETCH_BOTH
     *
     * @param integer $cursorOrientation    For a PDOStatement object representing a scrollable cursor, 
     *                                      this value determines which row will be returned to the caller. 
     *                                      This value must be one of the Doctrine_Core::FETCH_ORI_* constants, defaulting to
     *                                      Doctrine_Core::FETCH_ORI_NEXT. To request a scrollable cursor for your 
     *                                      Doctrine_Adapter_Statement_Interface object,
     *                                      you must set the Doctrine_Core::ATTR_CURSOR attribute to Doctrine_Core::CURSOR_SCROLL when you
     *                                      prepare the SQL statement with Doctrine_Adapter_Interface->prepare().
     *
     * @param integer $cursorOffset         For a Doctrine_Adapter_Statement_Interface object representing a scrollable cursor for which the
     *                                      $cursorOrientation parameter is set to Doctrine_Core::FETCH_ORI_ABS, this value specifies
     *                                      the absolute number of the row in the result set that shall be fetched.
     *                                      
     *                                      For a Doctrine_Adapter_Statement_Interface object representing a scrollable cursor for 
     *                                      which the $cursorOrientation parameter is set to Doctrine_Core::FETCH_ORI_REL, this value 
     *                                      specifies the row to fetch relative to the cursor position before 
     *                                      Doctrine_Adapter_Statement_Interface->fetch() was called.
     *
     * @return mixed
     */
    public function fetch($fetchStyle = Doctrine_Core::FETCH_BOTH, $cursorOrientation = Doctrine_Core::FETCH_ORI_NEXT, $cursorOffset = null)
    {
        
        
        switch ($fetchStyle) {
            case Doctrine_Core::FETCH_BOTH :
                $result = oci_fetch_array($this->statement, OCI_BOTH + OCI_RETURN_NULLS + OCI_RETURN_LOBS);
            break;
            case Doctrine_Core::FETCH_ASSOC :
                $result = oci_fetch_array($this->statement, OCI_ASSOC + OCI_RETURN_NULLS + OCI_RETURN_LOBS);
            break;
            case Doctrine_Core::FETCH_NUM :
                $result = oci_fetch_array($this->statement, OCI_NUM + OCI_RETURN_NULLS + OCI_RETURN_LOBS);
            break;
            case Doctrine_Core::FETCH_OBJ:
                $result = oci_fetch_object($this->statement, OCI_NUM + OCI_RETURN_NULLS + OCI_RETURN_LOBS);
            break;
            default:
                throw new Doctrine_Adapter_Exception("This type of fetch is not supported: ".$fetchStyle); 
/*
            case Doctrine_Core::FETCH_BOUND:
            case Doctrine_Core::FETCH_CLASS:
            case FETCH_CLASSTYPE:
            case FETCH_COLUMN:
            case FETCH_FUNC:
            case FETCH_GROUP:
            case FETCH_INTO:
            case FETCH_LAZY:
            case FETCH_NAMED:
            case FETCH_SERIALIZE:
            case FETCH_UNIQUE:
               case FETCH_ORI_ABS:
            case FETCH_ORI_FIRST:
            case FETCH_ORI_LAST:
            case FETCH_ORI_NEXT:
            case FETCH_ORI_PRIOR:
            case FETCH_ORI_REL:
*/
        }
        
        $result = $this->resolveAliases($result,$fetchStyle);
        
        return $result; 
    }
    
    protected function resolveAliases($result, $fetchStyle) {
        if(empty($result))
            return $result; 
        switch($fetchStyle) {
            case Doctrine_Core::FETCH_BOTH:
            case Doctrine_Core::FETCH_ASSOC:
                
                foreach($this->aliasMap as $alias=>$value) { 
                    if(!array_key_exists(strtoupper($alias),$result)) {
                       // $result[$value] = ""; 
                        continue;
                    } 
                    $result[$value] = $result[strtoupper($alias)];    
                }
          
                return $result;
            case Doctrine_Core::FETCH_NUM:
            
             return $result;

            case Doctrine_Core::FETCH_OBJ:
                 foreach($this->aliasMap as $alias=>$value) {
                    $result->{$value} = $result->{$alias}; 
                }
                
                return $result; 
        }
        
    }    

    /**
     * Returns an array containing all of the result set rows
     *
     * @param integer $fetchStyle           Controls how the next row will be returned to the caller.
     *                                      This value must be one of the Doctrine_Core::FETCH_* constants,
     *                                      defaulting to Doctrine_Core::FETCH_BOTH
     *
     * @param integer $columnIndex          Returns the indicated 0-indexed column when the value of $fetchStyle is
     *                                      Doctrine_Core::FETCH_COLUMN. Defaults to 0.
     *
     * @return array
     */
    public function fetchAll($fetchStyle = Doctrine_Core::FETCH_BOTH, $colnum=0)
    {
        $fetchColumn = false;
        $skip = 0;
        $maxrows = -1;
        $data = array();
        $flags = OCI_FETCHSTATEMENT_BY_ROW + OCI_ASSOC;

        $int = $fetchStyle & Doctrine_Core::FETCH_COLUMN;
        
        if ($fetchStyle == Doctrine_Core::FETCH_BOTH) {
            $flags = OCI_BOTH;
            $numberOfRows = @oci_fetch_all($this->statement, $data, $skip, $maxrows, OCI_FETCHSTATEMENT_BY_ROW + OCI_ASSOC + OCI_RETURN_LOBS);
        } else if ($fetchStyle == Doctrine_Core::FETCH_ASSOC) {
            $numberOfRows = @oci_fetch_all($this->statement, $data, $skip, $maxrows, OCI_FETCHSTATEMENT_BY_ROW + OCI_ASSOC + OCI_RETURN_LOBS);
        } else if ($fetchStyle == Doctrine_Core::FETCH_NUM) {
            $numberOfRows = @oci_fetch_all($this->statement, $data, $skip, $maxrows, OCI_FETCHSTATEMENT_BY_ROW + OCI_NUM + OCI_RETURN_LOBS);
        } else if ($fetchStyle == Doctrine_Core::FETCH_COLUMN) {
            while ($row = @oci_fetch_array ($this->statement, OCI_NUM+OCI_RETURN_LOBS)) {
                $data[] = $row[$colnum];
            }
        } else {
            throw new Doctrine_Adapter_Exception("Unsupported mode: '" . $fetchStyle . "' ");
        }
        foreach($data as &$i) {
            $i = $this->resolveAliases($i,$fetchStyle);
        }  
        return $data;
    }

    /**
     * Returns a single column from the next row of a
     * result set or FALSE if there are no more rows.
     *
     * @param integer $columnIndex          0-indexed number of the column you wish to retrieve from the row. If no 
     *                                      value is supplied, Doctrine_Adapter_Statement_Interface->fetchColumn() 
     *                                      fetches the first column.
     *
     * @return string                       returns a single column in the next row of a result set.
     */
    public function fetchColumn($columnIndex = 0)
    {
        if ( ! is_integer($columnIndex)) {
            $this->handleError(array('message'=>"columnIndex parameter should be numeric"));

            return false;
        }
        $row = $this->fetch(Doctrine_Core::FETCH_NUM);
        return isset($row[$columnIndex]) ? $row[$columnIndex] : false;
    }

    /**
     * Fetches the next row and returns it as an object.
     *
     * Fetches the next row and returns it as an object. This function is an alternative to 
     * Doctrine_Adapter_Statement_Interface->fetch() with Doctrine_Core::FETCH_CLASS or Doctrine_Core::FETCH_OBJ style.
     *
     * @param string $className             Name of the created class, defaults to stdClass. 
     * @param array $args                   Elements of this array are passed to the constructor.
     *
     * @return mixed                        an instance of the required class with property names that correspond 
     *                                      to the column names or FALSE in case of an error.
     */
    public function fetchObject($className = 'stdClass', $args = array())
    {
        $row = $this->fetch(Doctrine_Core::FETCH_ASSOC);
        if ($row === false) {
            return false;
        }

        $instantiation_code = "\$object = new $className(";
        $firstParam=true;
        foreach ($args as $index=>$value) {
            if ( ! $firstParam ) {
                $instantiation_code = $instantiation_code . ",";
            } else {
                $firstParam= false;
            }
            if ( is_string($index)) {
                $instantiation_code = $instantiation_code . " \$args['$index']";
            } else {
                $instantiation_code = $instantiation_code . "\$args[$index]";
            }
        }

        $instantiation_code = $instantiation_code . ");";

        eval($instantiation_code);

        //initialize instance of $className class
        foreach ($row as $col => $value) {
             $object->$col = $value;
        }

        return $object;
    }

    /**
     * Returns metadata for a column in a result set
     *
     * @param integer $column               The 0-indexed column in the result set.
     *
     * @return array                        Associative meta data array with the following structure:
     *
     *          native_type                 The PHP native type used to represent the column value.
     *          driver:decl_                type The SQL type used to represent the column value in the database. If the column in the result set is the result of a function, this value is not returned by PDOStatement->getColumnMeta().
     *          flags                       Any flags set for this column.
     *          name                        The name of this column as returned by the database.
     *          len                         The length of this column. Normally -1 for types other than floating point decimals.
     *          precision                   The numeric precision of this column. Normally 0 for types other than floating point decimals.
     *          pdo_type                    The type of this column as represented by the PDO::PARAM_* constants.
     */
    public function getColumnMeta($column)
    {
        if (is_integer($column)) {
            $internal_column = $column +1;
        } else {
            $internal_column = $column;
        }

        $data = array();
        $data['native_type'] = oci_field_type($this->statement, $internal_column);
        $data['flags'] = "";
        $data['len'] = oci_field_size($this->statement, $internal_column);
        $data['name'] = oci_field_name($this->statement, $internal_column);
        $data['precision'] = oci_field_precision($this->statement, $internal_column);

        return $data;
    }

    /**
     * Advances to the next rowset in a multi-rowset statement handle
     * 
     * Some database servers support stored procedures that return more than one rowset 
     * (also known as a result set). The nextRowset() method enables you to access the second 
     * and subsequent rowsets associated with a PDOStatement object. Each rowset can have a 
     * different set of columns from the preceding rowset.
     *
     * @return boolean                      Returns TRUE on success or FALSE on failure.
     */
    public function nextRowset()
    {
        throw new Doctrine_Adapter_Exception("Unsupported");
    }

    /**
     * rowCount() returns the number of rows affected by the last DELETE, INSERT, or UPDATE statement 
     * executed by the corresponding object.
     *
     * If the last SQL statement executed by the associated Statement object was a SELECT statement, 
     * some databases may return the number of rows returned by that statement. However, 
     * this behaviour is not guaranteed for all databases and should not be 
     * relied on for portable applications.
     *
     * @return integer                      Returns the number of rows.
     */
    public function rowCount()
    {
        return @oci_num_rows($this->statement);
    }

    /**
     * Set a statement attribute
     *
     * @param integer $attribute
     * @param mixed $value                  the value of given attribute
     * @return boolean                      Returns TRUE on success or FALSE on failure.
     */
    public function setAttribute($attribute, $value)
    {
        switch ($attribute) {
            case Doctrine_Core::ATTR_ERRMODE;
            break;
            default:
                throw new Doctrine_Adapter_Exception("Unsupported Attribute: $attribute");
        }
        $this->attributes[$attribute] = $value;
    }

    /**
     * Retrieve a statement attribute 
     *
     * @param integer $attribute
     * @see Doctrine_Core::ATTR_* constants
     * @return mixed                        the attribute value
     */
    public function getAttribute($attribute)
    {
        return $this->attributes[$attribute];
    }

    /**
     * Set the default fetch mode for this statement 
     *
     * @param integer $mode                 The fetch mode must be one of the Doctrine_Core::FETCH_* constants.
     * @return boolean                      Returns 1 on success or FALSE on failure.
     */
    public function setFetchMode($mode, $arg1 = null, $arg2 = null)
    {
        throw new Doctrine_Adapter_Exception("Unsupported");
    }

    private function handleError($params=array())
    {

        switch ($this->attributes[Doctrine_Core::ATTR_ERRMODE]) {
            case Doctrine_Core::ERRMODE_EXCEPTION:
                if (isset($params['message'])) {
                    throw new Doctrine_Adapter_Exception($params['message']);
                } else {
                    throw new Doctrine_Adapter_Exception($this->errorInfo());
                }

            break;
            case Doctrine_Core::ERRMODE_WARNING:
            case Doctrine_Core::ERRMODE_SILENT:
            break;
        }
    }

    /**
     * Parse actual query from queryString and returns OCI statement handler
     * @param  string       Query string to parse, if NULL, $this->queryString is used
     * 
     * @return resource     OCI statement handler
     */
    private function parseQuery($query=null)
    {
        if (is_null($query)) {
            $query = $this->queryString;
        }
        $bind_index = 1;
        // Replace ? bind-placeholders with :oci_b_var_ variables
        $query = preg_replace("/(\?)/e", '":oci_b_var_". $bind_index++' , $query);
         
        $this->statement =  @oci_parse($this->connection, $query);

        if ( $this->statement == false )
        {
            throw new Doctrine_Adapter_Exception($this->getOciError());
        }

        return $this->statement;
    }
}