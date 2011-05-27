<?php

class Cronks_Provider_CombinedSuccessView extends CronksBaseView {
    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);

        $this->setAttribute('_title', 'Provider.Combined');
    }

    public function executeJson(AgaviRequestDataHolder $rd) {
        if($this->hasAttribute('data')) {
            return json_encode($this->getAttribute('data', new stdClass()));
        }

        return json_encode(new stdClass());
    }
}

?>