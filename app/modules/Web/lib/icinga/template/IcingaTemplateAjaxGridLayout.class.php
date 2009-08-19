<?php

class IcingaTemplateAjaxGridLayout extends IcingaTemplateLayout {
	const LAYOUT_ACTION = 'Icinga.Cronks.ViewProc.AjaxGridLayout';
	const LAYOUT_MODULE = 'Web';
	
	public function getLayoutContent() {
		$rd =& $this->getParameters();
		return $this->createExecutionContainer(self::LAYOUT_MODULE, self::LAYOUT_ACTION, $rd);
	}
}

?>