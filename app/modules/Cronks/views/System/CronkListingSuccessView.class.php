<?php

class Cronks_System_CronkListingSuccessView extends CronksBaseView {
    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);

        $this->setAttribute('_title', 'Icinga.Cronks.CronkListing');
    }
}

?>
