<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
//
// Copyright (c) 2009-present Icinga Developer Team.
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

require 'agaviConsoleTask.php';
require 'CronkStruct.php';

/**
 * Class cronkUpgradeAbstract
 *
 * Abstract model to call update methods on CronkStruct
 * object.
 */
abstract class CronkUpgradeAbstract extends agaviConsoleTask {

    /**
     * Phing default entry poing
     */
    public function main() {
        parent::main();
        $this->cronkStructIterator();
    }

    /**
     * Cronk iterator
     *
     * Queries the database, iterate over all
     * cronks and create a CronkStructure object.
     *
     * For each cronk the abstract method in this class
     * is called
     */
    protected function cronkStructIterator() {
        $cronksQuery = Doctrine_Query::create()
            ->select('*')
            ->from('Cronk c');

        $cronks = $cronksQuery->execute();

        /** @var $cronk Cronk */
        $cronk = null;

        /** @var $structs CronkStruct[] */
        $structs = array();

        foreach ($cronks as $cronk) {
            $cronkStruct = new CronkStruct($cronk);
            $this->log('Testing cronk '. $cronkStruct->getName());

            $this->upgradeMethod($cronkStruct);

            $update = $cronkStruct->persistToDatabase();
            if ($update) {
                $this->log('Cronk changed', Project::MSG_WARN);
                continue;
            }

            $this->log('Nothing to to', Project::MSG_INFO);

        }
    }

    /**
     * Notifier function to modify cronk data
     * @param CronkStruct $struct
     * @return mixed
     */
    abstract protected function upgradeMethod(CronkStruct $struct);
}
