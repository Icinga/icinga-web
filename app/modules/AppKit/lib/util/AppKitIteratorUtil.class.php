<?php

/**
 * Iterator factory
 *
 * @author Eric Lippmann <eric.lippmann@netways.de>
 * @since 1.5.0
 */
final class AppKitIteratorUtil {

    /**
     * Create a new RegexRecursiveDirectoryIterator
     *
     * @param string directory
     * @param regex
     * @param int mode[optional]
     *
     * @return RegexIterator
     *
     * @author Eric Lippmann <eric.lippmann@netways.de>
     * @since 1.5.0
     */
    public static function RegexRecursiveDirectoryIterator($dir, $re, $mode=RecursiveRegexIterator::MATCH) {
        return new RegexIterator(
                   new RecursiveIteratorIterator(
                       new RecursiveDirectoryIterator($dir)
                   ),
                   $re,
                   $mode
               );
    }

}
