<?php

/**
 * Data file for timezone "Antarctica/Mawson".
 * Compiled from olson file "antarctica", version 8.7.
 *
 * @package    agavi
 * @subpackage translation
 *
 * @copyright  Authors
 * @copyright  The Agavi Project
 *
 * @since      0.11.0
 *
 * @version    $Id: Antarctica_47_Mawson.php 4357 2009-11-28 20:20:35Z david $
 */

return array (
  'types' => 
  array (
    0 => 
    array (
      'rawOffset' => 21600,
      'dstOffset' => 0,
      'name' => 'MAWT',
    ),
    1 => 
    array (
      'rawOffset' => 18000,
      'dstOffset' => 0,
      'name' => 'MAWT',
    ),
  ),
  'rules' => 
  array (
    0 => 
    array (
      'time' => -501206400,
      'type' => 0,
    ),
    1 => 
    array (
      'time' => 1255809600,
      'type' => 1,
    ),
  ),
  'finalRule' => 
  array (
    'type' => 'static',
    'name' => 'MAWT',
    'offset' => 18000,
    'startYear' => 2010,
  ),
  'source' => 'antarctica',
  'version' => '8.7',
  'name' => 'Antarctica/Mawson',
);

?>