<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2013 Icinga Developer Team.
// All rights reserved.
// 
// icinga-web is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
// 
// icinga-web is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with icinga-web.  If not, see <http://www.gnu.org/licenses/>.
// -----------------------------------------------------------------------------
// {{{ICINGA_LICENSE_CODE}}}


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
                }
            }
            if($splice !== false)
                array_splice($mergeResult,$splice,1);
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