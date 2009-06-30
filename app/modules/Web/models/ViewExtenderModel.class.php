<?php

class Web_ViewExtenderModel extends ICINGAWebBaseModel
implements AgaviISingletonModel, IcingaViewExtenderConstInterface
{
	/**
	 * A container for all links
	 * 
	 * @var array
	 */
	private $links		= array ();
	
	/**
	 * Registers a link for a icinga view
	 * 
	 * @param
	 * @return						mixed
	 */
	public function registerLink(IcingaViewExtenderLink $link) {
		if ($link->getType()) {
			
			if (!array_key_exists($link->getType(), $this->links)) {
				$this->links[$link->getType()] = array();
			}
			
			if ($link->getName()) {
				$this->links[$link->getType()][$link->getName()] =& $link;
			}
			else {
				$this->links[$link->getType()][] =& $link;
			}
			
			return true;
		}
		
		throw new IcingaBaseException('Links has no valid type!');
	}
	
	/**
	 * Unregisters a link by a given name or index
	 * @param $identifier
	 * @return unknown_type
	 */
	public function unregisterLink($identifier) {
		
	}
	
	public function countRegisteredTypes($type) {
		if (array_key_exists($type, $this->links) && is_array($this->links[$type])) {
			return count(is_array($this->links[$type]));
		} 
		return null;
	}
	
	public function rewriteType($type, array $rewrite_map = array ())  {
		if ($this->countRegisteredTypes($type)) { 
			$links =& $this->links[$type];
			
			$out = array ();
			
			foreach ($links as $lid=>$link) {
				$content = '';
				
				$route_args = $this->rewriteArgsArray($link->getRouteArgs(), $rewrite_map);
				
				/**
				 * @todo Implement image handling here
				 */
				
				$content = AppKitHtmlHelper::Obj()->LinkToRoute(
					$link->getRoute, 
					$link->getParameter(IcingaViewExtenderLink::PARAM_CAPTION),
					$route_args
				);
				
				$out[$lid] = $content->toString();
			}
			
			return $out;
		}
		
		return $out;
	}
	
	private function rewriteArgsArray(array $args, array $rewrite) {
		foreach ($args as $key=>$val) {
			foreach ($rewrite as $rid=>$rval) {
				if (strpos($val, $rid) !== false) {
					$args[$key] = str_replace($rid, $rval, $val);
				}
			}
		}
		return $args;
	} 
	
}

?>