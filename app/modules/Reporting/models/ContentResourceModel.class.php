<?php

class Reporting_ContentResourceModel extends JasperConfigBaseModel {
    
    /**
     * @var Reporting_JasperSoapFactoryModel
     */
    private $__client = null;
    
    private $__uri = null;
    
    /**
     * @var JasperSoapMultipartClient
     */
    private $__soap = null;
    
    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);
        
        $this->__client = $this->getParameter('client');
        
        $this->__uri = $this->getParameter('uri', null);
        
        if (!$this->__client instanceof Reporting_JasperSoapFactoryModel) {
            throw new AppKitModelException('Client must be instance of Reporting_JasperSoapFactoryModel');
        }
        
        if ($this->__uri === null) {
            throw new AppKitModelException('Model must have uri parameter');
        }
    }
    
    /**
     * Fire the request and return the result. Also do a basic security 
     * checking against the configured root path
     * @throws AgaviSecurityException
     * @return JasperSoapMultipartClient
     */
    public function doJasperRequest() {
        if ($this->checkUri($this->__uri) == false) {
            throw new AgaviSecurityException('You are not allowed to access '. $this->__uri);
        }
        
        $this->__soap = $this->__client->getSoapClientForWSDL(Reporting_JasperSoapFactoryModel::SERVICE_REPOSITORY);
        
        $request = new JasperRequestXmlDoc('get');
        $request->setResourceDescriptor(JasperRequestXmlDoc::RES_URI, $this->__uri);
        
        
        $this->__soap->doRequest($request);
        
        return $this->__soap;
    }
    
    public function getMetaData() {
        $response = $this->__soap->getJasperResponseFor(JasperSoapMultipartClient::CID_RESPONSE);
        
        if (count($response) == 1) {
            $rd = $response->current();
            
            $struct = array (
                'label' => $rd->getLabel(),
                'crdate' => $this->context->getTranslationManager()->_d($rd->getCrdate(), 'date-tstamp'),
                'has_attachment' => $this->__soap->hasAttachment(),
                'has_report' => $this->__soap->hasReport()
            );
            
            $struct = $rd->getResourceDescriptor()->getParameters() + $struct;
            $struct = $rd->getProperties()->getParameters() + $struct;
            
            if ($struct['has_attachment']) {
                
                $mime = AppKitFileUtil::getMimeTypeForData(
                    $this->__soap->getDataFor(JasperSoapMultipartClient::CID_ATTACHMENT),
                    $this->__soap->getHeaderFor(JasperSoapMultipartClient::CID_ATTACHMENT, 'content-type')
                );
                
                $struct = array (
                    'content_type' => $mime,
                    'content_length' => $this->__soap->getContentSize(JasperSoapMultipartClient::CID_ATTACHMENT),
                    'preview_allowed' => $this->canPreview($mime),
                    'download_allowed' => $this->canDownload($rd->getProperties()->getParameter('PROP_RESOURCE_TYPE'))
                ) + $struct;
            }
            
            return $struct;
        }
    }
    
    public function getContent() {
        return $this->__soap->getDataFor(JasperSoapMultipartClient::CID_ATTACHMENT);
    }
    
    private function canPreview($mime) {
        $re = AgaviConfig::get('modules.reporting.preview.mime_regex');
        return (boolean)preg_match($re, $mime);
    }
    
    private function canDownload($jasper_type) {
        $arry = AgaviConfig::get('modules.reporting.preview.jasper_resources');
        return (boolean)in_array($jasper_type, $arry);
    }

}

?>