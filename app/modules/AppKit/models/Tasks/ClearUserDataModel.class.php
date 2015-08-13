<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
//
// Copyright (c) 2009-2015 Icinga Developer Team.
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


/**
 * Export agavi's clear cache method outbound
 */
class AppKit_Tasks_ClearUserDataModel extends AppKitBaseModel {

    /**
     * Array of user id's this model should handle
     * @var array
     */
    private $userIds = array();

    public function setUserIds(array $userIds) {
        $this->userIds = $userIds;
    }

    /**
     * Clear application state
     */
    public function clearAppstate() {
        $state = AppKitDoctrineUtil::createQuery()
            ->delete('NsmUserPreference')
            ->andWhereIn('upref_user_id', $this->userIds)
            ->execute();
    }

    /**
     * Clear session for user
     */
    public function clearSession()
    {
        $result = AppKitDoctrineUtil::createQuery()
            ->select('session_entry_id, session_data')
            ->from('NsmSession')
            ->execute();

        foreach ($result as $session) {
            $m = array();

            // :"user_id";s:1:"1";
            if (preg_match('/"user_id";s:\d+:"([^"]+)"/', $session->session_data, $m)) {
                foreach ($this->userIds as $userId) {
                    if ($m[1] == $userId) {
                        AppKitDoctrineUtil::createQuery()
                            ->delete('NsmSession')
                            ->andWhere('session_entry_id=?', array($session->session_entry_id))
                            ->execute();
                    }
                }
            }
        }
    }
}
