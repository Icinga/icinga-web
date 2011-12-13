<?php

class AppKit_LogAdminModel extends AppKitBaseModel {

    /**
     * Returns a safe log collection
     * @param integer $limit
     * @return Doctrine_Query
     * @author Marius Hein
     */
    public function getLogQuery($limit=1000) {
        return AppKitDoctrineUtil::createQuery()
               ->from('NsmLog')
               ->limit('1000')
               ->orderBy('log_created DESC');
    }

    /**
     * Returns the log query in an executed state
     * @param integer $limit
     * @return Doctrine_Collection
     * @author Marius Hein
     */
    public function getLogCollection($limit=1000) {
        return $this->getLogQuery($limit)->execute();
    }

    public function getLoglevelMap() {
        return array(
                   AgaviLogger::DEBUG	=> 'debug',
                   AgaviLogger::ERROR	=> 'error',
                   AgaviLogger::FATAL	=> 'fatal',
                   AgaviLogger::INFO	=> 'info',
                   AgaviLogger::WARN	=> 'warn',
               );
    }

}

?>
