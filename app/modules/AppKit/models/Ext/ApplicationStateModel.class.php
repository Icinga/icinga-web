<?php

/**
 * Interface to read and write application states
 * @author mhein
 *
 */
class AppKit_Ext_ApplicationStateModel extends AppKitBaseModel implements AgaviISingletonModel {

    const PREFNS = 'org.icinga.ext.appstate';

    public function stateAvailable() {
        if ($this->getContext()->getUser()->isAuthenticated()) {
            return true;
        }

        return false;
    }

    public function readState() {
        $data = null;

        if ($this->stateAvailable()) {
            $data = $this->getContext()->getUser()->getPrefVal(self::PREFNS, null, true);
        }

        return $data;
    }

    public function writeState($data) {
        $merge = array();

        if ($this->stateAvailable()) {
            $existing = json_decode($this->readState());

            if (is_array($existing)) foreach($existing as $v) {
                $merge[$v->name] = $v->value;
            }

            foreach(json_decode($data) as $v) {
                $merge[$v->name] = $v->value;
            }
            $data = array();
            foreach($merge as $k => $v) {
                $data[] = (object) array(
                              'name' => $k,
                              'value' => $v
                          );
            }
            $this->getContext()->getUser()->setPref(self::PREFNS, json_encode($data), true, true);
        }
    }
}
