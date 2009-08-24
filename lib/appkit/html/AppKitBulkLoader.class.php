<?php

/**
 * AppKitBulkLoader - bulk loader for text files such as JavaScript, CSS, etc.
 * @author Christian Doebler <christian.doebler@netways.de>
 *
 * examples:
 *
 * javascript loading content:
 * $loader = new AppKitBulkLoader();
 * var_dump($loader->setType(AppKitBulkLoader::CODE_TYPE_JAVASCRIPT)->setOutput(AppKitBulkLoader::OUTPUT_TYPE_URL)->setBase('http://localhost/dev/appkittools/')->setFile('script_one.js')->setFile('script_two.js')->getContent());
 *
 * javascript inline content:
 * $loader = new AppKitBulkLoader();
 * var_dump($loader->setType(AppKitBulkLoader::CODE_TYPE_JAVASCRIPT)->setOutput(AppKitBulkLoader::OUTPUT_TYPE_INLINE)->setBase('/my/file/root/')->setFile('script_one.js')->setFile('script_two.js')->getContent());
 *
 * css loading content:
 * $loader = new AppKitBulkLoader();
 * var_dump($loader->setType(AppKitBulkLoader::CODE_TYPE_CSS)->setOutput(AppKitBulkLoader::OUTPUT_TYPE_URL)->setBase('http://localhost/dev/appkittools/')->setFile('style_one.css')->setFile('style_two.css')->getContent());
 *
 * css inline content:
 * $loader = new AppKitBulkLoader();
 * var_dump($loader->setType(AppKitBulkLoader::CODE_TYPE_CSS)->setOutput(AppKitBulkLoader::OUTPUT_TYPE_INLINE)->setBase('/my/file/root/')->setFile('style_one.css')->setFile('style_two.css')->getContent());
 *
 * raw content:
 * $loader = new AppKitBulkLoader();
 * var_dump($loader->setBase('/my/file/root/')->setFile('file_one.js')->setFile('file_two.js')->getContent());
 *
 * compressed raw content (no newlines):
 * $loader = new AppKitBulkLoader();
 * var_dump($loader->setCompress(true)->setBase('/my/file/root/')->setFile('file_one.css')->setFile('file_two.css')->getContent());
 */
class AppKitBulkLoader {

	/*
	 * VARIABLES
	 */
	private $config = array (
		'base'		=> null,
		'compress'	=> false,
		'file'		=> array(),
		'output'	=> false,
		'type'		=> false,
	);

	private $fileList = array();
	private $fileContent = null;
	private $filesRead = false;

	private $cssUrlTemplate = '<link rel="stylesheet" type="text/css" href="%s" />';
	private $jsUrlTemplate = '<script type="text/javascript" src="%s"></script>';
	
	private $cssContentTemplate =
		'<style type="text/css">
%s
</style>';
	private $jsContentTemplate =
		'<script type="text/javascript">
//<![CDATA[
%s
//]]>
</script>';

	const CODE_TYPE_CSS = 'css';
	const CODE_TYPE_JAVASCRIPT = 'js';

	const OUTPUT_TYPE_INLINE = 'inline';
	const OUTPUT_TYPE_URL = 'url';

	/*
	 * METHODS
	 */

	/**
	 * class constructor
	 * @param	array			$config			configuration data (see class variable $config) as associative array
	 * @return	object							AppKitBulkLoader
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function __construct ($config = false) {
		if ($config !== false) {
			$this->setConfig($config);
		}
	}

	/**
	 * gets config values
	 * @param	string			$key			configuration key (see class variable $config)
	 * @return	mixed							configuration value (false on error)
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function __get ($key = false) {
		$retVal = false;
		if ($key !== false && array_key_exists($key, $this->config)) {
			$retVal = $this->config[$key];
		} else {
			throw new AppKitBulkLoaderException(__CLASS__ . ': get(): Invalid key "' . $key . '"!');
		}
		return $retVal;
	}

	/**
	 * sets config variables
	 * @param	string			$key			configuration key (see class variable $config)
	 * @param	string			$value			configuration value
	 * @return	object							AppKitBulkLoader
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function __set ($key = false, $value = false) {
		switch ($key) {
			case 'base':
				$this->setBase($value);
				break;
			case 'compress':
				$this->setCompress($value);
				break;
			case 'file':
				$this->setFile($value);
				break;
			case 'output':
				$this->setOutput($value);
				break;
			case 'type':
				$this->setType($value);
				break;
			default:
				throw new AppKitBulkLoaderException(__CLASS__ . ': set(): Invalid key: "' . $key . '"!');
				break;
		}
		return $this;
	}

	/**
	 * sets base
	 * @param	string			$base			base dir or path
	 * @return	object							AppKitBulkLoader
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function setBase ($base = false) {
		if ($base !== false) {
			$this->config['base'] = $base;
		} else {
			throw new AppKitBulkLoaderException(__CLASS__ . ': setBase(): Invalid base: "' . $base . '"!');
		}
		return $this;
	}

	/**
	 * sets whether or not to remove newlines from content after reading from files
	 * @param	boolean			$compress		compress flag (true, false)
	 * @return	object							AppKitBulkLoader
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function setCompress ($compress = false) {
		if ($compress === true || $compress === false) {
			$this->config['compress'] = $compress;
		} else {
			throw new AppKitBulkLoaderException(__CLASS__ . ': setBase(): Invalid base: "' . $base . '"!');
		}
		return $this;
	}

	/**
	 * sets bulk files
	 * @param	mixed			$file			name of file or array containing file names to add to bulk loader
	 * @return	object							AppKitBulkLoader
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function setFile ($file = false) {
		if ($file !== false) {
			if (!is_array($file)) {
				if (!in_array($file, $this->config['file'])) {
					array_push($this->config['file'], $file);
				}
			} else {
				foreach ($file as $currentFile) {
					if (!in_array($currentFile, $this->config['file'])) {
						array_push($this->config['file'], $currentFile);
					}
				}
			}
		} else {
			throw new AppKitBulkLoaderException(__CLASS__ . ': setFile(): Invalid file: "' . $file . '"!');
		}
		return $this;
	}

	/**
	 * sets output type
	 * @param	string			$output			output type (inline, url)
	 * @return	object							AppKitBulkLoader
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function setOutput ($output = false) {
		if ($output !== false && $output == self::OUTPUT_TYPE_INLINE || $output == self::OUTPUT_TYPE_URL) {
			$this->config['output'] = $output;
		} else {
			throw new AppKitBulkLoaderException(__CLASS__ . ': setBase(): Invalid output type: "' . $output . '"!');
		}
		return $this;
	}

	/**
	 * sets type of code
	 * @param	string			$type			type of code (css, js)
	 * @return	object							AppKitBulkLoader
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function setType ($type = false) {
		if ($type !== false && $type == self::CODE_TYPE_CSS || $type == self::CODE_TYPE_JAVASCRIPT) {
			$this->config['type'] = $type;
		} else {
			throw new AppKitBulkLoaderException(__CLASS__ . ': setBase(): Invalid type of code: "' . $type . '"!');
		}
		return $this;
	}

	/**
	 * sets configuration data via an associative array
	 * @param	array			$config			configuration data (see class variable $config) as associative array
	 * @return	object							AppKitBulkLoader
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function setConfig (array $config = array()) {
		if (!empty($config)) {
			foreach ($config as $key => $value) {
				$this->{$key} = $value;
			}
		} else {
			throw new AppKitBulkLoaderException(__CLASS__ . ': setConfig(): Invalid data!');
		}
		return $this;
	}

	/**
	 * checks files set in configuration array and creates a list of absolute file names
	 * @param	array			$config			configuration data (see class variable $config) as associative array (optional)
	 * @return	boolean							true if files exist and if they are readable otherwise false
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function checkFiles ($config = false) {
		if ($config !== false) {
			$this->setConfig($config);
		}
		$filesOk = true;
		foreach ($this->config['file'] as $currentFile) {
			$currentAbsoluteFilename = $this->base . '/' . $currentFile;
			if (file_exists($currentAbsoluteFilename) && is_readable($currentAbsoluteFilename)) {
				array_push($this->fileList, $currentAbsoluteFilename);
			} else {
				$this->fileList = array();
				throw new AppKitBulkLoaderException(__CLASS__ . ': checkFiles(): file does not exist or is not readable: "' . $currentAbsoluteFilename . '"!');
				$filesOk = false;
				break;
			}
		}
		return $filesOk;
	}

	/**
	 * reads files set in configuration array
	 * @param	array			$config			configuration data (see class variable $config) as associative array (optional)
	 * @return	boolean							true if cache is expired otherwise false
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function readFiles ($config = false) {
		if ($config !== false) {
			$this->setConfig($config);
		}
		$readOk = true;
		if (!$this->filesRead) {
			if ($this->checkFiles()) {
				foreach ($this->fileList as $file) {
					$content = @file_get_contents($file);
					if ($content !== false) {
						$this->fileContent .= $content . "\n\n";
					} else {
						$readOk = false;
						$this->fileContent = null;
						$this->filesRead = false;
						break;
					}
				}
				if ($readOk) {
					$this->filesRead = true;
					if ($this->compress) {
						$this->fileContent = str_replace(array("\r\n", "\n", "\r"), array('', '', ''), $this->fileContent);
					}
				}
			}
		}
		return $readOk;
	}

	/**
	 * generates javascript tag for loading and returns it
	 * @param	array			$config			configuration data (see class variable $config) as associative array (optional)
	 * @return	string							html content
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	private function getJsUrlHtml ($config = false) {
		if ($config !== false) {
			$this->setConfig($config);
		}
		$href = $this->base . implode('&', $this->config['file']);
		$jsHtml = sprintf($this->jsUrlTemplate, $href);
		return $jsHtml;
	}

	/**
	 * generates javascript tag w/ inline content and returns it
	 * @param	array			$config			configuration data (see class variable $config) as associative array (optional)
	 * @return	string							html content
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	private function getJsContentHtml ($config = false) {
		if ($config !== false) {
			$this->setConfig($config);
		}
		$jsHtml = sprintf($this->jsContentTemplate, $this->fileContent);
		return $jsHtml;
	}

	/**
	 * generates css tag for loading and returns it
	 * @param	array			$config			configuration data (see class variable $config) as associative array (optional)
	 * @return	string							html content
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	private function getCssUrlHtml ($config = false) {
		if ($config !== false) {
			$this->setConfig($config);
		}
		$href = $this->base . implode('&', $this->config['file']);
		$cssHtml = sprintf($this->cssUrlTemplate, $href);
		return $cssHtml;
	}

	/**
	 * generates css tag w/ inline content and returns it
	 * @param	array			$config			configuration data (see class variable $config) as associative array (optional)
	 * @return	string							html content
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	private function getCssContentHtml ($config = false) {
		if ($config !== false) {
			$this->setConfig($config);
		}
		$cssHtml = sprintf($this->cssContentTemplate, $this->fileContent);
		return $cssHtml;
	}

	/**
	 * loads content of defined files if not done so far and returns it
	 * @param	boolean			$compress		remove newlines from file content (true, false)
	 * @param	array			$config			configuration data (see class variable $config) as associative array (optional)
	 * @return	string							file content
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function getContent ($config = false) {
		if ($config !== false) {
			$this->setConfig($config);
		}
		$error = false;
		$content = false;
		switch ($this->output) {
			case self::OUTPUT_TYPE_INLINE:
			case false:
				if (!$this->filesRead) {
					$this->readFiles();
				}
				break;
			case self::OUTPUT_TYPE_URL:
				break;
			default:
				throw new AppKitBulkLoaderException(__CLASS__ . ': getContent(): Invalid output type: "' . $output . '"!');
				$error = true;
				break;
		}
		if (!$error) {
			switch ($this->type) {
				case self::CODE_TYPE_CSS:
					if ($this->output == self::OUTPUT_TYPE_INLINE) {
						$content = $this->getCssContentHtml();
					} else {
						$content = $this->getCssUrlHtml();
					}
					break;
				case self::CODE_TYPE_JAVASCRIPT:
					if ($this->output == self::OUTPUT_TYPE_INLINE) {
						$content = $this->getJsContentHtml();
					} else {
						$content = $this->getJsUrlHtml();
					}
					break;
				case false:
					$content = $this->fileContent;
					break;
				default:
					throw new AppKitBulkLoaderException(__CLASS__ . ': getContent(): Invalid type of code: "' . $type . '"!');
					break;
			}
		}
		return $content;
	}

}

// add own exceptions
class AppKitBulkLoaderException extends AppKitException {}

?>