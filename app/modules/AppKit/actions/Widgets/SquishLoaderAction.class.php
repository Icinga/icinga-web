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


class AppKit_Widgets_SquishLoaderAction extends AppKitBaseAction {

    public function getDefaultViewName() {
        return 'Success';
    }

    public function executeRead(AgaviRequestDataHolder $rd) {
        $headers = $rd->getHeaders();
        $route =  $this->getContext()->getRequest()->getAttribute(
            'matched_routes', 'org.agavi.routing'
        );

        $route = $route[count($route)-1];
        $type = explode(".",$route);
        $type = $type[count($type)-1];
        $loader = $this->getContext()->getModel(
                      'SquishFileContainer',
                      'AppKit',
                      array(
                          'type' => $type,
                          'route' => $route
                      )
                  );
        if (isset($headers['IF_NONE_MATCH'])) {
            $etag = str_replace('"',"",$headers['IF_NONE_MATCH']);
            if($loader->hasEtagInCache($etag)) {
                header("HTTP/1.1 304 NOT MODIFIED");
                die();
            }

        }
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


        $loader->squishContents();
        $content = $loader->getContent();
        $this->setAttribute('content', $content. chr(10));
        $this->setAttribute("etag",$loader->getCachedChecksum());

        return $this->getDefaultViewName();
    }




}
