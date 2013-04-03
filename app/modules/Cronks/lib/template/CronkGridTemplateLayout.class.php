<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2013 Icinga Developer Team.
// All rights reserved.
// 
// icinga-web is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
// 
// icinga-web is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with icinga-web.  If not, see <http://www.gnu.org/licenses/>.
// -----------------------------------------------------------------------------
// {{{ICINGA_LICENSE_CODE}}}


class CronkGridTemplateLayout {

    /**
     * @var AgaviExecutionContainer
     */
    private $container = null;

    /**
     * @var CronkGridTemplateWorker
     */
    private $worker = null;

    /**
     * @var AgaviParameterHolder
     */
    private $params = null;

    public function __construct(AgaviExecutionContainer  $agaviContainer = null) {
        if ($agaviContainer !== null) {
            $this->setContainer($agaviContainer);
        }

        $this->initClass();
    }

    public function setContainer(AgaviExecutionContainer $container) {
        $this->container =& $container;
    }

    /**
     * Returns the agavi execution container
     * @return AgaviExecutionContainer
     */
    protected function getContainer() {
        return $this->container;
    }

    public function setWorker(CronkGridTemplateWorker $worker) {
        $this->worker =& $worker;
    }

    /**
     * Returns the template worker engine
     * @return CronkGridTemplateWorker
     */
    protected function getWorker() {
        return $this->worker;
    }

    public function setParameters(AgaviParameterHolder $rd) {
        $this->params =& $rd;
    }

    /**
     * Returns the request params
     * @return AgaviParameterHolder
     */
    protected function getParameters() {
        return $this->params;
    }

    protected function initClass() {

    }

    public function getLayoutContent() {
        return 'NOT IMPLEMENTED';
    }

    public function createExecutionContainer($module, $action, AgaviRequestDataHolder  &$rd) {
        return $this->getContainer()->createExecutionContainer(
                   $module, $action, $rd
               );
    }
}

class CronkGridTemplateLayoutException extends AppKitException { }

?>
