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


class Reporting_ReportUserFileModel extends ReportingBaseModel implements AgaviISingletonModel {

    const STORE_NAME = 'org.icinga.reporting.userfile';
    const MAX_TIME = 300;

    private static $__arrayTestKeys = array(
                                          'filename', 'bytes', 'format', 'checksum',
                                          'reportname', 'pushname'
                                      );

    private $__extensionMap = array(
                                  'pdf' => 'pdf',
                                  'csv' => 'csv',
                                  'html' => 'html',
                                  'xml' => 'xml',
                                  'rtf' => 'rtf',
                                  'xls' => 'xls'
                              );

    private $__dir = null;

    /**
     * @var AppKitSecurityUser
     */
    private $__user = null;

    private function fileGarbageCollector() {
        
        $this->getContext()->getLoggerManager()->log('REPORTING: Running garbage collector for user files.', AgaviLogger::DEBUG);
        
        $count = 0;
        foreach (scandir($this->__dir) as $f) {
            if ($f !== '.' && $f !== '..' && is_file(($file = $this->__dir . '/'. $f))) {
                $diff = time() - filectime($file);
                if ($diff > self::MAX_TIME) {
                    if (unlink($file)) {
                        $count++;
                    } else {
                        throw new AppKitModelException('Could not delete report user file: '. $file);
                    }
                }
            }
        }
        
        if ($count>0) {
            $this->getContext()->getLoggerManager()->log('Deleted '. $count. ' reporting user files', AgaviLogger::INFO);
            return true;
        }

        return false;
    }

    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);

        $this->__dir = AgaviConfig::get('modules.reporting.dir.download');

        if (!is_dir($this->__dir)) {
            @mkdir($this->__dir);
        }

        if (!is_dir($this->__dir)) {
            $this->log('Reporting: Permission denied to create temp dir: "%s"', $this->__dir, AgaviLogger::FATAL);
            throw new AppKitModelException('Reporting: No permission, could not create dir: '. $this->__dir);
        } else {
            $this->fileGarbageCollector();
        }

        $this->__user = $this->getContext()->getUser();

        if (!$this->__user instanceof AgaviSecurityUser) {
            throw new AppKitModelException('Could not get user instance');
        }
    }

    public function getExtensionFromFormat($format) {
        if (array_key_exists($format, $this->__extensionMap)) {
            return $this->__extensionMap[$format];
        }

        throw new AppKitModelException('Extension for format '. $format. ' not found');
    }

    private function getNewFilename($extension) {
        $username = $this->__user->getNsmUser()->user_name;
        $md5 = md5($username. '-'. microtime(true). '-'. getmypid());
        return sprintf('%s_%s.%s', $username, $md5, $extension);
    }

    public function storeFile($data, $output_format, JasperResourceDescriptor $rd) {
        $extension = $this->getExtensionFromFormat($output_format);
        $filename = sprintf('%s/%s', $this->__dir, $this->getNewFilename($extension));
        $bytes = file_put_contents($filename, $data);

        if (!strlen($data) == $bytes) {
            throw new AppKitModelException('Bytes written different to source data');
        }

        $struct = array(
                      'filename'         => $filename,
                      'format'           => $output_format,
                      'bytes'            => $bytes,
                      'checksum'         => md5($data),
                      'reportname'       => $rd->getResourceDescriptor()->getParameter(JasperResourceDescriptor::DESCRIPTOR_ATTR_NAME),
                      'pushname'         => sprintf('%s.%s', $rd->getResourceDescriptor()->getParameter(JasperResourceDescriptor::DESCRIPTOR_ATTR_NAME), $extension)

                  );

        $this->getContext()->getStorage()->write(self::STORE_NAME, $struct);

        return true;
    }

    public function getUserFileStruct() {
        $struct = $this->getContext()->getStorage()->read(self::STORE_NAME);

        if (!is_array($struct)) {
            throw new AppKitModelException('Could not get struct');
        }

        foreach(self::$__arrayTestKeys as $testKey) {
            if (!array_key_exists($testKey, $struct)) {
                throw new AppKitModelException('User file missing information: '. $testKey);
            }
        }

        if (!is_file($struct['filename'])) {
            throw new AppKitModelException('User does not exists anymore');
        }

        return $struct;
    }

    public function getFilePointer() {
        $struct = $this->getUserFileStruct();

        if (!is_file($struct['filename'])) {
            throw new AppKitModelException('User does not exists anymore');
        }

        //         $contents = file_get_contents($struct['filename']);

        //         if (!md5($contents) == $struct['checksum']) {
        //             throw new AppKitModelException('Checksum does not match, abort!');
        //         }

        //         if ($delete_after_receive === true) {
        //             unlink($struct['filename']);
        //         }


        return fopen($struct['filename'], 'r');
    }
}

?>