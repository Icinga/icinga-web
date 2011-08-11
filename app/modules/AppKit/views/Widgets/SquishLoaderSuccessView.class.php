<?php

class AppKit_Widgets_SquishLoaderSuccessView extends AppKitBaseView {

    public function executeJavascript(AgaviRequestDataHolder $rd) {
        if ($this->getAttribute('errors', false)) {
            return "throw '". join(", ", $this->getAttribute('errors')). "';";
        } else {
            $content = $this->getAttribute('content');

            $content .= 'AppKit.util.Config.add(\'path\', \''. AgaviConfig::get('org.icinga.appkit.web_path'). '\');'. chr(10);
            $content .= 'AppKit.util.Config.add(\'image_path\', \''. AgaviConfig::get('org.icinga.appkit.image_path'). '\');'. chr(10);

            $content .= $this->executeActions(
                            $this->getAttribute('javascript_actions',array())
                        );


            $etag = $this->getAttribute("etag",rand());



            header('Cache-Control: private');
            header('Pragma: ');
            header('Expires: ');
            header('ETag: "'.$etag.'"');

            if ($this->getAttribute('existsOnClient',false)) {
                $this->getResponse()->setHttpStatusCode("304");
                return "";
            }

            return $content;
        }
    }

    public function executeCss(AgaviRequestDataHolder $rd) {
        if ($this->getAttribute('errors', false)) {
            return "throw '". join(", ", $this->getAttribute('errors')). "';";
        } else {
            $content = $this->getAttribute('content');

            return $content;
        }
    }

    private function executeActions(array $jactions = array()) {
        $out = null;

        foreach($jactions as $jaction) {

            if (!isset($jaction['arguments'])) {
                $jaction['arguments'] = array();
            }

            $r = $this->createForwardContainer($jaction['module'], $jaction['action'], $jaction['arguments'], $jaction['output_type'])
                 ->execute();

            if ($r->hasContent()) {
                $out .= $r->getContent(). str_repeat(chr(10), 2);
            }
        }

        return $out;
    }

}
