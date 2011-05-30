<?php

class Icinga_Doctrine_Table extends Doctrine_Table {
    /**
    * Overwrite createQuery function so it creates Icinga_Doctrine_Queries instead of
    * Doctrine_Queries. This is needed in order to apply user restrictions to automatically
    *
    **/
    public function createQuery($alias = '') {
        if (! empty($alias)) {
            $alias = ' ' . trim($alias);
        }

        $class = $this->getAttribute(Doctrine_Core::ATTR_QUERY_CLASS);

        return Icinga_Doctrine_Query::create($this->_conn, $class)
               ->from($this->getComponentName() . $alias);
    }



}
