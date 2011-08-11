<?php

/**
 * Write agavi logs into icinga doctrine database
 * @author mhein
 *
 */
class AppKitDoctrineLoggerAppender extends AgaviLoggerAppender {

    /**
     * (non-PHPdoc)
     * @see AgaviLoggerAppender::initialize()
     */
    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);
    }

    /**
     * (non-PHPdoc)
     * @see AgaviLoggerAppender::write()
     */
    public function write(AgaviLoggerMessage $message) {

        if (($layout = $this->getLayout()) === null) {
            throw new AgaviLoggingException('No Layout set');
        }

        $NsmLog = new NsmLog();
        $NsmLog->log_level		= $message->getParameter('level');
        $NsmLog->log_message	= $this->getLayout()->format($message);
        $NsmLog->save();

    }

    /**
     * (non-PHPdoc)
     * @see AgaviLoggerAppender::shutdown()
     */
    public function shutdown() {
        // Do nothing here ... ;-)
    }
}