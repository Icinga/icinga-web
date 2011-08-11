<?php
class Api_Store_Modifiers_StoreGroupingModifierModel extends IcingaBaseModel
    implements IDataStoreModifier {

    protected $mappedParameters = array(
                                      "groupfields" => "groupfields"
                                  );

    protected $groupfields = array();

    public function setGroupfields($field) {
        if (is_array($field)) {
            $field = implode(",",$field);
        }

        $this->groupfields = $field;
    }
    public function getGroupfields() {
        return $this->groupfields;
    }


    /**
    * @see IDataStoreModifier::handleArgument
    **/
    public function handleArgument($name,$value) {
        switch ($name)   {
            case 'groupfields':
                $this->groupfields = $value;
                break;
        }
    }

    /**
    * @see IDataStoreModifier::getMappedArguments();
    **/
    public function getMappedArguments() {
        return $this->mappedParameters;
    }

    /**
    *
    * @see IDataStoreModifier::modify();
    **/
    public function modify(&$o) {
        $this->modifyImpl($o); // type safe call
    }

    /**
    * Typesafe call to modify
    * @access private
    **/
    protected function modifyImpl(Doctrine_Query &$o) {
        if ($this->groupfields) {
            $groups = explode(",",$this->groupfields);
            foreach($groups as $group)
            $o->addGroupBy($group);
        }
    }

    /**
    * @see IDataStoreModifier::getJSDescriptor
    **/
    public function __getJSDescriptor() {
        return array(
                   "type"=>"group",
                   "params" => $this->getMappedArguments()
               );
    }
}


