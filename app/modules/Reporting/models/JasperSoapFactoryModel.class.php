<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2015 Icinga Developer Team.
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


class Reporting_JasperSoapFactoryModel extends JasperConfigBaseModel implements AgaviISingletonModel {

    const SERVICE_SCHEDULER        = 'ReportScheduler';
    const SERVICE_PERMISSIONS      = 'PermissionsManagementService';
    const SERVICE_USER             = 'UserAndRoleManagementService';
    const SERVICE_REPOSITORY       = 'repository';
    const SERVICE_ADMIN            = 'AdminService';
    const SERVICE_VERSION          = 'Version';

    private $clients               = array();

    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);
    }

    protected function wrapWsdl($service_name) {
        return sprintf('%s/services/%s?wsdl', $this->getParameter('jasper_url'), $service_name);
    }

    /**
     * Creates a configured SOAP client
     * @param string $wsdl
     * @param array $additional_options
     * @internal param string $url
     * @return SoapClient
     */
    protected function getSoapClient($wsdl, array $additional_options=array()) {
        if (!isset($this->clients[$wsdl]) || !$this->clients[$wsdl] instanceof SoapClient) {
            
            $this->testWsdl($wsdl);
            
            $options = array(
                           'cache_wsdl'    => WSDL_CACHE_NONE,
                           'trace'         => true,
                           'exceptions'    => true
                       );

            if ($this->getParameter('jasper_user') !== null) {
                $options['login'] = $this->getParameter('jasper_user');
            }

            if ($this->getParameter('jasper_pass') !== null) {
                $options['password'] = $this->getParameter('jasper_pass');
            }
            
            $this->clients[$wsdl] = new JasperSoapMultipartClient($wsdl, $options);
        }

        return $this->clients[$wsdl];
    }
    
    /**
     * Tests the SOCKET connection to jasper
     * (This is small hack because I can not catch/supress constructor
     * created PHP FATAL ERRORS (which occurs as AppKit Exceptions))
     * @param string $wsdl
     * @throws Reporting_JasperSoapFactoryModelExceltion
     * @return boolean true on success
     */
    protected function testWsdl($wsdl) {
        // Test if sockets available and log error that
        // we can not check availability -> LOG
        // #3694
        if (extension_loaded('sockets2') === false) {
            $this->getContext()->getLoggerManager()->log(
                'Reporting/JasperSoapFactory: Can not detect sockets, assume '
                . ' configuration is correct: '. $wsdl,
                AgaviILogger::WARN
            );
            return false;
        }

        $parts = parse_url($wsdl);
        $sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        $test = @socket_connect($sock, $parts['host'], $parts['port']);
        socket_close($sock);
        if ($test) {
            return true;
        } else {
            throw new Reporting_JasperSoapFactoryModelExceltion(null, 'Could not connect to server');
        }
    }

    /**
     * Just a wrapper to get the configured client for a Jasper service name (class constants)
     * @param string $service_name
     * @return SoapClient
     */
    public function getSoapClientForWSDL($service_name) {
        return $this->getSoapClient($this->wrapWsdl($service_name));
    }

    /**
     * Checks if we can use the jasper server at the soap side
     * @return boolean true response
     */
    public function pingServer() {
        try {
            $client = $this->getSoapClientForWSDL(self::SERVICE_VERSION);
            $response = $client->getVersion();

        } catch (Exception $e) {
            $response = '';
        }

        return (preg_match('/^apache axis[^\d]+\d+\.\d+/i', $response)) ? true : false;
    }

}

class Reporting_JasperSoapFactoryModelExceltion extends SoapFault {}

?>