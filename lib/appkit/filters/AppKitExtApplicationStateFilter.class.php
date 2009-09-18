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
					$data[ substr($name, 3) ] = $val;
					
					/*
					 * @todo check why this is not working
					 */
					// $response->unsetCookie($name);	// 1st try
					
					setCookie($name, '', time()-1000, '/'); // okay this works really
				}
				
				
			}
			
			// Yes, we have data, serialize and push to db
			if (count($data)) {
				$data = base64_encode(serialize($data));
				$this->getContext()->getUser()->setPref(self::DATA_NAMESPACE, $data, true, true);
			}
		}

		parent::executeOnce($filterChain, $container);
	}
	
}

?>