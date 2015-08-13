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
 * Model that represent a placeholder image we can not found a corresponding
 * image id in Jasper's response
 * 
 * @package IcingaWeb
 * @subpackage Reporting
 * @since 1.8.0
 */
class Reporting_Image_PlaceholderModel extends ReportingBaseModel
implements AgaviISingletonModel {
    const BASE_IMAGE_FILE = '/icinga/idot.png';
    private $imageData = null;
    private $contentType = null;
    
    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);
        $this->imageData = $this->createImage();
    }
    
    private function createImage() {
        $file = AgaviConfig::get('org.icinga.appkit.image_absolute_path'). self::BASE_IMAGE_FILE;
        
        if (!is_file($file)) {
            throw new AppKitModelException("Could not read base image file: ". $file);
        }
        
        $this->contentType = 'image/png';
        return file_get_contents($file);
    }
    
    public function getImageData() {
        return $this->imageData;
    }
    
    public function getContentType() {
        return $this->contentType;
    }
    
    public function __toString() {
        return $this->getImageData();
    }
}
