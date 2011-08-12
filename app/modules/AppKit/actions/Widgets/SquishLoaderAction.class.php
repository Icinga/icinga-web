<?php

class AppKit_Widgets_SquishLoaderAction extends AppKitBaseAction {

    public function getDefaultViewName() {
        return 'Success';
    }

    public function executeRead(AgaviRequestDataHolder $rd) {
        $ra = explode('.', array_pop(
                          $this->getContext()->getRequest()->getAttribute(
                              'matched_routes', 'org.agavi.routing'
                          )
                      ));

        $type = array_pop($ra);

        $loader = $this->getContext()->getModel(
                      'SquishFileContainer',
                      'AppKit',
                      array('type' => $type)
                  );

        $resources = $this->getContext()->getModel('Resources', 'AppKit');

        switch ($type) {
            case 'javascript':
                try {
                    $loader->addFiles(
                        $resources->getJavascriptFiles()
                    );
                    
                    $loader->setActions($resources->getJavascriptActions());

                } catch (AppKitModelException $e) {
                    $this->setAttribute('errors', $e->getMessage());
                }

                break;

            case 'css':
                try {
                    $loader->addFiles(
                        $resources->getCssFiles()
                    );
                } catch (AppKitModelException $e) {
                    $this->setAttribute('errors', $e->getMessage());
                }

                break;
        }

        $headers = $rd->getHeaders();
        $etag = rand();

        if (isset($headers['IF_NONE_MATCH'])) {
            $etag = str_replace('"',"",$headers['IF_NONE_MATCH']);
        }

        if (!$loader->squishContents($etag)) {
            $content = $loader->getContent();
            $this->setAttribute('content', $content. chr(10));
        } else {
            $this->setAttribute('existsOnClient',true);
        }

        $this->setAttribute('etag',$loader->getChecksum());

        return $this->getDefaultViewName();
    }




}
