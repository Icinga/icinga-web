<?php

class Cronks_System_CronkListingSuccessView extends CronksBaseView
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
		$cat = $rd->getParameter('cat', null);
		
		$model = $this->getContext()->getModel('System.CronkData', 'Cronks', array('filter' => 'list', 'sort' => true));
		
		$categories = $model->getCategories();
		
		$out = array (
			'cat'		=> array (
				'resultCount'	=> count($categories),
				'resultRow'		=> $categories,
				'resultSuccess'	=> true
			),
			
			'cronks'	=> array ()
		);
		
		if ($type == 'cat') {
			unset($out['cronks']);
			return json_encode($out);
		}
		
		
		foreach ($categories as $catkey=>$catmeta) {
			$cronks = $model->getCronksByCategory($catkey, true, 'id');
			$out['cronks'][$catkey] = array (
				'resultCount'	=> count($cronks),
				'resultRow'		=> $cronks,
				'resultSuccess'	=> true
			);
		}
		
		
		if ($cat !== null) {
			$out['cronks'] = $out['cronks'][$cat];
			unset($out['cat']);
		}
		
		return json_encode($out);
	}
}

?>
