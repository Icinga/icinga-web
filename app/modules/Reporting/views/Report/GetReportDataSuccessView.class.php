<?php

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