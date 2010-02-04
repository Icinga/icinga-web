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
		
		$type = $rd->getParameter('type', 'cronks');
		$catdata = AgaviConfig::get('de.icinga.web.cronk.categories');
		$out = array ();
		
		switch ($type) {
			
			// Returning only the categories
			case 'cat':
				$out['categories'] = array ();
				foreach ($catdata as $k=>$v) {
					if (!isset($v['visible']) || $v['visible'] == false) continue;
					$out['categories'][ $k ] = $v;
				}
			break;
			
			// Return the cronks by a category
			case 'cronks':
			default:

				$source = AgaviConfig::get('de.icinga.web.cronks');
				$cat = $rd->getParameter('cat', null);
				$user = $this->getContext()->getUser();
				
				if (!$cat || !isset($catdata[$cat])) {
					throw new AgaviViewException('A valid cronk category is needed!');
				}

				$category = $catdata[$cat];
				
				$data = array ();
				
				if (!isset($category['visible']) || $category['visible'] == true) {
					foreach ($source as $name=>$meta) {
						
						// Cronk is not visible
						if (isset($meta['meta']) && $meta['hide'] == true) continue;
						
						// Not in category
						$ccategories = split(',', $meta['categories']);
						if (in_array($cat, $ccategories) == false) continue;
						
						// Check group
						if (isset($meta['groupsonly'])) {
							$groups = split(',', $meta['groupsonly']);
							$check = false;
							foreach ($groups as $group) {
								
								if ($user->hasRole($group)) {
									$check = true;
									break;
								}
								
							}
							
							if ($check == false) {
								continue;
							}
						}
						
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
				}
				
				$out['cronks'] = $data;
				
			break;
		}
		
		// Return as json!
		return json_encode($out);
		
	}
}

?>