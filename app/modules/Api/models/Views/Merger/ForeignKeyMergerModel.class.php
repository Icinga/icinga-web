<?php

class Api_Views_Merger_ForeignKeyMergerModel extends IcingaApiBaseModel {

    private $target = null;
    private $mergeField = null;
    private $type = "left";
    private $createView = null;

    public function setup($target,$mergeField,$type="left") {
        $this->target = $target;
        $this->mergeField = $mergeField;
        $this->type = $type;
        
    }

    public function getView() {
        if(!$this->createView)
            $this->createMergeView();
        return $this->createView;
    }

    public function merge(array &$result) {
        if(!$this->createView)
            $this->createMergeView();
        $mergeResult = $this->createView->getResult();
        AppKitLogger::verbose("Got mergeResult \w %i results %s", count($mergeResult),$mergeResult);
        switch($this->type) {
            case 'left':
                $this->mergeLeft($result,$mergeResult);
                return $result;
            case 'right':
                return $this->mergeRight($mergeResult,$result);

                break;
            
        }
    }

    private function mergeLeft(&$result,&$mergeResult) {
        $id; $splice;

        foreach($result as &$resultRow) {
            $id = $resultRow[$this->mergeField];
            $splice = false;
            for($i=0;$i<count($mergeResult);$i++) {
                $additional = $mergeResult[$i];
                if($additional[$this->mergeField] == $id) {
                    $resultRow = array_merge($additional,$resultRow);
                    $splice = $i;
                    break;
                }
            }
            if($splice !== false)
                array_splice($mergeResult,$splice);
        }
    }

    private function mergeRightKeepOrder(&$result,&$mergeResult) {
        $id;

        foreach($result as &$resultRow) {
            $id = $resultRow[$this->mergeField];
            for($i=0;$i<count($mergeResult);$i++) {
                $additional = $mergeResult[$i];
                if($additional[$this->mergeField] == $id) {
                    $resultRow = array_merge($additional,$resultRow);
                    break;
                }
            }
        }
        
    }

    private function mergeRight(&$result,&$mergeResult) {
        $id;

        foreach($result as &$resultRow) {
            $id = $resultRow[$this->mergeField];
            for($i=0;$i<count($mergeResult);$i++) {
                $additional = $mergeResult[$i];
                if($additional[$this->mergeField] == $id) {
                    $resultRow = array_merge($additional,$resultRow);
                    break;
                }
            }
        }
    }


    private function createMergeView() {
        $this->createView = $this->getContext()->getModel("Views.ApiDQLView","Api",array(
            "view" => $this->target
        ));
    }
}