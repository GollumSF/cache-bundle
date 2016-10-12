<?php
/**
 * polyfill-apc for test
 */
if (!extension_loaded('apcu') || !ini_get('apc.enabled')) {
	
	class APCUIterator implements Iterator {
		
		public static $data = [];
		private $position = 0;
		private $match;
		
		public function __construct($match) {
			$this->match = $match;
		}
		
		public function current() {
			$i = 0;
			$entry = [];
			foreach (self::$data as $key => $value) {
				if (apcu_exists($key) && preg_match($this->match, $key)) {
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
				if (apcu_exists($key) && preg_match($this->match, $key)) {
					if ($i == $this->position) {
						return true;
					}
					$i++;
				}
			}
			return false;
		}
	}
	
	function apcu_store($key, $value, $lifeTime = 0) {
		APCUIterator::$data[$key] = [
			'value'    => $value,
			'lifeTime' => $lifeTime,
			'time'     => time(),
		];
	}
	
	function apcu_exists($key) {
		return
			array_key_exists($key, APCUIterator::$data) &&
			(
				!APCUIterator::$data[$key]['lifeTime'] ||
				time() - APCUIterator::$data[$key]['time'] < APCUIterator::$data[$key]['lifeTime']
			)
			;
	}
	
	function apcu_fetch($key, &$success = NULL) {
		return ($success = apcu_exists($key)) ? APCUIterator::$data[$key]['value'] : NULL;
	}
	
	function apcu_delete($key) {
		if(array_key_exists($key, APCUIterator::$data)) {
			unset(APCUIterator::$data[$key]);
		}
	}
	
}