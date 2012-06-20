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


class AppKit_Widgets_SquishLoaderAction extends AppKitBaseAction {

    public function getDefaultViewName() {
        return 'Success';
    }

    public function executeRead(AgaviRequestDataHolder $rd) {
        $ra = explode('.', array_pop(
                          $this->getContext()->getRequest()->getAttribute(
                              'matched_routes', 'org.agavi.routing'
                          )
                      ));

        $type = array_pop($ra);

        $loader = $this->getContext()->getModel(
                      'SquishFileContainer',
                      'AppKit',
                      array('type' => $type)
                  );

        $resources = $this->getContext()->getModel('Resources', 'AppKit');

        switch ($type) {
            case 'javascript':
                try {
                    $loader->addFiles(
                        $resources->getJavascriptFiles()
                    );
                    
                    $loader->setActions($resources->getJavascriptActions());

                } catch (AppKitModelException $e) {
                    $this->setAttribute('errors', $e->getMessage());
                }

                break;

            case 'css':
                try {
                    $loader->addFiles(
                        $resources->getCssFiles()
                    );
                } catch (AppKitModelException $e) {
                    $this->setAttribute('errors', $e->getMessage());
                }

                break;
        }

        $headers = $rd->getHeaders();
        $etag = rand();

        if (isset($headers['IF_NONE_MATCH'])) {
            $etag = str_replace('"',"",$headers['IF_NONE_MATCH']);
        }

        if (!$loader->squishContents($etag)) {
            $content = $loader->getContent();
            $this->setAttribute('content', $content. chr(10));
        } else {
            $this->setAttribute('existsOnClient',true);
        }

        $this->setAttribute('etag',$loader->getChecksum());

        return $this->getDefaultViewName();
    }




}
