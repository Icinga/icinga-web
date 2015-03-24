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


class Reporting_Provider_ContentResourceSuccessView extends ReportingBaseView {
    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);

        $this->setAttribute('_title', 'Provider.ContentResource');
    }

    public function executeJson(AgaviRequestDataHolder $rd) {

        $client = $this->getContext()->getModel('JasperSoapFactory', 'Reporting', array(
                'jasperconfig' => $rd->getParameter('jasperconfig')
                                                ));

        $resource = $this->getContext()->getModel('ContentResource', 'Reporting', array(
                        'jasperconfig' => $rd->getParameter('jasperconfig'),
                        'client' => $client,
                        'uri' => $rd->getParameter('uri')
                    ));

        $resource->doJasperRequest();

        return json_encode(array(
                               'success' => true,
                               'count' => count(($data=$resource->getMetaData())),
                               'data' => $data
                           ));
    }

    public function executeSimple(AgaviRequestDataHolder $rd) {

        $client = $this->getContext()->getModel('JasperSoapFactory', 'Reporting', array(
                'jasperconfig' => $rd->getParameter('jasperconfig')
                                                ));

        $resource = $this->getContext()->getModel('ContentResource', 'Reporting', array(
                        'jasperconfig' => $rd->getParameter('jasperconfig'),
                        'client' => $client,
                        'uri' => $rd->getParameter('uri')
                    ));

        $resource->doJasperRequest();

        $m = $data=$resource->getMetaData();

        if ($m['has_attachment'] && $m['download_allowed']) {
            $this->getResponse()->setHttpHeader('content-length', $m['content_length'], true);
            $this->getResponse()->setHttpHeader('content-type', $m['content_type'], true);

            $content_disposition = sprintf(
                                       '%s; filename=%s',
                                       $rd->getParameter('inline') ? 'inline' : 'attachment',
                                       $m['name']
                                   );

            $this->getResponse()->setHttpHeader('content-disposition', $content_disposition, true);

            return $resource->getContent();
        }

        return null;
    }
}

?>