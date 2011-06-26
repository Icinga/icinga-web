<?
class DataStoreFilterParseException extends AppKitException {};
abstract class DataStoreFilterModifier extends IcingaBaseModel  implements IDataStoreModifier
{
    protected $mapped_arguments = array('filter'=>'filter_json');
    protected $static_quirks = true;    
    /**
    * defines which filter classes are allowed for this FilterModifier
    **/
    protected $filterClasses = array(
        "GenericStoreFilter",
        "GenericStoreFilterGroup"
    );

    public function __getJSDescriptor() {
        $allowedFilter = array();
        foreach($this->filterClasses as $filter) {         
            $cl = new $filter();
            $allowedFilter[] = $cl->__getJSDescriptor();
        }
        return array(
            "type"=> "filter",
            "allowedFilter" => $allowedFilter,
            "params" => $this->getMappedArguments()
        );
    } 
  
    protected $filter = null;
    
     /**
    * @see IDataStoreModifier::handleArgument
    **/
    public function handleArgument($name,$value) {
        if($name == 'filter_json')
            $this->setFilter($value);
    }
    /**
    * @see IDataStoreModifier::getMappedArguments
    **/
    public function getMappedArguments() {
        return $this->mapped_arguments;
    }
    /**
    * Adds an filter to this modifier
    * If an instance of StoreFilerBase is provided, this will be used
    * otherwise every filterClass defined in $filterClasses will be called
    * with the static ::parse($filter,$parser,[$instance]) method. The first 
    * filter that matches will be used
    * 
    * @param    mixed   A @see StoreFilterBase or an Array/Object defining the filter
    * 
    * @throws   DataStoreFilterParseException if a filter can't be parsed
    **/ 
    protected function setFilter($filter) {
        if(is_a($filter,"StoreFilterBase")) {
            $this->filter = $filter; 
        }
        else {
            $filter = $this->tryParse($filter); 
            $this->filter = $filter; 
        }
    }
    
    public function getFilter() {
        return $this->filter;
    }
    
    protected function customArgumentParser($filterString) {
        return null; 
    }
    
    public function tryParse($filter) {
        $filterParsed = null;
        try {
            
            $filterParsed = $this->customArgumentParser($filter);
            if($filterParsed && is_a($filterParsed,"StoreFilterBase"))
                return $filterParsed; 
            foreach($this->filterClasses as $filterClass) {
                
                if(class_exists($filterClass)) {
                    // work around lack of lsb in php <5.3.0
                    
                    if($this->static_quirks) { 
                        $filterParsed = call_user_func($filterClass."::parse",$filter,$this);
                    } else {
                        $filterParsed = $filterClass::parse($filter,$this);
                    }
                }
                if($filterParsed && is_a($filterParsed,"StoreFilterBase"))
                    return $filterParsed;
            }  
        } catch(Exception $e) {
            throw new DataStoreFilterParseException($e->getMessage()); 
        }
      
        return null;
    } 
    public function __construct() {
        $this->static_quirks = version_compare(PHP_VERSION,"5.3.0","<"); 
    

    }
}


?>
