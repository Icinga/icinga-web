<?php

class AppKit_Widgets_AddHeaderDataAction extends NETWAYSAppKitBaseAction
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
		if (is_array( ($css_files = AgaviConfig::get('de.netways.appkit.include_styles')) )) {
			foreach ($css_files as $css_file) {
				$header->addCssFile($css_file);
			}
		}
		
		// Adding preconfigured javascript files
		if (is_array( ($js_files = AgaviConfig::get('de.netways.appkit.include_javascript')) )) {
			foreach ($js_files as $js_file) {
				$header->addJsFile($js_file);
			}
		}
		
		if (is_array( ($tags = AgaviConfig::get('de.netways.appkit.meta_tags')) )) {
			foreach ($tags as $tag_name => $tag_val) {
				$header->addMetaTag($tag_name, $tag_val);
			}
		}
		
		return 'Success';
		
	}
}

?>