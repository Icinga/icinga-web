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
 * Dump files to string minifying content on request
 *
 * Either construct with parameter array, e.g.
 * array(
 *     'newlines' => false,
 *     'indent' => false,
 *     'comments' => false
 * )
 * or call setParameter, e.g.
 * $loader->setParameter('newlines', false);
 *
 * @author Eric Lippmann <eric.lippmann@netways.de>
 * @since 1.5.0
 */
class AppKit_BulkLoaderModel extends AppKitBaseModel implements AgaviISingletonModel {

    /**
     * Set file
     * @paramter string filename
     * @return this
     *
     * @author Eric Lippmann <eric.lippmann@netways.de>
     * @since 1.5.0
     */
    public function setFile($file) {
        $this->appendParameter('files', $file);

        return $this;
    }

    /**
     * Set files
     * @paramter array string filenames
     * @return this
     *
     * @author Eric Lippmann <eric.lippmann@netways.de>
     * @since 1.5.0
     */
    public function setFiles(array $files) {
        $this->setParameter(
            'files',
            array_merge(
                $this->getParameter('files', array()),
                $files
            )
        );

        return $this;
    }

    /**
     * Validate readibility of set files
     * @return this
     *
     * @author Eric Lippmann <eric.lippmann@netways.de>
     * @since 1.5.0
     */
    protected function checkFiles() {
        $files = array();

        foreach($this->getParameter('files') as $file) {
            if (false !== ($rfile = realpath($file)) && @is_readable($file)) {
                $files[] = $rfile;
            } else {
                AppKitAgaviUtil::log(
                        '%s: File %s not readable.',
                        get_class($this),
                        $file,
                        AgaviLogger::ERROR
                );
            }
        }

        $this->setParameter('files', $files);

        return $this;
    }

    /**
     * Dump files minifying content on request
     * @return this
     *
     * @author Eric Lippmann <eric.lippmann@netways.de>
     * @since 1.5.0
     */
    protected function readFiles() {
        $content = null;

        foreach($this->getParameter('files') as $file) {
            if (false !== ($fcontent = @file_get_contents($file))) {
                if (!$this->getParameter('comments', true)) {
                    $fcontent = preg_replace("!^\s*//.*$!m", "\n", $fcontent);
                    $fcontent = preg_replace("!^\s*/\*.*?\*/!s", "", $fcontent);
                }

                if (!$this->getParameter('indent', true)) {
                    $fcontent = implode("\n", array_map('trim', explode("\n", $fcontent)));
                }

                if (!$this->getParameter('newlines', true)) {
                    $fcontent = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $fcontent);
                }

                $content .= $fcontent . "\n";
            } else {
                AppKitAgaviUtil::log(sprintf(
                                         '%s: Could not get contents of file %s.',
                                         get_class($this),
                                         $file
                                     ), AgaviLogger::ERROR);
            }
        }

        $this->setParameter('content', $content);

        return $this;
    }

    /**
     * Get content of files
     * @return string content
     *
     * @author Eric Lippmann <eric.lippmann@netways.de>
     * @since 1.5.0
     */
    public function getContent() {
        $this->checkFiles();
        $this->readFiles();

        return $this->getParameter('content');
    }

}
