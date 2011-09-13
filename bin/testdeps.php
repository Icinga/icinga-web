#!/usr/bin/php
<?php

if (class_exists('IcingaWebDependencyTester')) {
	IcingaWebDependencyTester::doTest();
}

die('Something wired error occured, class for testing not found!');

exit (0);

class IcingaWebDependencyTester {

	private static $plan = array (
	
		array (
			'name'			=> 'core_phpversion',
			'description'	=> 'Test php version >= 5.2.3',
			'method'		=> 'tVersion',
			'args'			=> array('5.2.3', '>='),
			'required'		=> true,
		
			'header'		=> 'PHP (core) tests'
		),
		
		array (
			'name'			=> 'core_pear',
			'description'	=> 'Test for PEAR',
			'method'		=> 'tPear',
			'args'			=> array(),
			'required'		=> true
		),
		
		array (
			'name'			=> 'ext_xslt',
			'description'	=> 'Test php5-xsl',
			'method'		=> 'tExtension',
			'args'			=> array('xsl'),
			'required'		=> true,
		
			'header'		=> 'PHP extensions'
		),
		
		array (
			'name'			=> 'ext_ldap',
			'description'	=> 'Test php5-ldap',
			'method'		=> 'tExtension',
			'args'			=> array('ldap'),
			'required'		=> false
		),
		
		array (
			'name'			=> 'ext_pdo',
			'description'	=> 'Test php5-pdo',
			'method'		=> 'tExtension',
			'args'			=> array('pdo'),
			'required'		=> true
		),
		
		array (
			'name'			=> 'ext_dom',
			'description'	=> 'Test php5-dom',
			'method'		=> 'tExtension',
			'args'			=> array('dom'),
			'required'		=> true
		),
		
		array (
			'name'			=> 'ext_session',
			'description'	=> 'Test php5-session',
			'method'		=> 'tExtension',
			'args'			=> array('session'),
			'required'		=> true
		),
		
		array (
			'name'			=> 'ext_spl',
			'description'	=> 'Test php5-spl',
			'method'		=> 'tExtension',
			'args'			=> array('spl'),
			'required'		=> true
		),
		
		array (
			'name'			=> 'ext_pcre',
			'description'	=> 'Test php5-pcre',
			'method'		=> 'tExtension',
			'args'			=> array('pcre'),
			'required'		=> true
		),
		
		array (
			'name'			=> 'ext_tokenizer',
			'description'	=> 'Test php5-tokenizer',
			'method'		=> 'tExtension',
			'args'			=> array('tokenizer'),
			'required'		=> true
		),
		
		array (
			'name'			=> 'ext_libxml',
			'description'	=> 'Test php5-libxml',
			'method'		=> 'tExtension',
			'args'			=> array('libxml'),
			'required'		=> true
		),
		
		array (
			'name'			=> 'ext_reflection',
			'description'	=> 'Test php5-reflection',
			'method'		=> 'tExtension',
			'args'			=> array('reflection'),
			'required'		=> true
		),
		
		array (
			'name'			=> 'ext_gettext',
			'description'	=> 'Test php5-gettext',
			'method'		=> 'tExtension',
			'args'			=> array('gettext'),
			'required'		=> true
		),
		
		array (
			'name'			=> 'ext_pdo_mysql',
			'description'	=> 'Test php5-pdo-mysql',
			'method'		=> 'tExtension',
			'args'			=> array('pdo_mysql'),
			'required'		=> false,
		
			'header'		=> 'Optional pdo drivers'
		),
		
		array (
			'name'			=> 'ext_pdo_pgsql',
			'description'	=> 'Test php5-pdo-pgsql',
			'method'		=> 'tExtension',
			'args'			=> array('pdo_pgsql'),
			'required'		=> false
		),
		
		array (
			'name'			=> 'ext_soap',
			'description'	=> 'Test php5-soap',
			'method'		=> 'tExtension',
			'args'			=> array('soap'),
			'required'		=> false,
		
			'header'		=> 'Optional php extension'
		),
		
		array (
			'name'			=> 'ext_xmlrpc',
			'description'	=> 'Test php5-xmlrpc',
			'method'		=> 'tExtension',
			'args'			=> array('xmlrpc'),
			'required'		=> false
		),
		
		array (
			'name'			=> 'ext_iconv',
			'description'	=> 'Test php5-iconv',
			'method'		=> 'tExtension',
			'args'			=> array('iconv'),
			'required'		=> false
		),
		
		array (
			'name'			=> 'ext_iconv',
			'description'	=> 'Test php5-gd',
			'method'		=> 'tExtension',
			'args'			=> array('gd'),
			'required'		=> false
		),
		array (
			'name'			=> 'ext_ctype',
			'description'	=> 'Test php5-ctype',
			'method'		=> 'tExtension',
			'args'			=> array('ctype'),
			'required'		=> false
		),
		array (
			'name'			=> 'ext_json',
			'description'	=> 'Test php5-json',
			'method'		=> 'tExtension',
			'args'			=> array('json'),
			'required'		=> false
		),
		array (
			'name'			=> 'ext_hash',
			'description'	=> 'Test php5-hash',
			'method'		=> 'tExtension',
			'args'			=> array('hash'),
			'required'		=> false
		),
		array (
			'name'			=> 'ini_memory_limit',
			'description'	=> 'Test php.ini memory_limit',
			'method'		=> 'tIniSettings',
			'args'			=> array('memory_limit', '16777216', '>=', 'bytes'),
			'required'		=> false
		)
	);
	
	private static $tresult = array (
		'all'		=> 0,
		'ok'		=> 0,
		'fail'		=> 0,
		'req_ok'	=> 0,
		'req_count'	=> 0,
		'opt_ok'	=> 0,
		'opt_count'	=> 0,
	
		'pass'		=> true
	);
	
	private static $time_start = 0;
	
	private static $data = array ();
	
	public static function doTest() {
		
		self::$time_start = microtime(true);
		
		$tests = count(self::$plan);
		
		self::pout('Icinga-web dependencies (running %d test)', $tests);
		self::pnl(1);
		
		$ref = new ReflectionClass(__CLASS__);
		
		foreach (self::$plan as $test) {
			
			if (array_key_exists('header', $test)) {
				self::pnl();
				self::pout($test['header']);
				self::pnl();
			}
			
			$name = $test['name'];
			
			self::pout('%s%d/%d %s%s', chr(9), self::$tresult['all']+1, $tests, $test['description'], $test['required'] ? ' (REQUIRED)' : null);
			self::pspace();
			
			$re = false;
			
			if ($ref->hasMethod($test['method'])) {
				$a = is_array($test['args']) ? $test['args'] : array();
				array_splice($a, 0, 0, $name);
				
				$m = $ref->getMethod($test['method']);
				
				$re = $m->invokeArgs(null, $a);			
			}
			
			if (($data = self::getTestData($name))) {
				self::pout('(%s) ', $data);
			}
			
			self::presult($re);
			
			self::pnl();
			
			self::updateStat($test, $re);
			
			
		}
		
		self::printStat();
		
		self::exitVal();
		
	}
	
	public static function tPear($test) {
		if (!class_exists('PEAR')) {
			@include('PEAR.php');
		}
		
		if (class_exists('PEAR')) {
			return true;
		}
		
		return false;
	}
	
	public static function tVersion($test, $dep, $op) {
		$re = version_compare(PHP_VERSION, $dep, $op);
		self::setTestData($test, 'version %s >= %s', PHP_VERSION, $dep);
		return $re;
	}
	
	public static function tExtension($test, $ext) {
		try {
			$ref = new ReflectionExtension($ext);
			$v = $ref->getVersion();
			self::setTestData($test, '%s found%s', $ref->getName(), $v ? ' v'.$v : '');
			return true;
		}
		catch (ReflectionException $e) {
			self::setTestData($test, $e->getMessage());
		}
		
		return false;
		
	}

	public static function tIniSettings($test, $ini, $cmp_val, $cmp='===', $convert=null) {
		$val = ini_get($ini);
		
		if ($convert !== null) {
			switch ($convert) {
				case 'bytes':
					$val = self::cIniBytes($val);
				break;
			}
		}
		
		$re = false;
		if (eval('return ($val '. $cmp. ' $cmp_val);')) {
			$re = !$re;
		}
		
		self::setTestData($test, '%1$s=\'%2$s\'', $ini, $val);
		
		return $re;
	}
	
	private static function cIniBytes($string) {
		static $mods = array (
			'm' => 1048576,
			'k'	=> 1024,
			'g'	=> 1073741824
		);
		
		$mod = strtolower($string[strlen($string)-1]);
		$val = (int)$string;
		// $val = (int)str_ireplace(array_keys($mods), '', $string);
		
		if (array_key_exists($mod, $mods)) {
			$val *= $mods[$mod];
		}
		
		return $val;
	}
	
	private static function exitVal() {
		$re = 0;
		if (self::$tresult['pass'] !== true) {
			$re = 1;
		}
		
		self::pout('Exit (status=%d)', $re);
		
		self::pnl();
		
		exit($re);
	}
	
	private static function printStat() {
		self::pnl();
		
		self::pout('All over result: %s', self::$tresult['pass'] === true ? 'PASS' : 'FAIL');
		
		self::pout(
			' (required %d/%d, optional %d/%d, all %d/%d, time %.2fs)',
			self::$tresult['req_ok'],
			self::$tresult['req_count'],
			self::$tresult['opt_ok'],
			self::$tresult['opt_count'],
			self::$tresult['ok'],
			self::$tresult['all'],
			(microtime(true) - self::$time_start)
		);
		
		self::pnl(2);
	}
	
	private static function updateStat(array $test, $re) {
		self::$tresult['all']++;
		
		if (array_key_exists('required', $test) && $test['required'] === true) {
			self::$tresult['req_count']++;
			
			if ($re === true) {
				self::$tresult['req_ok']++;
			}
			elseif ($re === false && self::$tresult['pass'] === true) {
				self::$tresult['pass'] = false;
			}
		}
		else {
			self::$tresult['opt_count']++;
			
			if ($re === true) {
				self::$tresult['opt_ok']++;
			}
		}
			
		if ($re === true) {
			self::$tresult['ok']++;
		}
		else {
			self::$tresult['fail']++;
		}
		
	}
	
	private static function pout($string) {
		$a = func_get_args();
		echo vsprintf(array_shift($a), $a);
	}
	
	private static function pspace() {
		echo ' ... ';
	}
	
	private static function presult($result=false) {
		if ($result === true) {
			echo "OK";
		}
		else {
			echo "FAIL";
		}
	}
	
	private static function pnl($num=1) {
		echo str_repeat(chr(10), $num);
	}
	
	private static function setTestData($t_name, $t_data) {
		$a = func_get_args();
		$test = array_shift($a);
		$data = array_shift($a);
		self::$data[$test] = vsprintf($data, $a);
		return true;
	}
	
	private static function existTestData($test) {
		return array_key_exists($test, self::$data);
	}
	
	private static function getTestData($test) {
		if (self::existTestData($test)) {
			return self::$data[$test];
		}
		
		return null;
	}
	
}
?>
