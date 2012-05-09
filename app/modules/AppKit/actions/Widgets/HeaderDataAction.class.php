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


class AppKit_Widgets_HeaderDataAction extends AppKitBaseAction {

    public function getDefaultViewName() {
        return 'Success';
    }

    public function execute(AgaviRequestDataHolder $rd) {
        $type = $rd->getParameter('type', 'javascript');




        switch ($type) {
            case 'javascript':
                $includes = array(
                                $this->getContext()->getRouting()->gen('modules.appkit.squishloader.javascript'),
                                $this->getContext()->getRouting()->gen('modules.appkit.ext.applicationState', array('cmd' => 'init')),
                                $this->getContext()->getRouting()->gen('modules.appkit.ext.initI18n')
                            );
                break;

            case 'css':
                $includes = array(
                                $this->getContext()->getRouting()->gen('styles.css')
                            );

                $resources = $this->getContext()->getModel('Resources', 'AppKit');

                $imports = $resources->getCssImports();

                $this->setAttribute('imports', $imports);

                break;
        }

        $this->setAttribute('includes', $includes);

        return $this->getDefaultViewName();
    }

}
