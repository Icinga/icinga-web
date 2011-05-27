<?php

class AppKit_SquishFileContainerModel extends AppKitBaseModel {
    const TYPE_JAVASCRIPT	= 'js';
    const TYPE_STYLESHEET	= 'css';

    private $files			= array();
    private $actions		= array();
    private $type			= null;
    private $content		= null;

    /**
     * (non-PHPdoc)
     * @see lib/agavi/src/model/AgaviModel#initialize($context, $parameters)
     */
    public function initialize(AgaviContext $context, array $parameters = array()) {

        if(array_key_exists('type', $parameters)) {
            $this->setType($parameters['type']);
        }

        parent::initialize($context, $parameters);
    }

    /**
     * Adding a single file
     * @param $file
     * @param $type
     * @return unknown_type
     */
    public function addFile($file) {
        if(file_exists($file)) {
            $this->files[] = $file;
            return true;
        }

        throw new AppKitModelException('File not found: '. $file);
    }

    /**
     * Adding an array of files
     * @param array $files
     * @param $type
     * @return unknown_type
     */
    public function addFiles(array $files) {
        $this->files = $files + $this->files;
        return true;
    }

    /**
     * Sets agavi actions
     * @param array $actions
     * @return unknown_type
     */
    public function setActions(array $actions) {
        $this->actions = ($actions) + $this->actions;
        return true;
    }

    private function setType($type) {
        $this->type = $type;
        return true;
    }

    public function getType() {
        return $this->type;
    }

    public function squishContents() {

        $this->content = null;

        if(is_array($this->files)) {
            $loader = new AppKitBulkLoader();
            $loader->setCompress(false);

            array_walk($this->files, array(&$loader, 'setFile'));

            $this->content .= $loader->getContent();
        }

        //	$this->content = JSMin::minify($this->content);

        return null;
    }

    public function getContent() {
        return $this->content;
    }

    public function getActions() {
        return $this->actions;
    }
}

?>