<?php

class AppKitFileCache extends AppKitCache {
	
	const DIR_MODE					= 0755;
	const FILE_COMPRESSION_LEVEL	= 7;
	const FILE_MODE					= 0644;
	
	private $dir = null;
	
	public function initializeFactory(array $parameters=array()) {
		
		if (array_key_exists('fileDataDir', $parameters)) {
			$this->dir = $parameters['fileDataDir'];
		}
		
		if (!file_exists($this->dir)) {
			mkdir($this->dir, self::DIR_MODE, true);
		}
		
		parent::initializeFactory($parameters);
	}
	
	protected function storeData($region_name = null) {
		
		if (!count($this->getRegionData($region_name))) {
			return false;
		}
		
		parent::storeData($region_name);
		
		$file = $this->getFileName($region_name);
		$data = $this->getRegionRawData($region_name);
		
		if ($this->getMd5($region_name) == md5($data)) {
			return true;
		}
		
		$this->writeMd5($region_name, $data);
		$data = gzdeflate($data, self::FILE_COMPRESSION_LEVEL);
		
		$this->writeData($file, $data);
		
		return true;
	}	
	
	protected function restoreData($region_name = null) {			
		$file = $this->getFileName($region_name);
		if (file_exists($file)) {
			
			$deflate = file_get_contents($file);
			$raw = gzinflate($deflate);
			
			$this->setRegionRawData($region_name, $raw);
			
			$config = $this->getRegionConfig($region_name);
			
			if ((time()-filectime($file))>$config['lifetime']) {
				$this->removeRegionData($region_name);
				
				if (!unlink($file)) {
					throw new AppKitCacheException('Could not unlink cache file: %s', $file);
				}
				
				return false;
			}
			
			parent::restoreData($region_name);
			
			return true;
		}
		
		return false;
	}
	
	private function writeMd5($region_name, $data) {
		$file = $this->getFileName($region_name, 'md5');
		$this->writeData($file, md5($data));
		return true;
	}
	
	private function getMd5($region_name) {
		$file = $this->getFileName($region_name, 'md5');
		if (file_exists($file)) {
			return trim(file_get_contents($file));
		}
		
		return null;
	}
	
	private function writeData($file_name, $data) {
		if (!file_exists($file_name)) {
			touch($file_name);
			chmod($file_name, self::FILE_MODE);
		}
		
		file_put_contents($file_name, $data);
	}
	
	private function getFileName($region_name, $type = 'deflate') {
		return sprintf('%s/%s.cache.%s', $this->dir, $region_name, $type);
	}
	
}

?>