<?php
/**
 * @author Christian Doebler <christian.doebler@netways.de>
 */
class Cronks_System_StatusMapSuccessView extends CronksBaseView {
    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);

        $this->setAttribute('_title', 'System.StatusMap');
    }

    public function executeJson(AgaviRequestDataHolder $rd) {
        $connection = $rd->getParameter("connection","icinga");

        $model = $this->getContext()->getModel('System.StatusMap', 'Cronks',array(
            "connection"=> $connection
        ));

        $jsonData = $model->getParentChildStructure();

        return trim(json_encode($jsonData), '[]');
    }

}

?>