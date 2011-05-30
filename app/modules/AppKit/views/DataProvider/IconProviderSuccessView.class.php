<?php

class AppKit_DataProvider_IconProviderSuccessView extends AppKitBaseView {
    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);

        $this->setAttribute('_title', 'DataProvider.IconProvider');
    }

    public function executeJson(AgaviRequestDataHolder $rd) {
        $success = true;

        $model = $this->getAttribute('filemodel');

        if (!$rd->getParameter('path') || !$model) {
            $success = false;
        }

        $out = array(
                   'success'	=> $success,
                   'total'		=> 0,
                   'rows'		=> array()
               );

        if ($success) {
            $out['total'] = $model->Count();
            $out['rows'] = $model->Files();
        }

        return json_encode($out);
    }
}

?>