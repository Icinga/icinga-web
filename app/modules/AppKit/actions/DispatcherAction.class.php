<?php

class AppKit_DispatcherAction extends AppKitBaseAction {
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
    public function getDefaultViewName() {
        return 'Success';
    }

    public function executeRead(AgaviRequestDataHolder $r) {
        $this->executeWrite($r);
        return $this->getDefaultViewName();
    }

    public function executeWrite(AgaviRequestDataHolder $rd) {
        $module = $rd->getParameter("module");
        $action = $rd->getParameter("action");
        $output_type = $rd->getParameter("output_type","json");
        $dispatchParams = json_decode($rd->getParameter("params"), 1);
        $params = new AgaviRequestDataHolder();

        if (is_array($dispatchParams)) {
            foreach($dispatchParams as $key=>$param) {
                if ($param != null && $param != 'null') {
                    $params->setParameter($key,$param);
                }
            }
        }
        
        $controller = $this->getContext()->getController();
        $actionInstance = $controller->createActionInstance($module,$action);

        if (!($actionInstance instanceof IAppKitDispatchableAction)) {
            $this->setAttribute("error",$module.".".$action." is not accessible via the dispatcher");
            return "Error";
        }

        $this->setAttribute("execContainer",$controller->createExecutionContainer($module,$action,$params,$output_type));
        return $this->getDefaultViewName();
    }

    public function isSecure() {
        return true;
    }
}