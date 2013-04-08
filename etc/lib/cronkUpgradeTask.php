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

require 'agaviConsoleTask.php';
require 'CronkStruct.php';

class cronkUpgradeTask extends agaviConsoleTask {

    public function main() {
        parent::main();

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

            $cronkStruct->dropLayoutState();

            $update = $cronkStruct->persistToDatabase();
            if ($update) {
                $this->log('Columns upgraded', Project::MSG_WARN);
                continue;
            }

            $this->log('Nothing to to', Project::MSG_INFO);

        }
    }


}
