<?php
class Api_Store_Modifiers_StoreSortModifierModel extends IcingaBaseModel 
        implements IDataStoreModifier 
{
    protected $mappedParameters = array(
        "sortfield" => "sortfield",
        "dir" => "dir"
    );
    
    private $sortfield ;
    private $dir = "DESC";

    public function handleArgument($name,$value) {
        switch($name)   {
            case 'sortfield':
                $this->sortfield = $value;
                break;
            case 'dir':
                if($value == "ASC" || $value == "DESC")
                    $this->dir = $value;
                else
                    throw new InvalidArgumentException("Sort direction $value is not allowed");
                break;
        }
    }
    
    public function getMappedArguments() {
        return $this->mappedParameters;
    }

    public function modify(&$o) {
        $this->modifyImpl($o); // type safe call
    }

    protected function modifyImpl(Doctrine_Query &$o) {
        if($this->sortfield)
            $o->orderBy($this->sortfield." ".$this->dir);
    }
    
    public function __getJSDescriptor() {
        return array(
            "type"=>"sort",
            "params" => $this->getMappedArguments()
        );
    }
}

?> 
