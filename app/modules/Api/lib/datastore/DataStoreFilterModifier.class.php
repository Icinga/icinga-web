<?
class DataStoreFilterParseException extends AppKitException {};
class DataStoreFilterModifier extends IDataStoreModifier
{
    protected $mapped_arguments = array('filter_json');
    protected $static_quirks = version_compare(PHP_VERSION,"5.3.0","<"); 
    
    /**
    * defines which filter classes are allowed for this FilterModifier
    **/
    protected $filterClasses = array
        "GenericStoreFilter",
        "GenericStoreFilterGroup"
    );

  
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
    
    protected setFilter($filter) {
        if(is_a($filter,"StoreFilterBase")) {
            $this->filter = $filter; 
        }
        else {
            $filter = $this->tryParse();
            $this->filter = $filter; 
        }
    }
    
    public function getFilter() {
        return $this->filter;
    }
    
    protected customArgumentParser($filterString) {
        return null; 
    }
    
    protected function tryParse($filter) {
        $filter = null;
        try {
            $filter = $this->customArgumentParser($filter);
            if($filter && is_a($filter,"StoreFilterBase"))
                return $filter; 
            foreach($this->filterClasses as $filterClass) {
                if(class_exists($filterClass)) {
                    // work around lack of lsb in php <5.3.0
                    if($this->static_quirks)
                        $filter = call_user_func($filterClass."::parse",$filter);
                    else
                        $filter = $filterClass::parse($filter);
                }
                if($filter && is_a($filter,"StoreFilterBase"))
                    return $filter;
            }  
        } catch(Exception $e) {
            throw new DataStoreFilterParseException($e->getMessage()); 
        }
    } 
}


?>
