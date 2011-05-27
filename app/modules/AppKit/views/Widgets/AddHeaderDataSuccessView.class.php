<?php

class AppKit_Widgets_AddHeaderDataSuccessView extends AppKitBaseView {
    public function executeHtml(AgaviRequestDataHolder $rd) {

        $this->setupHtml($rd);

        $this->setAttribute('web_path', AgaviConfig::get('org.icinga.appkit.web_path'));

        AppKitEventDispatcher::getInstance()->triggerSimpleEvent(
            'appkit.headerdata.publish',
            'Last change to add some header data',
            $this->getContext()
        );
        //
        //		$this->setAttribute('css_files',
        //			$this->getContainer()->getAttribute('app.css_files', AppKitModuleUtil::CONFIG_NAMESPACE)
        //		);

        $header = $this->getContext()->getModel('HeaderData', 'AppKit');

        $this->setAttribute('css_raw', $header->getCssData());

        // $this->setAttribute('js_files', $header->getJsFiles());
        // $this->setAttribute('js_raw', $header->getJsData());

        // $this->setAttribute('meta_tags', $header->getMetaTags());

    }
}

?>