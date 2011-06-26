<?php
class Api_Store_Modifiers_StorePaginationModifierModel extends IcingaBaseModel implements IDataStoreModifier 
{
    public function getMappedArguments() {
        return array(
            "offset" => "offset",
            "limit" => "limit"
        );
    }

    private $offset = 0;
    private $limit = -1;
    private function checkVal($val) {
        if(!is_numeric($val))
            throw new InvalidArgumentException("Filter/Offset must be an integer"); 
        if(!intval($val)<0)
            throw new InvalidArgumentException("Filter/Offset must be an integer >= 0"); 
        return intval($val);
    }
    
    public function handleArgument($name,$value) {
        switch($name) {
            case 'offset':
                $this->offset = $this->checkVal($value);
                break;
            case 'limit':
                $this->limit = $this->checkVal($value);
                break;
        }
    }
     
    public function modify(&$o) {
        $this->modifyImpl($o); // type safe call
    }

    protected function modifyImpl(Doctrine_Query &$o) { 
        $o->offset($this->offset); 
        if($this->limit>0)
            $o->limit($this->limit);
    }
    
    public function __getJSDescriptor() {
        return array(
            "type"=>"pagination",
            "params" => $this->getMappedArguments() 
        );
    }
    
}
?>
