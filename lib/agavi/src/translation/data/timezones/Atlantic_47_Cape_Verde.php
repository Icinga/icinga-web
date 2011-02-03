<?php

/**
 * Data file for timezone "Atlantic/Cape_Verde".
 * Compiled from olson file "africa", version 8.28.
 *
 * @package    agavi
 * @subpackage translation
 *
 * @copyright  Authors
 * @copyright  The Agavi Project
 *
 * @since      0.11.0
 *
 * @version    $Id: Atlantic_47_Cape_Verde.php 4577 2010-08-20 18:56:31Z david $
 */

return array (
  'types' => 
  array (
    0 => 
    array (
      'rawOffset' => -7200,
      'dstOffset' => 0,
      'name' => 'CVT',
    ),
    1 => 
    array (
      'rawOffset' => -7200,
      'dstOffset' => 3600,
      'name' => 'CVST',
    ),
    2 => 
    array (
      'rawOffset' => -3600,
      'dstOffset' => 0,
      'name' => 'CVT',
    ),
  ),
  'rules' => 
  array (
    0 => 
    array (
      'time' => -1988144756,
      'type' => 0,
    ),
    1 => 
    array (
      'time' => -862610400,
      'type' => 1,
    ),
    2 => 
    array (
      'time' => -764118000,
      'type' => 0,
    ),
    3 => 
    array (
      'time' => 186120000,
      'type' => 2,
    ),
  ),
  'finalRule' => 
  array (
    'type' => 'static',
    'name' => 'CVT',
    'offset' => -3600,
    'startYear' => 1976,
  ),
  'source' => 'africa',
  'version' => '8.28',
  'name' => 'Atlantic/Cape_Verde',
);

?>