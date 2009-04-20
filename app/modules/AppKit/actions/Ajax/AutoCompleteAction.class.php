<?php
/**
 * 
 * 	<setting name="appkit.ajax.ac.types.grapher.panel">
 *		<parameter name="component">NgpPanel</parameter>
 *		<parameter name="search_fields">
 *			<parameter>panel_title</parameter>
 *		</parameter>
 *		<parameter name="value_field">panel_title</parameter>
 *		<parameter name="key_field">panel_id</parameter>
 *		
 *		<!--
 *		<parameter name="static_where">
 *			<parameter name="FIELD">VALUE</parameter>
 *		</parameter>
 *		-->
 *		
 *		<parameter name="userid_fields">
 *			<parameter>panel_user_id</parameter>
 *		</parameter>
 *
 *		<parameter name="parent">
 *			<parameter>
 *				<parameter name="alias">h</parameter>
 *				<parameter name="component">NgHost</parameter>
 *				<parameter name="field">host_id</parameter>
 *			</parameter>
 *		</parameter>
 *
 *		<parameter name="static_values">
 *			<parameter name="##ALL##">All services</parameter>
 *		</parameter>
 *
 *	</setting>
 * 
 * @package NETWAYSAppKit
 * @subpackage AppKit
 * 
 * @author Marius Hein
 * 
 * @copyright Authors
 * @copyright NETWAYS GmbH
 * 
 * @version $Id$
 */
class AppKit_Ajax_AutoCompleteAction extends NETWAYSAppKitBaseAction
{
	/**
	 * Returns the default view if the action does not serve the request
	 * method used.
	 *
	 * @return     mixed <ul>
	 *                     <li>A string containing the view name associated
	 *                     with this action; or</li>
	 *                     <li>An array with two indices: the parent module
	 *                     of the view to be executed and the view to be
	 *                     executed.</li>
	 *                   </ul>
	 */
	public function getDefaultViewName()
	{
		return 'Success';
	}
	
	public function executeRead(AgaviRequestDataHolder $rd) {
		$prefix = AgaviConfig::get('de.netways.appkit.ajax.ac.prefix');
		$search = sprintf('%s.%s', $prefix, $rd->getParameter('type'));
		
		if ($rd->getParameter('noout', false) !== false) {
			$this->setAttributeByRef('json_result', array());
		}
		
		elseif (($config = AgaviConfig::get($search))) {
			
			$ac_holder = $this->getContext()->getModel('AjaxAutoCompleteData', 'AppKit');
			
			// Doctrine parameters
			if ($config['component'] && $config['key_field'] && $config['value_field']) {
				$ac_holder->setComponentName($config['component']);
				$ac_holder->setKeyField($config['key_field']);
				$ac_holder->setValueField($config['value_field']);
				$ac_holder->setSearchFields($config['search_fields']);
			}
			// Parents
			if ($rd->getParameter('parent', false) !== false) {
				$ac_holder->setParents($rd->getParameter('parent'));
				$ac_holder->setParentsConfig($config['parent']);
			}
			
			// static values
			if (is_array($config['static_values'])) {
				$ac_holder->setStaticValues($config['static_values']);
			}
			
			// Userdependent field
			if (is_array($config['userid_fields'])) {
				foreach ($config['userid_fields'] as $user_field) {
					$ac_holder->addStaticWhereCondition($user_field, $this->getContext()->getUser()->getAttribute('userobj')->user_id);
				}
			}
			
			if (is_array($config['static_where'])) {
				foreach ($config['static_where'] as $field=>$val) {
					$ac_holder->addStaticWhereCondition($field, $val);
				}
			}
			
			$ac_holder->setSearchValue($rd->getParameter('query', null));
			
			$results = $ac_holder->getResults();
			
			$this->setAttributeByRef('json_result', $results);
		}
		
		return $this->getDefaultViewName();
	}
	
	public function handleError(AgaviRequestDataHolder $rd) {
		return $this->getDefaultViewName();
	}
	
	public function isSecure() {
		return true;
	}
}

?>