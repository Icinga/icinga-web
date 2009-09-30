<?php

class AppKitEventDispatcher extends AppKitSingleton {
	
	const ASTERISK		= '*';
	
	public static function registerEventClasses(array $events) {
		foreach ($events as $name=>$event) {
			if ($event['event'] && $event['class']) {
				
				if (!array_key_exists('parameter', $events)) {
					$events['parameter'] = array();
				}
				
				self::getInstance()->addListener( 
					$event['event'], 
					AppKit::getInstance($event['class'], $event['parameter']) 
				);
			}
		}
		
		return true;
	}
	
	/**
	 * 
	 * @return AppKitEventDispatcher
	 * @author Marius Hein
	 */
	public static function getInstance() {
		return parent::getInstance('AppKitEventDispatcher');
	}
	
	private $listeners	= array ();
	private $stats		= array ();
	
	public function __construct() {
		parent::__construct();
		
		// Adding the asterisk listeners container
		$this->listeners[self::ASTERISK] = array ();
	}
	
	/**
	 * Adds a eventhandler to the listening stack
	 * @param string $name
	 * @param AppKitEventHandlerInterface $handler
	 * @return boolean
	 * @author Marius Hein
	 */
	public function addListener($name, AppKitEventHandlerInterface &$handler) {
		
		if (!array_key_exists($name, $this->listeners)) {
			$this->listeners[$name] = array ();
		}
		
		// Invoke the main initialize method
		$ref = new ReflectionObject($handler);
		if ($ref->hasMethod('initializeHandler')) {
			$ref->getMethod('initializeHandler')->invoke($handler);
		}
		
		$this->listeners[$name][] =& $handler;

		return true;
		
	}
	
	/**
	 * Invokes a single listener, calling the initialize methods
	 * @param AppKitEventHandlerInterface $h
	 * @param AppKitEvent $e
	 * @return boolean
	 * @throws AppKitEventDispatcherException
	 * @author Marius Hein
	 */
	private function invokeListener(AppKitEventHandlerInterface &$h, AppKitEvent &$e) {
		$ref = new ReflectionObject($h);
		if ($ref->implementsInterface('AppKitEventHandlerInterface')) {
			
			if ($ref->hasMethod('checkEventType')) {
				if ($ref->getMethod('checkEventType')->invoke($h, $e) !== true) {
					throw new AppKitEventDispatcherException('checkEventType have to return true');
				}
			}
			
			if ($ref->hasMethod('checkObjectType')) {
				if ($ref->getMethod('checkObjectType')->invoke($h, $e) !== true) {
					throw new AppKitEventDispatcherException('checkObjectType have to return true');
				}
			}
			
			$m = $ref->getMethod('handleEvent');
			$re = $m->invoke($h, $e);
			
			if ($re !== true) {
				throw new AppKitEventDispatcherException('Handler have to return true!');
			}
			else {
				$e->touch();
				$this->writeStats('status_touched');
			}
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * Write some stats
	 * @param string $key
	 * @param integer $add
	 * @return boolean
	 * @author Marius Hein
	 */
	private function writeStats($key, $add=1) {
		if (!isset($this->stats[$key])) $this->stats[$key] = 0;
		$this->stats[$key] += $add;
		
		return true;
	}
	
	/**
	 * Trigger a event, by a event class
	 * @param AppKitEvent $event
	 * @return boolean
	 * @author Marius Hein
	 */
	public function triggerEvent(AppKitEvent $event) {
		
		if (array_key_exists($event->getName(), $this->listeners)) {	
			if ($event->issetStatus(AppKitEvent::CANCELLED)) {
				$event->unsetStatus(AppKitEvent::CANCELLED);
				$event->setStatus(AppKitEvent::RESUMED);
				$this->writeStats('status_resumed');
			}
			
			$listeners = array_merge($this->listeners[self::ASTERISK], $this->listeners[$event->getName()]);
			
			foreach ($listeners as $listener) {
				if ($event->issetStatus(AppKitEvent::CANCELLED)) {
					$this->writeStats('status_cancelled');
					break;
				}
				$this->writeStats($event->getName());
				$this->invokeListener($listener, $event);
			}
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * Triggers a event without a class
	 * @param string $name
	 * @param string $info
	 * @param object $object
	 * @return AppKitEvent
	 * @author Marius Hein
	 */
	public function triggerSimpleEvent($name, $info = null, &$object=null, array &$data = null) {
		
		$event = new AppKitEvent($name);
		
		if ($object !== null) {
			$event->setObject($object);
		}
		
		if ($info !== null) {
			$event->setInfo($info);
		}
		
		if ($data !== null) {
			$event->setData($data);
		}
		
		$this->triggerEvent($event);
		
		return $event;
	}
	
	/**
	 * Return the stats
	 * @return array
	 * @author Marius Hein
	 */
	public function getStats() {
		return $this->stats;
	}
	
	/**
	 * Returns all registered event names
	 * @return array
	 */
	public function getEvents() {
		return array_keys( $this->listeners );
	}
}

class AppKitEventDispatcherException extends AppKitException {}

?>