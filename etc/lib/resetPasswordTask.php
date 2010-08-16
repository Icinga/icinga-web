<?php

require_once(dirname(__FILE__)."/agaviConsoleTask.php");

class resetPasswordTask extends agaviConsoleTask {

	const MIN_PASSWORD_LENGTH = 6;

	protected $user_name = null;
	protected $password = null;

	public function setUser($user) {
		$this->user_name = $user;
	}

	public function setPassword($password) {
		$this->password = $password;
	}

	protected function getUserObj($user_name) {
		$res = Doctrine_Query::create()
		->from('NsmUser')
		->andWhere('user_name=?', array($user_name))
		->limit(1)
		->execute();

		if ($res->count() == 1) {
			return $res->getFirst();
		}
		return null;
	}

	public function main() {
		parent::main();

		$user = $this->getUserObj($this->user_name);

		if (isset($user) && $user instanceof  NsmUser) {

			if ($user->user_authsrc !== 'internal') {
				throw new BuildException('Not an internal user! (user_authsrc=\''. $user->user_authsrc. '\'');
			}

			if (strlen($this->password) < self::MIN_PASSWORD_LENGTH) {
				throw new BuildException('Password should be at least '. self::MIN_PASSWORD_LENGTH. ' characters long!');
			}

			$user->updatePassword($this->password);

			$user->save();
			
			return true;
		}
		else {
			throw new BuildException('User \''. $this->user_name. '\' not found!');
		}

	}

}

?>
