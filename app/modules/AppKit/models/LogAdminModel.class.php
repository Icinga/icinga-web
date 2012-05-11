<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2012 Icinga Developer Team.
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


class AppKit_LogAdminModel extends AppKitBaseModel {

    /**
     * Returns a safe log collection
     * @param integer $limit
     * @return Doctrine_Query
     * @author Marius Hein
     */
    public function getLogQuery($limit=1000) {
        return AppKitDoctrineUtil::createQuery()
               ->from('NsmLog')
               ->limit('1000')
               ->orderBy('log_created DESC');
    }

    /**
     * Returns the log query in an executed state
     * @param integer $limit
     * @return Doctrine_Collection
     * @author Marius Hein
     */
    public function getLogCollection($limit=1000) {
        return $this->getLogQuery($limit)->execute();
    }

    public function getLoglevelMap() {
        return array(
                   AgaviLogger::DEBUG   => 'debug',
                   AgaviLogger::ERROR   => 'error',
                   AgaviLogger::FATAL   => 'fatal',
                   AgaviLogger::INFO    => 'info',
                   AgaviLogger::WARN    => 'warn',
               );
    }

}

?>
