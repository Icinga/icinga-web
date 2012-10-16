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
 * Model to compress binary image data.
 * 
 * 
 * 
 * @package IcingaWeb
 * @subpackage Reporting
 * @since 1.8.0
 */
class Reporting_Image_CompressorModel extends ReportingBaseModel
implements AgaviISingletonModel {

    const DEFAULT_COMPRESSION_LEVEL = 9;
    const DEFAULT_CONTENT_TYPE = 'image/png';
    
    private $imageData = null;
    
    private $contentType = null;
    
    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);
        
        if ($this->testAvailability() === true) {
            $this->getContext()->getLoggerManager()->log(
                'REPORTING: GD available. Compressing images and convert to PNG',
                AgaviLogger::DEBUG
            );
        }
    }
    
    public function compressImage($data, $content_type) {
        $this->imageData = null;
        
        if ($this->testAvailability() !== true) {
            $this->imageData = $data;
            $this->contentType = $content_type;
        } else {
            $im = $this->createImageResource($data);
            ob_start();
            imagepng($im);
            $this->imageData = ob_get_clean();
            $this->contentType = self::DEFAULT_CONTENT_TYPE;
            imagedestroy($im);
        }
        
        return true;
    }
    
    public function getImageData() {
        return $this->imageData;
    }
    
    public function getContentType() {
        return $this->contentType;
    }
    
    public function getBase64Image() {
        return base64_encode($this->getImageData());
    }
    
    private function createImageResource($data) {
        $im = imagecreatefromstring($data);
        
        if ($data === false) {
            throw new AppKitModelException('Could not create image resource');
        }
        
        imagealphablending($im, false);
        imagesavealpha($im, true);
        
        return $im;
    }
    
    private function testAvailability() {
        return function_exists('imagecreatefromstring')
            && function_exists('imagepng');
    }
    
}