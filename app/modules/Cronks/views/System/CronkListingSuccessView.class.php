<?php

class Cronks_System_CronkListingSuccessView extends ICINGACronksBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'Icinga.Cronks.CronkListing');
	}

	/**
	 * Deliver available cronks through json
	 * @param AgaviRequestDataHolder $rd
	 * @return string
	 */
	public function executeJson(AgaviRequestDataHolder $rd) {
		
		$source = AgaviConfig::get('de.icinga.web.cronks');

		$data = array ();
		
		foreach ($source as $name=>$meta) {
			if (array_key_exists('hide', $meta) && $meta['hide'] == true) continue;
			
			// Remove security related information from the 
			// array
			unset($meta['module']);
			unset($meta['action']);
			
			// The id
			$meta['id'] = $name;
			
			// Adding images
			if (!array_key_exists('image', $meta)) $meta['image'] = 'cronks.default';
			
			$meta['image'] = AppKitHtmlHelper::Obj()->imageUrl($meta['image']);
			
			// Add the safe data to stack
			$data[] = $meta;
		}
		
		// Return as json!
		return json_encode(array('cronks' => $data));
		
	}
}

?>