<?php

class Cronks_System_CronkPortalSuccessView extends CronksBaseView {
    public function executeHtml(AgaviRequestDataHolder $rd) {

        $customViewFields  = array(
                                 "cr_base"=>false,
                                 "groupField"=>false,
                                 "groupDir"=>false,
                                 "template"=>false,
                                 "crname"=>false,
                                 "title"=>false
                             );

        $rd->setParameter("isURLView",true);
        foreach($customViewFields as $name=>&$val) {
            $val = $rd->getParameter($name,null);

            if ($val == null) {
                $rd->setParameter("isURLView",false);
                break;
            }
        }

        if ($rd->getParameter("isURLView"))  {
            $this->formatFields($customViewFields);
            $rd->setParameter("URLData",json_encode($customViewFields));
        }

        $this->setupHtml($rd);
        $this->setAttribute('_title', 'Icinga.Cronks.CronkPortal');
    }

    /**
     * Converts the url and agavi routing friendly format of the parameters to
     * its original values
     */
    public function formatFields(array &$fields) {
        $formatFields = array("cr_base");
        foreach($formatFields as $fieldName) {
            $field = $fields[$fieldName];
            $result = array();

            // Because of empty arrays in javascript
            $field = preg_replace('/;$/', '', $field);

            // split at ;
            $fieldParts = explode(';',$field);

            foreach($fieldParts as $currentField) {
                if (!$currentField) {
                    continue;
                }

                //rebuild field
                $parts = array();

                if (preg_match("/(\w*?)\|(.*?)_\d+=(.*)/",$currentField,$parts)) {

                    // @todo: Works better without, quickfix!
                    //if(!isset($result[$parts[1]]))
                    //	$result[$parts[1]] = array();

                    $result[$parts[1]."[".$parts[2]."]"] = $parts[3];
                } else {
                    $str = explode("=",$currentField);

                    $result[$str[0]] = $str[1];
                }
            }
            $fields[$fieldName] = $result;
        }

    }
}

?>