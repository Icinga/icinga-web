<?php

class AppKit_Admin_ViewLogsSuccessView extends NETWAYSAppKitBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('title', 'AppKit logviewer');
		
		
		
		try {
			$logadmin = $this->getContext()->getModel('LogAdmin', 'AppKit');
			
			$log_query = $logadmin->getLogQuery();
			$pager = AppKitDoctrinePager::createNew($log_query, $rd->getParameter('page_offset'), 'appkit.admin.logs');
			
			$this->setAttribute('log_levelmap', $logadmin->getLoglevelMap());
			$this->setAttribute('log_collection', $log_query->execute());
			$this->setAttribute('log_pager', $pager);
		}
		catch (Exception $e) {
			$this->getMessageQueue()->enqueue(AppKitMessageQueueItem::Error($e->getMessage()));
		}
	}
}

?>