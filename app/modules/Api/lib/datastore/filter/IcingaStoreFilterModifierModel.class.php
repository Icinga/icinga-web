<?php
class IcingaStoreFilterModifierModel extends DataStoreFilterModifier implements IDataStoreModifier {

    // Use these filterclasses
    protected $filterClasses = array(
                                   "ApiStoreFilter",
                                   "ApiStoreFilterGroup"
                               );

    public function modify(&$o) {
        // type safe call
        $this->modifyImpl($o);
    }

    protected function modifyImpl(IcingaDoctrine_Query &$o) {
        $f = $this->filter;

        if ($f) {
            $f->__toDQL($o);
        }

    }


}
