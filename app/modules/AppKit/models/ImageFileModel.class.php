<?php

class AppKit_ImageFileModel extends AppKitBaseModel
    implements AgaviISingletonModel {

    private static $extensions = array('png', 'gif', 'jpg');
    private static $headers = array(
                                  'png'	=> 'image/png',
                                  'gif'	=> 'image/gif',
                                  'jpg'	=> 'image/jpeg',
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