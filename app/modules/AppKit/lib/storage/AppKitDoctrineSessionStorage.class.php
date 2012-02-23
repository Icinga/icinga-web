<?php

/**
 * Store agavi sesstion data (or simple php session data) into
 * appkit doctrine tables
 * @author mhein
 *
 */
class AppKitDoctrineSessionStorage extends AgaviSessionStorage {

    /**
     * @var NsmSession
     */
    private $NsmSession = null;

    public function initialize(AgaviContext $context, array $parameters = array()) {

        // initialize the parent
        parent::initialize($context, $parameters);

        session_set_save_handler(
            array(&$this, 'sessionOpen'),
            array(&$this, 'sessionClose'),
            array(&$this, 'sessionRead'),
            array(&$this, 'sessionWrite'),
            array(&$this, 'sessionDestroy'),
            array(&$this, 'sessionGC')
        );

    }

    public function sessionClose() {
        // Hm, the same as sessionOpen?!

    }

    /**
     * Trigger the sesstion destroy and remove
     * data from database
     * @param string $id
     */
    public function sessionDestroy($id) {

        $result = AppKitDoctrineUtil::createQuery()
                  ->delete('NsmSession')
                  ->andWhere('session_name=? and session_id=?', array($this->getParameter('session_name'), 'id'))
                  ->execute();

        if ($result > 0) {
            return true;
        }

        return false;

    }

    /**
     Trigger for garbage selector, runs
     every X times to remove old sessions
     * @param integer $lifetime
     */
    public function sessionGC($lifetime) {
        $diff = time() - $lifetime;

        $date = new DateTime(strftime('%Y-%m-%d %H:%M:%S', $diff));
        // $date->sub(new DateInterval(sprintf('PT%dS', $lifetime)));

        $this->getContext()->getLoggerManager()->log('Deleting sessions older that '. $date->format('c'), AgaviLogger::DEBUG);

        $result = AppKitDoctrineUtil::createQuery()
                  ->andWhere('session_modified < ?', array($date->format('Y-m-d H:i:s')))
                  ->delete('NsmSession')
                  ->execute();

        if ($result > 0) {
            $this->getContext()->getLoggerManager()
            ->log(sprintf('Session garbage collector, deleted %d old sessions.', $result), AgaviLogger::INFO);
        }

        if ($result > 0) {
            return true;
        }

        return false;
    }

    /**
     * Trigger to open the session
     * @param string $path
     * @param string $name
     */
    public function sessionOpen($path, $name) {
        // Hm should we do anything here?
    }

    /**
     * Reads data from doctrine tables and return its content
     * @param string $id
     * @throws AppKitDoctrineSessionStorageException
     */
    public function sessionRead($id) {
        $session_name = $this->getParameter('session_name');
        AppKitLogger::verbose("Reading session %s ",$session_name);
        $result = AppKitDoctrineUtil::createQuery()
                  ->select('*')
                  ->from('NsmSession n')
                  ->andWhere('session_id=? and session_name=?', array($id, $session_name))
                  ->execute();

        if ($result->count() == 0) {
            AppKitLogger::verbose("No session found, creating new ");
            $this->NsmSession = new NsmSession();
            $this->NsmSession->session_id = $id;
            $this->NsmSession->session_name = $session_name;

            return '';
        } else {
            AppKitLogger::verbose("Session found in database, reading data");
            $this->NsmSession = $result->getFirst();
            $data = $this->NsmSession->get('session_data');

            if (is_resource($data)) {
                AppKitLogger::verbose("Reading session from BLOB");
                $data = stream_get_contents($this->NsmSession->get('session_data'));
            }
            AppKitLogger::verbose("MD5 Check: %s == %s ", md5($data), $this->NsmSession->session_checksum);
            if (md5($data) == $this->NsmSession->session_checksum) {
                AppKitLogger::verbose("Using persisted session");
                return $data;
            }
            AppKitLogger::verbose("Session invalid, deleting it");
            $this->NsmSession->delete();
            throw new AppKitDoctrineSessionStorageException('Sessiondata integrity error, should be: '. $this->NsmSession->session_checksum);
        }

    }

    /**
     * Writes session data to database tables
     * @param string $id
     * @param mixed $data
     */
    public function sessionWrite($id, &$data) {
        AppKitLogger::verbose("Writing new session information (checksum=%s)",md5($data));
        $this->NsmSession->session_data = $data;
        $this->NsmSession->session_checksum = md5($data);
        $this->NsmSession->session_modified = date('Y-m-d H:i:s');
        $this->NsmSession->save();
        AppKitLogger::verbose("Writing new session information successful");
    }

}

class AppKitDoctrineSessionStorageException extends AppKitException {}
