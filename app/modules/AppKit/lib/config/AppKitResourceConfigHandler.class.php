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


/**
 * AppKitResourceConfigHandler collects resources like javascript and css files from global and module configs
 * @author Eric Lippmann <eric.lippmann@netways.de>
 * @since 1.5.0
 */
class AppKitResourceConfigHandler extends AgaviXmlConfigHandler {

    /**
     * Documents' xml namespace
     * @var const string
     */
    const XML_NAMESPACE = 'http://icinga.org/icinga/config/global/resource/1.0';

    /**
     * Resource types to collect
     * @var array string type => string suffix
     */
    protected $_resources = array('javascript' => '.js', 'css' => '.css', 'css_import' => '.css');

    /**
     * Collect resource files
     *
     * @param string resource
     * @param string resource suffix
     * @param AgaviXmlConfigDomElement resource config
     *
     * @return array files
     *
     * @author Eric Lippmann <eric.lippmann@netways.de>
     * @since 1.5.0
     */
    protected function collectResource($resource, $sfx, AgaviXmlConfigDomElement $cfg) {
        $resources = array();

        foreach($cfg->getChildren($resource, null, false) as $rcfg) {
            $_resources = array();

            foreach($rcfg->getAgaviParameters() as $r) {
                if (is_dir($r)) {
                    $_resources = array_merge(
                                      $_resources,
                                      array_keys(iterator_to_array(
                                                     AppKitIteratorUtil::RegexRecursiveDirectoryIterator(
                                                         $r,
                                                         sprintf('/^[^.].+\%s$/i', $sfx)
                                                     )
                                                 ))
                                  );
                } else {
                    $_resources[] = $r;
                }
            }

            $resources = array_merge($resources, $_resources);
        }

        return $resources;
    }

    /**
     * (non-PHPdoc)
     * @see AgaviIXmlConfigHandler::execute()
     */
    public function execute(AgaviXmlConfigDomDocument $doc) {
        $resources = array_combine(
                         array_keys($this->_resources),
                         array_fill(0, count($this->_resources), array())
                     );

        $jactions = array();

        foreach($doc->getConfigurationElements() as $cfg) {

            // Collecting resources
            foreach($this->_resources as $resource => $sfx) {
                $resources[$resource] = array_unique(array_merge(
                                            $resources[$resource],
                                            $this->collectResource($resource, $sfx, $cfg)
                                        ));
            }
            // Collecting javascript actions
            foreach($cfg->getChildren('jactions', null, true) as $jaction) {
                $jactions[] = $jaction->getAgaviParameters();
            }

        }

        return $this->generate(sprintf(
                                   '$this->resources = array_merge_recursive($this->resources, %s);%s$this->jactions=array_merge_recursive($this->jactions, %s);',
                                   var_export($resources, true),
                                   chr(10),
                                   var_export($jactions, true)
                               ), $doc->documentURI);
    }

}
