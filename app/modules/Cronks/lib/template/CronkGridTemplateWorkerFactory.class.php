<?php

/**
 * Factory that creates appropriate tempalte workers for cronk templates.
 *
 * This is mainly used to allow old and new template versions being used together
 *
 * @author jmosshammer
 */
class CronkGridTemplateWorkerFactory {

    static public function createWorker(CronkGridTemplateXmlParser $template, AgaviContext $context, $connection = "icinga") {
        $sections = array_flip($template->getSections());
        
        if(isset($sections["type"]) && class_exists($template->getSection("type")."CronkTemplateWorker")) {
            $class = $template->getSection("type")."CronkTemplateWorker";
            return new $class($template,$context, $connection);
        }
        return new GenericCronkTemplateWorker($template, $context);

        
    }
}
