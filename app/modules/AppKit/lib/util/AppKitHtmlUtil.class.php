<?php

class AppKitHtmlUtil {
	
	const IMAGE_SUFFIX			= 'png';
	const IMAGE_PATH_IDENTIFIER	= '.';
	const IMAGE_PATH_SEPERATOR	= '/';
	
	/**
	 * @param string $route_name
	 * @param string $caption
	 * @param array $route_args
	 * @param array $attributes
	 * @return AppKitXmlTag
	 */
	public static function LinkToRoute($route_name, $caption, array $route_args = array (), array $attributes = array (), AgaviRequestDataHolder $other_args = null) {
		if ($other_args != null) {
			$route_args = array_merge($other_args->getParameters(), $route_args);
		}
		
		// Rewrite the ambersands (Because DOM rewrites it again!)
		$href = AppKitAgaviUtil::getContext()->getRouting()->gen($route_name, $route_args);
		
		$href = str_replace('&amp;', '&', $href);
		
		return AppKitXmlTag::create('a')
		->setContent($caption)
		->setNotEmpty()
		->addAttribute('href', $href)
		->addAttributeArray($attributes);
	}
	
	public static function imageUrl($def, $suffix=null) {
		$url = AgaviConfig::get('org.icinga.appkit.image_path'). '/'
		. str_replace(self::IMAGE_PATH_IDENTIFIER, self::IMAGE_PATH_SEPERATOR, $def)
		. '.'. (isset($suffix) ? $suffix : self::IMAGE_SUFFIX);
		return $url;
	}
	
}

?>