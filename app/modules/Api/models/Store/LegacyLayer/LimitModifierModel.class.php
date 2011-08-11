<?php

class Api_Store_LegacyLayer_LimitModifierModel extends Api_Store_Modifiers_StorePaginationModifierModel {

    public function setSearchLimit($start, $length = false) {
        $this->setOffset($start);

        if ($length) {
            $this->setLimit($length);
        }
    }
}
