<?php

class AppKit_Widgets_AddHeaderDataAction extends AppKitBaseAction
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
	
	public function execute(AgaviRequestDataHolder $rd) {
		
		$header = $this->getContext()->getModel('HeaderData', 'AppKit');
		
		// Adding preconfigured style files
		if (is_array( ($css_files = AgaviConfig::get('de.icinga.appkit.include_styles')) )) {
			foreach ($css_files as $css_file) {
				$header->addCssFile($css_file);
			}
		}
		
		// Adding squished javascript files
		$squish_url = $this->getContext()->getRouting()->gen('appkit.squishloader', array('type' => AppKitBulkLoader::CODE_TYPE_JAVASCRIPT));
		$header->addJsFile($squish_url);
		
		// Adding inline files
		$files = AgaviConfig::get('de.icinga.appkit.include_javascript');
		if (is_array($files) && array_key_exists('inline', $files) && is_array($files['inline'])) {
			foreach ($files['inline'] as $js_file) {
				$header->addJsFile($js_file);
			}
		}
		
		
		// Adding some meta tags to the page header
		if (is_array( ($tags = AgaviConfig::get('de.icinga.appkit.meta_tags')) )) {
			foreach ($tags as $tag_name => $tag_val) {
				$header->addMetaTag($tag_name, $tag_val);
			}
		}
		
		return $this->getDefaultViewName();
	}
}

?>