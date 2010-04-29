<?php

/** 
 * Interface for icingaCronJobs
 */
interface IcingaCronJobInterface { 
	public function __construct(array $params = array(), $verbose = false);
	public function execute();
}