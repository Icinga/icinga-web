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


class Reporting_Report_GetReportDataSuccessView extends ReportingBaseView {

    /**
     * @var Reporting_ReportUserFileModel
     */
    private $__userFile = null;

    private function prepareReportingData(AgaviRequestDataHolder $rd) {
        $struct = $this->__userFile->getUserFileStruct();
        $fp = $this->__userFile->getFilePointer();

        if ($rd->getParameter('inline', null) !== 1) {
            $this->getResponse()->setHttpHeader(
                'Content-Disposition',
                sprintf('attachment; filename=%s', $struct['pushname'])
            );
        }

        $this->getResponse()->setContent($fp);
    }

    public function initialize(AgaviExecutionContainer $container) {
        parent::initialize($container);
        $this->__userFile = $this->getContext()->getModel('ReportUserFile', 'Reporting');
    }

    public function executeJson(AgaviRequestDataHolder $rd) {
        try {
            $struct = $this->__userFile->getUserFileStruct();
            return $this->createForwardContainer('Reporting', 'Report.GetReportData', null, $struct['format']);
        } catch (AppKitModelException $e) {
            return json_encode(array(
                                   'success' => false,
                                   'errors' => array('exception' => $e->getMessage())
                               ));
        }
    }

    public function executePdf(AgaviRequestDataHolder $rd) {
        return $this->prepareReportingData($rd);
    }

    public function executeCsv(AgaviRequestDataHolder $rd) {
        $this->prepareReportingData($rd);
    }

    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->prepareReportingData($rd);
    }

    public function executeXls(AgaviRequestDataHolder $rd) {
        $this->prepareReportingData($rd);
    }

    public function executeRtf(AgaviRequestDataHolder $rd) {
        $this->prepareReportingData($rd);
    }

    public function executeXml(AgaviRequestDataHolder $rd) {
        $this->prepareReportingData($rd);
    }
}

?>