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

class Cronks_Provider_CronkSecuritySuccessView extends CronksBaseView {

    public function executeHtml(AgaviRequestDataHolder $rd) {

    }

    public function executeJson(AgaviRequestDataHolder $rd) {
        return json_encode(array(
            'success'    => $this->getAttribute('success'),
            'errors'     => $this->getAttribute('errors'),
            'cronk'      => $this->getAttribute('cronk'),
            'roles'      => $this->getAttribute('roles'),
            'role_uids'  => $this->getAttribute('role_uids'),
            'principals' => $this->getAttribute('principals')
        ));
    }
}
