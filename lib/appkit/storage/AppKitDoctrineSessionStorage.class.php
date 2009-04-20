<?php

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
	
	public function sessionDestroy($id) {
		
		$result = Doctrine_Query::create()
		->delete('NsmSession')
		->andWhere('session_name=? and session_id=?', array($this->getParameter('session_name'), 'id'))
		->execute();
		
		if ($result > 0) return true;
		
		return false;
		
	}
	
	public function sessionGC($lifetime) {
		$result = Doctrine_Query::create()
		->andWhere('TIME_TO_SEC(TIMEDIFF(now(), session_created)) > ?', array($lifetime))
		->delete('NsmSession')
		->execute();
		
		$this->getContext()->getLoggerManager()
		->logDebug('Session garbage collector, deleted %d old sessions.', $result);
		
		if ($result > 0) return true;
		
		return false;
	}
	
	public function sessionOpen($path, $name) {
		// Hm should we do anything here?
	}
	
	public function sessionRead($id) {
		$session_name = $this->getParameter('session_name');
		
		$result = Doctrine_Query::create()
		->from('NsmSession')
		->andWhere('session_id=? and session_name=?', array($id, $session_name))
		->execute();
		
		if ($result->count() == 0) {
			$this->NsmSession = new NsmSession();
			$this->NsmSession->session_id = $id;
			$this->NsmSession->session_name = $session_name;
			return '';
		}
		else {
			$this->NsmSession = $result->getFirst();
			$data = gzinflate($this->NsmSession->session_data);
			
			if (md5($data) == $this->NsmSession->session_checksum) {
				return $data;
			}
			
			throw new AppKitDoctrineSessionStorageException('Sessiondata integrity error, should be: '. $this->NsmSession->session_checksub);
		}

	}
	
	public function sessionWrite($id, &$data) {
		$this->NsmSession->session_data = gzdeflate($data, $this->getParameter('gzip_level', 6));
		$this->NsmSession->session_checksum = md5($data);
		$this->NsmSession->save();
	}
	
}

class AppKitDoctrineSessionStorageException extends AppKitException {}

?>