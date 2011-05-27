<?php

class AppKitDoctrineLoggerAppender extends AgaviLoggerAppender {

    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);
    }

    public function write(AgaviLoggerMessage $message) {

        if(($layout = $this->getLayout()) === null) {
            throw new AgaviLoggingException('No Layout set');
        }

        $NsmLog = new NsmLog();
        $NsmLog->log_level		= $message->getParameter('level');
        $NsmLog->log_message	= $this->getLayout()->format($message);
        $NsmLog->save();

    }

    public function shutdown() {
        // Do nothing here ... ;-)
    }
}

class AppKitDoctrineLoggerAppenderException extends AppKitException {}

?>