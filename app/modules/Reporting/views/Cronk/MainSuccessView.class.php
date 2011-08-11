<?php

class Reporting_Cronk_MainSuccessView extends ReportingBaseView {
    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);

        $this->setAttribute('_title', 'Cronk.Main');
    }
}

?>