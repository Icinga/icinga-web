<?php

/**
 * The base view from which all AppKit module views inherit.
 */
class AppKitBaseView extends IcingaBaseView {
	public function  initialize(AgaviExecutionContainer $container) {
		parent::initialize($container);
		AppKitModuleUtil::getInstance()->applyToRequestAttributes($this->getContainer());

		// var_dump($this->getContainer()->getContext()->getRequest()->getAttributes('org.icinga.global'));

	}
}

?>
