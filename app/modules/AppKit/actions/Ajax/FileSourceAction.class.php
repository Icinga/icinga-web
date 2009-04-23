<?php

class AppKit_Ajax_FileSourceAction extends ICINGAAppKitBaseAction
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
		$prefix = AgaviConfig::get('de.icinga.appkit.ajax.fs.prefix');
		$search = sprintf('%s.%s', $prefix, $rd->getParameter('type'));

		if (($config = AgaviConfig::get($search))) {
			try {
				
				$fs = $this->getContext()->getModel('FileSource', 'AppKit');
				$content = $fs->readFileContent($config['file']);
				
				$this->setAttribute('content', $content);
				
			}
			catch (AppKitModelException $e) {
				throw new AppKitModelException('FileSource could not find the file!');
			}
		}
		
		return $this->getDefaultViewName();
	}
	
	public function isSecure() {
		return true;
	}
}

?>