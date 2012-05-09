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
 * Handling all image icons for use in Ext providers
 * @author mhein
 *
 */
class AppKit_IconFilesModel extends AppKitBaseModel implements Countable {

    /**
     * Filesystem path
     * @var string
     */
    private $real_path = null;

    /**
     * Relative web path
     * @var string
     */
    private $web_path = null;

    /**
     * Glob output list
     * @var array
     */
    private $files = array();

    /**
     * Sub part from the icon directory
     * @var string
     */
    private $part = null;

    /**
     * Number of globbed files
     * @var integer
     */
    private $count = 0;

    /**
     * (non-PHPdoc)
     * @see AppKitBaseModel::initialize()
     */
    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);
        $this->real_path = AgaviConfig::get('org.icinga.appkit.image_absolute_path');
        $this->web_path = AgaviConfig::get('org.icinga.appkit.image_path');

        if ($this->hasParameter('path')) {
            $this->setDirectoryPart($this->getParameter('path'));
        }

        if ($this->hasParameter('query')) {
            $this->globFiles($this->getParameter('query'));
        }
    }

    /**
     * Add the sub directory part after images/ e.g. cronks
     * @param unknown_type $path
     */
    public function setDirectoryPart($path) {
        $this->real_path .= DIRECTORY_SEPARATOR. $path;
        $this->web_path .= DIRECTORY_SEPARATOR. $path;
        $this->part = $path;
    }

    /**
     * Collect the files together
     * @param string $query
     * @return boolean always true
     */
    public function globFiles($query) {
        $s = '.'. $this->getParameter('extension', 'png');

        $q = $this->real_path
             . DIRECTORY_SEPARATOR
             . '*'
             . $query
             . '*'
             . $s;

        $files = glob($q);

        foreach($files as $file) {
            $name = basename($file, $s);

            $this->files[] = array(
                                 'web_path' => $this->web_path. DIRECTORY_SEPARATOR. rawurlencode(basename($file)),
                                 'name' => $name,
                                 'short' => $this->part . '.'. $name
                             );
        }

        $this->count = count($files);

        return true;
    }

    /**
     * Return number of globbed files
     * @return number
     */
    public function Count() {
        return $this->count;
    }

    /**
     * Return the files
     * @return array
     */
    public function Files() {
        return $this->files;
    }
}
