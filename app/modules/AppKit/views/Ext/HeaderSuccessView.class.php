<?php

class AppKit_Ext_HeaderSuccessView extends AppKitBaseView {
    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);
        $this->setAttribute('json_menu_data', $this->jsonMenuData());
    }

    public function executeJavascript(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);
        $this->setAttribute('json_menu_data', $this->jsonMenuData());
    }

    private function jsonMenuData() {
        $model = $this->getContext()->getModel('NavigationContainer', 'AppKit');
        return $model->getJsonData();
    }

}

?>
