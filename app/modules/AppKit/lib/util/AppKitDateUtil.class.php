<?php

/**
 * Class to do some magick things with dates
 * @author mhein
 *
 */
class AppKitDateUtil {

    /**
     * Calculates the duration up to now from an iso date string
     * @param string $iso_date
     * @param integer $reference_stamp
     * @return integer
     */
    public static function dateToDuration($iso_date, $reference_stamp = null) {

        if(($tstamp = strtotime($iso_date)) < 0) {
            throw new AppKitDateUtilException('$iso_date is not in iso format');
        }

        if($reference_stamp === null) {
            $reference_stamp = time();
        }

        return (int)($reference_stamp - $tstamp);
    }

    /**
     * Display integer seconds as durations
     * @param integer $seconds
     * @return string
     */
    public static function durationToString($seconds) {
        static $frames = array(
                             'd'		=> 86400,
                             'h'		=> 3600,
                             'm'		=> 60,
                             's'		=> 1
                         );

        foreach($frames as $name=>$mod) {
            if($seconds >= $mod && ($rest = $seconds%$mod)>0) {
                $parts[] = floor($seconds/$mod). $name;
                $seconds = $rest;
            }

            elseif(($rest = $seconds%$mod)==0) {
                $parts[] = floor($seconds/$mod). $name;
                break;
            }
            else {
                $parts[] = '0'. $name;
            }

        }

        return implode(' ', $parts);
    }

}

class AppKitDateUtilException extends AppKitException { }

?>