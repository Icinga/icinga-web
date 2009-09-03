<?php

class Web_Icinga_Cronks_ObjectSearchSuccessView extends ICINGAWebBaseView
{
	
	/**
	 * Web_Icinga_Cronks_ObjectSearchResultModel
	 * @var Web_Icinga_Cronks_ObjectSearchResultModel
	 */
	private $model = null; 
	
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'Icinga.Cronks.ObjectSearch');
	}
	
	public function executeJson(AgaviRequestDataHolder $rd) {
		
		$this->model = $this->getContext()->getModel('Icinga.Cronks.ObjectSearchResult', 'Web');
		$this->model->setQuery($rd->getParameter('q'));
		$data = $this->model->getData();
		
		return json_encode($data);

	}
}

?>