<?php
/**
 * polyfill-apc for test
 */
if (!extension_loaded('apc') || !ini_get('apc.enabled')) {
	
	class APCIterator implements Iterator {
		
		public static $data = [];
		private $position = 0;
		private $match;
		
		public function __construct($user, $match) {
			$this->match = $match;
		}
		
		public function current() {
			$i = 0;
			$entry = [];
			foreach (self::$data as $key => $value) {
				if (apc_exists($key) && preg_match($this->match, $key)) {
					$entry['key'] = $key;
					$entry['value'] = $value;
					if ($i == $this->position) {
						break;
					}
					$i++;
				}
			}
			return $entry;
		}
		public function key()    { return $this->position; }
		public function next()   {  ++$this->position; }
		public function rewind() { $this->position = 0; }
		public function valid() {
			$i = 0;
			foreach (self::$data as $key => $value) {
				if (apc_exists($key) && preg_match($this->match, $key)) {
					if ($i == $this->position) {
						return true;
					}
					$i++;
				}
			}
			return false;
		}
	}
	
	function apc_store($key, $value, $lifeTime = 0) {
		APCIterator::$data[$key] = [
			'value'    => $value,
			'lifeTime' => $lifeTime,
			'time'     => time(),
		];
	}
	
	function apc_exists($key) {
		return
			array_key_exists($key, APCIterator::$data) &&
			(
				!APCIterator::$data[$key]['lifeTime'] ||
				time() - APCIterator::$data[$key]['time'] < APCIterator::$data[$key]['lifeTime']
			)
		;
	}
	
	function apc_fetch($key, &$success = NULL) {
		return ($success = apc_exists($key)) ? APCIterator::$data[$key]['value'] : NULL;
	}
	
	function apc_delete($key) {
		if(array_key_exists($key, APCIterator::$data)) {
			unset(APCIterator::$data[$key]);
		}
	}
	
}