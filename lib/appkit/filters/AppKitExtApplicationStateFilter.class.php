<?php
/**
 * Stupid filter to write cookie information into the user preference table
 * @author mhein
 *
 */
class AppKitExtApplicationStateFilter extends AgaviFilter implements AgaviIActionFilter {
	
	const EXT_COOKIE_PATTERN	= 'ys-';
	const DATA_NAMESPACE		= 'de.icinga.ext.appstate';
	
	/**
	 * (non-PHPdoc)
	 * @see lib/agavi/src/filter/AgaviFilter#executeOnce($filterChain, $container)
	 */
	public function executeOnce(AgaviFilterChain $filterChain, AgaviExecutionContainer $container) {
		
		// Check if we have a valid user
		if ($this->getContext()->getUser()->isAuthenticated()) {
			
			$request = $this->getContext()->getRequest();
			$response = $container->getResponse();
			$cookies = $request->getRequestData()->getCookies();
			
			$data = array ();
			
			// Iterate through the cookies and find extjs cookies
			foreach ($cookies as $name=>$val) {
				if(strpos($name, self::EXT_COOKIE_PATTERN) === 0) {
					$data[ substr($name, 3) ] = ($val);
					/*
					 * @todo check why this is not working
					 */
					// $response->unsetCookie($name);	// 1st try
					
					setCookie($name, '', time()-1000, '/'); // okay this works really
				}
				
				
			}
			
			
			
			// Yes, we have data, serialize and push to db
			if (count($data)) {
				
				/*
				 * We need the old data so we didn't loose the existing state
				 */
				$save = $this->getContext()->getUser()->getPrefVal(self::DATA_NAMESPACE, null, true);
				if ($save) {
					$save = unserialize( base64_decode( $save ) );
				}
				else {
					$save = array ();
				}
				
				$save = array_merge($save, $data);
				
				$this->cleanUpData($save);
				
				$save = base64_encode(serialize($save));
				$this->getContext()->getUser()->setPref(self::DATA_NAMESPACE, $save, true, true);
			}
		}

		parent::executeOnce($filterChain, $container);
	}
	
	private function cleanUpData(array &$data) {
		$check = array ();
		
		$values = array_keys ($data);
		
		foreach ($data as $key=>$json) {
			$check[] = json_decode($json, true);
		}
		
		foreach ($values as $val) {
			if (AppKitArrayUtil::searchKeyRecursive($val, $check)) {
				
			}
			else {
				unset($data[$val]);
			}
		}
		
		return true;
	}
	
	
	
}

?>