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
 * IDO Implementation of the api filter
 * @author Jannis Mosshammer <jannis.mosshammer@netways.de>
 */
class IcingaApiSearchFilterIdo extends IcingaApiSearchFilter {
    public function createQueryStatement() {
        $field = $this->getField();
        $value = $this->getValue();
        $match = $this->getMatch();

        if ($match == IcingaApiConstants::MATCH_LIKE || $match == IcingaApiConstants::MATCH_NOT_LIKE) {
            $value = str_replace("*","%",$value);
        }

        $statementSkeleton = $field." ".$match." '".$value."' ";

        return $statementSkeleton;
    }

    public function __toDQL(IcingaDoctrine_Query $q,$dqlOnly = false) {
        $field = $this->getField();
        $value = $this->getValue();
        $match = $this->getMatch();

        if ($match == IcingaApiConstants::MATCH_LIKE || $match == IcingaApiConstants::MATCH_NOT_LIKE) {
            $value = str_replace("*","%",$value);
        }
        if (strtolower($q->getConnection()->getDriverName()) == "oracle") {
            $value = str_replace("'","''",$value);
        } else {
            $value = preg_replace("/'/","\'",$value);
        }
        $field = preg_replace("/(.*?) +AS +.+ */i","$1",$field);

        $statementSkeleton = $field." ".$match." '".$value."' ";

        return $statementSkeleton;

    }
}


?>
