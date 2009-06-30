<?php

class Web_IndexAction extends ICINGAWebBaseAction
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
	
	public function executeRead(AgaviParameterHolder $rd) {
		
		$model = $this->getContext()->getModel('ViewExtender', 'Web');
		
		$link = new IcingaViewExtenderLink(IcingaViewExtenderLink::VIEW_HOST, 'test');
		
		$link->setRoute('test.route');
		$link->addRouteArg('id', '@SERVICE_ID@');
		$link->setParameter(IcingaViewExtenderLink::PARAM_LINK_TYPE, IcingaViewExtenderLink::VALUE_LINK_TEXT);
		$link->setParameter(IcingaViewExtenderLink::PARAM_IMAGE, 'storage.my_icon');
		$link->setParameter(IcingaViewExtenderLink::PARAM_CAPTION, 'Testlink');
		
		$model->registerLink($link);
		
		$out = $model->rewriteType(Web_ViewExtenderModel::VIEW_HOST, array (
			'@SERVICE_ID@'	=> 123,
		));
		
		var_dump($out);
		
		return $this->getDefaultViewName();
		
	}
}

?>