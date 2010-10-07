<?php

class AppKit_Tasks_ClearCacheModel extends AppKitBaseModel {

	public function clearCache() {
		$cache_dir = AgaviConfig::get('core.cache_dir');
		
		$content_dir = $cache_dir. '/content';
		if (is_dir($content_dir)) {
			$this->getContext()->getLoggerManager()->log(sprintf('ClearCache: rmdir %s', $content_dir), AgaviLogger::INFO);
			rmdir($content_dir);
		}
		
		$iterator = new GlobIterator($cache_dir. '/config/*.php', FilesystemIterator::KEY_AS_FILENAME);
		$config_count = count($iterator);
		if ($config_count > 0) {
			foreach ($iterator as $fi) {
				unlink($fi->getRealPath());
			}
			$this->getContext()->getLoggerManager()->log(sprintf('ClearCache: Deleted %d cache files', $config_count), AgaviLogger::INFO);
		}
	}
	
}

?>