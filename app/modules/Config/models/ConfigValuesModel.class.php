<?php

/**
 * Class to create viewable configuration items of icinga
 * @author mhein
 *
 */
class Config_ConfigValuesModel extends IcingaConfigBaseModel {
    const HIDDEN_VALUE = '**HIDDEN_ENTRY**';
    
    /**
     * Creates an array of readable configuration items
     * @return multitype:string mixed
     */
    public function getValuesForDisplay() {
        $out = array();
        $values = AgaviConfig::toArray();
        ksort($values);
        foreach ($values as $k=>$v) {
            if (preg_match('/password|passwd/i', $k)) {
                $out[] = array (
                    'key' => $k,
                    'value' => self::HIDDEN_VALUE
                );
            } else {
                $out[] = array (
                    'key' => $k,
                    'value' => $this->getValuesDump($v)
                );
            }
        }
        return $out;
    }
    
    private function getValuesDump($var) {
        if (ob_start()) {
            var_dump($var);
            return htmlspecialchars(ob_get_clean());
        }
    }
    
}

?>