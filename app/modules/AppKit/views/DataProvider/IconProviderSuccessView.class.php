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


class AppKit_DataProvider_IconProviderSuccessView extends AppKitBaseView {
    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);

        $this->setAttribute('_title', 'DataProvider.IconProvider');
    }

    public function executeJson(AgaviRequestDataHolder $rd) {
        $success = true;

        $model = $this->getAttribute('filemodel');

        if (!$rd->getParameter('path') || !$model) {
            $success = false;
        }

        $out = array(
                   'success'    => $success,
                   'total'      => 0,
                   'rows'       => array()
               );

        if ($success) {
            $out['total'] = $model->Count();
            $out['rows'] = $model->Files();
        }

        return json_encode($out);
    }
}

?>
