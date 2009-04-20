<?php

class AppKit_RoleAdminModel extends NETWAYSAppKitBaseModel
{

	private static $editableAttributes = array (
		'role_name', 'role_description', 'role_disabled'
	);
	
	/**
	 * 
	 * @param boolean $disabled
	 * @return Doctrine_Query
	 * @author Marius Hein
	 */
	public function getRoleQuery($disabled = false) {
		$roles = Doctrine_Query::create()
		->from('NsmRole')
		->orderBy('role_name ASC');
		
		if ($disabled === false) {
			$roles->andWhere('role_disabled = ?', array($disabled));
		}
		
		return $roles;
	}
	
	/**
	 * 
	 * @param boolean $disabled
	 * @return Doctrine_Collection
	 * @author Marius Hein
	 */
	public function getRoleCollection($disabled = false) {
		return $this->getRoleQuery($disabled)->execute();
	}
	
	/**
	 * Load a role record by id
	 * @param integer $role_id
	 * @return NsmRole
	 * @throws AppKitDoctrineException
	 * @author Marius Hein
	 */
	public function getRoleById($role_id) {
		$role = Doctrine::getTable('NsmRole')->find($role_id);
		if (!$role instanceof NsmRole) {
			throw new AppKitDoctrineException('Role not found with this id');
		}
		return $role;
	}
	
	/**
	 * Updates the simple role data
	 * @param NsmRole $role
	 * @param AgaviRequestDataHolder $rd
	 * @return boolean
	 * @author Marius Hein
	 */
	public function updateRoleData(NsmRole &$role, AgaviRequestDataHolder &$rd) {
		AppKitDoctrineUtil::updateRecordsetFromArray($role, $rd->getParameters(), self::$editableAttributes);
		$role->save();
		return true;
	}
	
	/**
	 * Toggles the role status
	 * @param NsmRole $role
	 * @return boolean
	 * @throws AppKitException
	 * @author Marius Hein
	 */
	public function toggleActivity(NsmRole &$role) {
		AppKitDoctrineUtil::toggleRecordValue($role);
		$role->save();
		return true;
	}
	
}

?>