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
 * Util collection for working with files
 * @author mhein
 *
 */
final class AppKitFileUtil {

    /**
     * Tries to find a file with different suffixes
     * @param string $directory
     * @param string $basename
     * @param string $extension
     * @param array $suffixes
     * @throws AppKitFileUtilException
     */
    public static function getAlternateFilename($directory, $basename, $extension, array $suffixes=array('.site')) {
        $suffixes[] = '';
        foreach($suffixes as $suffix) {
            try {
                $filename = $directory. '/'. $basename. $suffix. $extension;
                self::fileExists($filename);
                return new SplFileObject($filename);
            } catch (AppKitFileUtilException $e) {}

        }
        throw new AppKitFileUtilException('Could not find any alternatives for '. $basename);
    }

    /**
     * Returns true if a file exists
     * @param $filename
     * @throws AppKitFileUtilException
     */
    public static function fileExists($filename) {
        if (is_file($filename)) {
            return true;
        }

        throw new AppKitFileUtilException('File %s does not exist!', $filename);
    }

    /**
     * Returns the mime type for content of a string
     * @param mixed $data
     * @param string $default
     * @return string the mime type or default
     */
    public static function getMimeTypeForData($data, $default = null) {
        if (class_exists('finfo', false)) {
            $finfo = new finfo(FILEINFO_MIME);
            return $finfo->buffer($data);
        }

        return $default;
    }

    /**
     * Recursive deletion of a directory with all content
     * @param string $directory
     */
    public static function rmdir($directory) {
        if (is_dir($directory)) {
            $content = scandir($directory);
            foreach($content as $item) {
                if ($item !== '.' && $item !== '..') {
                    $newitem = $directory. DIRECTORY_SEPARATOR. $item;

                    if (filetype($newitem) == 'dir') {
                        self::rmdir($newitem);
                    } else {
                        unlink($newitem);
                    }
                }
            }
        }
    }


}

class AppKitFileUtilException extends AppKitException {}
