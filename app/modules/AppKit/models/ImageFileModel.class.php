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


class AppKit_ImageFileModel extends AppKitBaseModel
    implements AgaviISingletonModel {

    private static $extensions = array('png', 'gif', 'jpg');
    private static $headers = array(
                                  'png' => 'image/png',
                                  'gif' => 'image/gif',
                                  'jpg' => 'image/jpeg',
                              );

    private $image_string = null;
    private $image_file = null;
    private $image_extension = null;
    private $image_header = null;

    public function __construct($image_string=null) {
        if ($image_string !== null) {
            $this->setImageString($image_string);
        }
    }

    public function getImageResource() {
        if (file_exists($this->image_file)) {
            $resource = fopen($this->image_file, 'r');
            return $resource;
        }

        return false;
    }

    public function setImageString($image_string) {
        $this->image_string = str_replace('.', '/', $image_string);
        $this->image_file = $this->findImage();
    }

    public function getImageString() {
        return $this->image_string;
    }

    public function getImageFile() {
        return $this->image_file;
    }

    public function getImageFileRelative() {
        return AppKitStringUtil::absolute2Rel($this->getImageFile());
    }

    public function getImageContentType() {
        return $this->image_header;
    }

    public function getImageType() {
        return $this->image_extension;
    }

    /**
     * @return SplFileInfo
     */
    public function getFileInfo() {
        return new SplFileInfo($this->image_file);
    }

    public function findImage() {
        foreach(self::$extensions as $extension) {
            $file = sprintf('%s/%s.%s', $this->getImagePath(), $this->image_string, $extension);

            if (file_exists($file)) {
                $this->image_extension = $extension;
                $this->image_header = self::$headers[$extension];
                return $file;
            }
        }

        return false;
    }

    public function getImagePath() {
        return AgaviConfig::get('org.icinga.appkit.image_path');
    }

}

?>