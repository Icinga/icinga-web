<?php

class AppKit_Tasks_ClearCacheModel extends AppKitBaseModel {

	public function clearCache() {
		$cache_dir = AgaviConfig::get('core.cache_dir');
		
		$content_dir = $cache_dir. '/content';
		if (is_dir($content_dir)) {
			$config_count = $this->removeFilesRecursive($content_dir);
			$this->getContext()->getLoggerManager()->log(sprintf('ClearCache: Deleted %d cache (content) objects', $config_count), AgaviLogger::INFO);
		}
		
		$iterator = new GlobIterator($cache_dir. '/config/*.php', FilesystemIterator::KEY_AS_FILENAME);
		$config_count = count($iterator);
		if ($config_count > 0) {
			foreach ($iterator as $fi) {
				unlink($fi->getRealPath());
			}
			$this->getContext()->getLoggerManager()->log(sprintf('ClearCache: Deleted %d cache (config) files', $config_count), AgaviLogger::INFO);
		}
	}
	
	private function removeFilesRecursive($path) {
		$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
		
		$order = array ();
		
		foreach ($iterator as $fileObject) {
			$order[] =$fileObject->getRealPath();
		}
		
		$count = count($order);
		
		while(($file = array_pop($order))) {
			if (is_dir($file)) {
				rmdir($file);
			}
			elseif (is_file($file)) {
				unlink($file);
			}
		}
		
		return $count;
	}
	
}

?>