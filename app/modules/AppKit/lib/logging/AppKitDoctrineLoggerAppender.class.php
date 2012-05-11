<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2012 Icinga Developer Team.
// All rights reserved.
// 
// icinga-web is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
// 
// icinga-web is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with icinga-web.  If not, see <http://www.gnu.org/licenses/>.
// -----------------------------------------------------------------------------
// {{{ICINGA_LICENSE_CODE}}}


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
        $NsmLog->log_level      = $message->getParameter('level');
        $NsmLog->log_message    = $this->getLayout()->format($message);
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