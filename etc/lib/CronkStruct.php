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

class CronkStruct {

    /**
     * Cronk record to modify
     * @var Cronk
     */
    private $cronk;

    /**
     * Dom of xml struct
     * @var DOMDocument
     */
    private $dom;

    /**
     * Json state data
     * @var stdClass
     */
    private $json;

    /**
     * Cronk UID
     * @var string
     */
    private $uid;

    /**
     * Cronk DB UID
     * @var integer
     */
    private $id;

    /**
     * Human readable name of cronk
     * @var string
     */
    private $name;

    public function __construct(Cronk $record=null)
    {
        $this->dom = new DOMDocument('1.0', 'utf-8');
        $this->xpath = new DOMXPath($this->dom);
        $this->xpath->registerNamespace('ae', 'http://agavi.org/agavi/config/global/envelope/1.0');

        if ($record !== null) {
            $this->setCronkRecord($record);
        }
    }

    public function setCronkRecord(Cronk $record)
    {
        $this->setId($record->cronk_id);
        $this->setUid($record->cronk_uid);
        $this->setName($record->cronk_name);

        if ($record->cronk_xml) {
            $this->setXml($record->cronk_xml);
        }

        $this->cronk = $record;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param \stdClass $json
     */
    public function setJson($json)
    {
        $this->json = $json;
    }

    /**
     * @return \stdClass
     */
    public function getJson()
    {
        return $this->json;
    }

    /**
     * @param string $uid
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
    }

    /**
     * @return string
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets xml and initialize depending objects
     * @param string $xml xml dump
     */
    public function setXml($xml)
    {
        $this->dom->loadXML($xml);
        $this->getXPathInstance(true);

        $state = $this->getXmlParam('state');
        if ($state) {
            $this->setJson(json_decode($state, true));
        }
    }

    /**
     * Manages Xpath instance on this dom
     * @param bool $fresh
     * @return DOMXPath
     */
    public function getXPathInstance($fresh=false)
    {
        static $xpath = null;

        if ($xpath === null || $fresh === true) {
            $xpath = null;
            $xpath = new DOMXPath($this->dom);
            $xpath->registerNamespace('ae', 'http://agavi.org/agavi/config/global/envelope/1.0');
        }

        return $xpath;
    }

    /**
     * Returns parameter value from xml
     * @param $name Parameter name
     * @return null|string
     */
    public function getXmlParam($name)
    {

        $xpath = $this->getXPathInstance();
        $query = '/cronk/ae:parameter/ae:parameter[@name=\'' . $name. '\']';
        $nodes = $xpath->query($query);

        if ($nodes->length>0) {
            $node = $nodes->item(0);
            return $node->textContent;
        }

        return null;
    }

    public function writeXmlParam($name, $content, $cdata=true)
    {
        $xpath = $this->getXPathInstance();
        $query = '/cronk/ae:parameter/ae:parameter[@name=\'' . $name. '\']';
        $nodes = $xpath->query($query);

        if ($nodes->length>0) {
            $node = $nodes->item(0);
            while ($node->hasChildNodes()) {
                $child = $node->firstChild;
                $node->removeChild($child);
            }

            $sub = null;

            if ($cdata) {
                $sub = $this->dom->createCDATASection($content);
            } {
                $sub = $this->dom->createTextNode($content);
            }

            $node->appendChild($sub);
        }
    }

    public function persistToDatabase()
    {
        $this->writeXmlParam('state', json_encode($this->getJson()), true);
        $this->cronk->cronk_xml = $this->dom->saveXML();
        $return = ($this->cronk->state() === Doctrine_Record::STATE_CLEAN) ? false : true;
        $this->cronk->save();
        return $return;
    }

    // ------------------------------------------------------------------------
    // Modifying methods
    // ------------------------------------------------------------------------

    /**
     * Upgrade cronk event sub frame
     *
     * This is needed to use cronk below 1.8 with 1.8 event expander row
     *
     * @return bool
     * @deprecated 1.8.3
     */
    public function upgradeEventSubFrame()
    {
        $module = $this->getXmlParam('module');
        $action = $this->getXmlParam('action');
        $return = true;

        if ($module !== 'Cronks' || $action !== 'System.ViewProc' || !is_array($this->getJson())) {
            return false;
        }

        $columns = $this->json['nativeState']['columns'];

        if (!isset($columns[1]) || $columns[1]['id'] !== 'event-sub-frame' || $columns[1]['width'] !== 20) {
            $newCol = array(
                'id'        => 'event-sub-frame',
                'width'     => 20
            );

            array_splice($columns, 1, 0, array($newCol));

            $this->json['nativeState']['columns'] = $columns;
            $return = true;
        }

        if (isset($this->json['colModel']['columns'])) {
            $columns = $this->json['colModel']['columns'];
            $found = 0;

            foreach ($columns as $index=>$column) {
                if (isset($column['id']) && $column['id'] === 'event-sub-frame') {
                    $found = $index;
                    break;
                }
            }

            $newCol = array(
                'width'         => 20,
                'dataIndex'     => 'id',
                'id'            => 'event-sub-frame',
                'sortable'      => false
            );


            if ($found>0) {
                $columns[$found] = $newCol;
            } else {
                $columns[] = $newCol;
                $return = true;
            }

            $this->json['colModel']['columns'] = $columns;
        }

        return $return;
    }

    /**
     * Try to fix old relations between columns
     *
     * EXPERIMENTAL
     *
     * @return bool
     * @deprecated 1.8.3
     */
    public function fixOldColumns()
    {

        /**
         * Columns we can drop, becase they
         * moved into the row expander
         */
        $removeIndex = array(
            'pnp4nagios_host_link',
            'pnp4nagios_host_image_hover',
            'pnp4nagios_service_link',
            'pnp4nagios_service_image_hover',
            'service_info',
            'service_history_link',
            'service_to_host_link',
            'host_info',
            'host_history_link'
        );

        /**
         * Test columns that they are hidden, always
         */
        $hideIndex = array(
            'instance_name',
            'host_object_id',
            'service_object_id'
        );

        if (isset($this->json['colModel']['columns'])) {
            $colModel = $this->json['colModel']['columns'];
            $colIndex = $this->json['nativeState']['columns'];

            // ----------------------------------------------------------------
            // Drop columns
            // ----------------------------------------------------------------
            foreach ($colModel as $index => $column) {
                foreach ($removeIndex as $dataIndex) {

                    if ($column['dataIndex'] === $dataIndex) {

                        if (isset($colIndex[$index])) {
                            // All this special columns are small!
                            if ($colIndex[$index]['width'] < 100) {
                                array_splice($colIndex, $index, 1); // < Drop column
                            }
                        }

                        array_splice($colModel, $index, 1);
                    }

                }
            }

            // ----------------------------------------------------------------
            // Hide columns
            // ----------------------------------------------------------------
            foreach ($colModel as $index => $column) {
                foreach ($hideIndex as $dataIndex) {
                    if ($column['dataIndex'] === $dataIndex) {
                        $column['hidden'] = true;
                        $colModel[$index] = $column;
                    }
                }
            }

            $this->json['colModel']['columns'] = $colModel;
            $this->json['nativeState']['columns'] = $colIndex;

            return true;
        }

        return false;
    }

    /**
     * Drop the layout state from custom cronks.
     *
     * Thanks to eric for this idea
     */
    public function dropLayoutState()
    {
        unset($this->json['colModel']);
        unset($this->json['nativeState']);
    }
}