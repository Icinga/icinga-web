<?php

class CronkGridTemplateAjaxGridLayout extends CronkGridTemplateLayout {
    const LAYOUT_ACTION = 'System.ViewProc.AjaxGridLayout';
    const LAYOUT_MODULE = 'Cronks';

    public function getLayoutContent() {
        $rd = $this->getParameters();
        return $this->createExecutionContainer(self::LAYOUT_MODULE, self::LAYOUT_ACTION, $rd);
    }
}

?>