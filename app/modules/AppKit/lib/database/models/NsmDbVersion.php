<?php
class NsmDbVersion extends BaseNsmDbVersion {
    /**
     * (non-PHPdoc)
     * @see BaseNsmDbVersion::setUp()
     */
    public function setUp() {
        parent::setUp();

        $options = array(
            'created' =>  array('name' => 'created'),
            'updated' =>  array('name' => 'modified')
        );

        $this->actAs('Timestampable', $options);
    }
}