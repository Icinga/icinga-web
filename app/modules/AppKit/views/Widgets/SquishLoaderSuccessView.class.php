<?php

class AppKit_Widgets_SquishLoaderSuccessView extends AppKitBaseView {
    public function executeJavascript(AgaviRequestDataHolder $rd) {

        if($this->getAttribute('errors', false)) {
            return "throw '". join(", ", $this->getAttribute('errors')). "';";
        } else {
            $content = $this->getAttribute('javascript_content');
            $content .= 'AppKit.util.Config.add(\'path\', \''. AgaviConfig::get('org.icinga.appkit.web_path'). '\');'. chr(10);
            $content .= 'AppKit.util.Config.add(\'image_path\', \''. AgaviConfig::get('org.icinga.appkit.image_path'). '\');'. chr(10);

            $content .= $this->executeActions(
                            $this->getAttribute('javascript_actions')
                        );

            return $content;
        }
    }

    private function executeActions(array $actions = array()) {
        $out = null;

        if(count($actions)==1 && isset($actions[0])) {

            foreach($actions[0] as $modules) {

                foreach($modules as $a) {
                    $p = array();

                    if(!isset($a['arguments'])) {
                        $a['arguments'] = false;
                    }

                    if(is_array($a['arguments'])) {
                        $p = $a['arguments'];
                    }

                    $a['arguments']['is_slot'] = true;
                    $r = $this->createForwardContainer($a['module'], $a['action'], $p, $a['output_type'])
                         ->execute();

                    if($r->hasContent()) {
                        $out .= $r->getContent(). "\n\n";
                    }
                }

            }

        }

        return $out;
    }
}

?>
