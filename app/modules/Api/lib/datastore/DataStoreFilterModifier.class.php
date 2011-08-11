<?

/**
* Thrown if an invalid formatted filter will be send to the FilterModifier
*
* @package Icinga_Api
* @category DataStoreModifier
*
* @author Jannis Moßhammer <jannis.mosshammer@netways.de>
**/
class DataStoreFilterParseException extends AppKitException {};

/**
* Modifier that allows filtered queries via filterobjects that are derived from
* @see GenericStoreFilter or @see GenericStoreFilterGroup
*
* Exports the getFilter() and setFilter functions to the DataStore
* @package Icinga_Api
* @category DataStoreModifier
*
*
* @author Jannis Moßhammer <jannis.mosshammer@netways.de>
**/
abstract class DataStoreFilterModifier extends IcingaBaseModel  implements IDataStoreModifier {
    protected $mapped_arguments = array('filter'=>'filter_json');
    protected $static_quirks = true;
    /**
    * An array of classnames (strings) that defines which filter classes
    * are used by this FilterModifier. The parse functions of this classes will be
    * called in the order of the classnames in this array, so if 2 filters match a
    * string the first filter will be used.
    * Example:
    * <code>
    * </code>
    *
    * @var Array
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    protected $filterClasses = array(
                                   "GenericStoreFilter",
                                   "GenericStoreFilterGroup"
                               );

    /**
    *
    * @see IDataStoreModifier::__getJSDescriptor()
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
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
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function handleArgument($name,$value) {
        if ($name == 'filter_json') {
            $this->setFilter($value);
        }
    }
    /**
    * @see IDataStoreModifier::getMappedArguments
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function getMappedArguments() {
        return $this->mapped_arguments;
    }
    /**
    * Sets filter to this modifier
    * If an instance of StoreFilerBase is provided, this will be used
    * otherwise every filterClass defined in $filterClasses will be called
    * with the static ::parse($filter,$parser,[$instance]) method. The first
    * filter that matches will be use. The exact filter string format will be definedd
    * in the filter classes or @see customArgumentParser
    *
    * @param    mixed   A @see StoreFilterBase or an Array/Object defining the filter
    *
    * throws  DataStoreFilterParseException if a filter can't be parsed
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function setFilter($filter) {
        if ($filter instanceof StoreFilterBase) {
            $this->filter = $filter;
        } else {
            $filter = $this->tryParse($filter);
            $this->filter = $filter;
        }
    }
    /**
    * Returns the current set filter
    * @return StoreFilterBase|null
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function getFilter() {
        return $this->filter;
    }

    /**
    * Execute custom filter parsing directly in the modifier
    *
    * If you don't want your filter classes to parse filter strings, you can just
    * overload this function and let it return an instance of @see StoreFilterBase
    * based on the filterString. The @see parse function of the filter classes will be
    * ignored then.
    *
    * @param String     The string given to this modifier as a filter descriptor
    * @return StoreFilterBase|null
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    protected function customArgumentParser($filterString) {
        return null;
    }

    /**
    * Tries to parse the filter parameter provided and return the filter if succeeded.
    * Calls every filter defined in @see filterClasses and calls their parse function
    *
    * @param mixed  The filter object to send to the filterclasses
    * @return null|StoreFilerBase   The filter defined by this descriptor or null if it doesn't match
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function tryParse($filter) {
        $filterParsed = null;

        try {

            $filterParsed = $this->customArgumentParser($filter);

            if ($filterParsed && $filterParsed instanceof StoreFilterBase) {
                return $filterParsed;
            }

            foreach($this->filterClasses as $filterClass) {

                if (class_exists($filterClass)) {
                    // work around lack of lsb in php <5.3.0

                    if ($this->static_quirks) {
                        $filterParsed = call_user_func($filterClass."::parse",$filter,$this);
                    } else {
                        $filterParsed = $filterClass::parse($filter,$this);
                    }
                }

                if ($filterParsed && $filterParsed instanceof StoreFilterBase) {
                    return $filterParsed;
                }
            }
        } catch (Exception $e) {
            throw new DataStoreFilterParseException($e->getMessage());
        }

        return null;
    }
    /**
    * @access private
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function __construct() {
        $this->static_quirks = version_compare(PHP_VERSION,"5.3.0","<");
    }
}


?>
