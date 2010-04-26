<?php

class AppKit_UserAdminModel extends AppKitBaseModel
{

	private static $editableAttributes = array (
		'user_name', 'user_lastname', 'user_firstname',
		'user_email', 'user_disabled'
	);
	
	/**
	 * Creates a collection of NsmUser objects and returns it
	 * @param boolean $disabled
	 * @return Doctrine_Collection
	 * @author Marius Hein
	 */
	public function getUsersCollection($disabled=false) {
		return $this->getUsersQuery($disabled)->execute();
	}	
	
	/**
	 * Returns a unexecuted query for users
	 * @param boolean $disabled
	 * @return Doctrine_Query
	 * @author Marius Hein
	 */
	public function getUsersQuery($disabled=false) {
		$query = Doctrine_Query::create()
		->from('NsmUser')
		->orderBy('user_name ASC');
		
		if ($disabled === false) {
			$query->andWhere('user_disabled=?', array(0));
		}
		
		return $query;
	}
	
	/**
	 * Returns a userrecord by its id
	 * @param integer $id
	 * @return NsmUser
	 * @throws AppKitDoctrineException
	 * @author Marius Hein
	 */
	public function getUserById($id) {
		$user = Doctrine::getTable('NsmUser')->find($id);
		if (!$user instanceof NsmUser) {
			throw new AppKitDoctrineException('User not found with this id');
		}
		return $user;
	}
	
	/**
	 * Updates the 'simple' userdata
	 * @param NsmUser $user
	 * @param AgaviRequestDataHolder $rd
	 * @return boolean
	 * @author Marius Hein
	 */
	public function updateUserData(NsmUser &$user, AgaviRequestDataHolder &$rd) {
		AppKitDoctrineUtil::updateRecordsetFromArray($user, $rd->getParameters(), self::$editableAttributes);
		$user->save();
		
		return true;
	}
	
	/**
	 * Updates the user password, this is only a smart reference
	 * @param $user
	 * @param $user_password
	 * @return unknown_type
	 * @throws AppKitException
	 * @author Marius Hein
	 */
	public function updateUserPassword(NsmUser &$user, $user_password) {
		AppKitRandomUtil::initRand(); 
		
		$user->updatePassword($user_password);
		$user->save();
		
		return true;
	}
	
	/**
	 * Updates the user roles memberships. First
	 * revoke all roles, after that assign again
	 * @param NsmUser $user
	 * @param array $user_roles
	 * @return boolean
	 * @author Marius Hein
	 */
	public function updateUserroles(NsmUser &$user, array $user_roles) {
		// first revoke all roles!
		$user->NsmUserRole->delete();
		
		// Adding the roles selected
		foreach ($user_roles as $index=>$role_id) {
			$role = Doctrine::getTable('NsmRole')->find($role_id);
			if ($role instanceof NsmRole) {
				$user->NsmRole[$index] = $role;
			}
		}
		
		// Checking the principal
		if (!$user ->NsmPrincipal->principal_id) {
			$user ->NsmPrincipal->principal_type = NsmPrincipal::TYPE_ROLE;
		}
		
		// Save the record
		$user->save();
		
		return true;
	}
	
	/**
	 * Toggles the activity of a NsmUser object
	 * @param NsmUser $user
	 * @return boolean
	 * @throws AppKitException
	 * @author Marius Hein
	 */
	public function toggleActivity(NsmUser &$user) {
		AppKitDoctrineUtil::toggleRecordValue($user);
		$user->save();
		return true;
	}
}

?>