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

/**
 * @author Christian Doebler <christian.doebler@netways.de>
 */
class Web_Icinga_ApiSimpleDataProviderSuccessView extends IcingaWebBaseView {
    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);

        $this->setAttribute('_title', 'IcingaApiSimpleDataProvider');
    }
    
    public function executeJson(AgaviRequestDataHolder $rd) {

        // init
        $jsonData = array(
                        'result'    => array(
                            'count' => 0,
                            'data'  => array(),
                        ),
                    );
        $model = $this->getContext()->getModel('Icinga.ApiSimpleDataProvider', 'Web');

        $srcId = $rd->getParameter('src_id');
        $filter = $rd->getParameter('filter');

        $result = $model->setSourceId($srcId)->setFilter($filter)->fetch();

        $jsonData['result']['data'] = $result;

        // store final count and convert
        $jsonData['result']['count'] = count($jsonData['result']['data']);

        if (($template = $model->getTemplateCode()) !== false) {
            $jsonData['result']['template'] = $template;
        }
        
        AppKitArrayUtil::toUTF8_recursive($jsonData);
        $jsonDataEnc = json_encode($jsonData);

        return $jsonDataEnc;

    }

}

?>