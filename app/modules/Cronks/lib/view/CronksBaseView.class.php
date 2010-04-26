<?php

/**
 * The base view from which all Cronks module views inherit.
 */
class CronksBaseView extends IcingaBaseView
{
	/**
	 * Execute html within a slot layout only without 
	 * implementing a new layout method in the corresponding
	 * views
	 * 
	 * @param AgaviRequestDataHolder $rd
	 * @return mixed
	 */
	public function executeSimple(AgaviRequestDataHolder $rd) {
		$rd->setParameter('is_slot', true);
		return $this->executeHtml($rd);
	}
}

?>