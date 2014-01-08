<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-present Icinga Developer Team.
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


class Reporting_Provider_TreeLoaderSuccessView extends ReportingBaseView {
    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);
        $this->setAttribute('_title', 'Provider.TreeLoader');
    }

    public function executeJson(AgaviRequestDataHolder $rd) {

        $factory = $this->getContext()->getModel('JasperSoapFactory', 'Reporting', array(
                       'jasperconfig' => $rd->getParameter('jasperconfig')
                   ));
        
        try {
            $client = $factory->getSoapClientForWSDL(Reporting_JasperSoapFactoryModel::SERVICE_REPOSITORY);
        } catch(SoapFault $e) {
            return json_encode(array(
                'success' => false,
                'error' => $e->getMessage()
            ));
        }

        $params = array(
                      'client'    => $client,
                      'parentid'  => $rd->getParameter('node'),
                      'jasperconfig' => $rd->getParameter('jasperconfig')
                  );

        $filter_val = $rd->getParameter('filter', null);

        if ($filter_val) {
            $filter = $this->getContext()->getModel('JasperTreeFilter', 'Reporting');

            if ($filter_val == 'reports') {
                $filter->addFilter(Reporting_JasperTreeFilterModel::TYPE_DESCRIPTOR, JasperResourceDescriptor::DESCRIPTOR_ATTR_TYPE, '/^folder|reportunit$/i');
            }

            $params['filter'] = $filter;
        }

        $tree = $this->getContext()->getModel('JasperTreeStruct', 'Reporting', $params);

        return json_encode($tree->getJsonStructure());
    }


}

?>