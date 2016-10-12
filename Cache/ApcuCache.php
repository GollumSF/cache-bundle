<?php
namespace GollumSF\CacheBundle\Cache;

/**
 * ApcuCache
 *
 * @author Damien Duboeuf <smeagolworms4@gmail.com>
 */
class ApcuCache extends AbstractCache {
	
	/**
	 * Save value in cache key
	 * @param $key
	 * @param $value
	 * @param int $lifeTime
	 */
	public function set($key, $value, $lifeTime = 0) {
		apcu_store($this->getFinalKey($key), $this->serialize([
			'value'    => $value,
			'lifeTime' => (int)$lifeTime,
			'time'     => time(),
		]), $lifeTime);
	}
	
	/**
	 * Return value if key exist
	 * @param $key
	 * @param $defaultValue
	 * @return mixed
	 */
	public function get($key, $defaultValue = NULL) {
		if ($this->has($key)) {
			$success = false;
			$data = apcu_fetch($this->getFinalKey($key), $success);
			if ($success) {
				return $this->unserialize($data)['value'];
			}
		}
		return $defaultValue;
	}
	
	/**
	 * Return rue if key exist
	 * @param $key
	 * @return boolean
	 */
	public function has($key) {
		if (apcu_exists($this->getFinalKey($key))) {
			$exist = false;
			$data = apcu_fetch($this->getFinalKey($key), $exist);
			if ($exist) {
				$data = $this->unserialize($data);
				$exist =
					!$data['lifeTime'] ||
					time() - $data['time'] < $data['lifeTime']
				;
			}
			if (!$exist) {
				apcu_delete($this->getFinalKey($key));
			}
			return $exist;
		}
		return false;
	}
	
	/**
	 * Dalete key
	 * @param $key
	 */
	public function delete($key) {
		if ($this->has($key)) {
			apcu_delete($this->getFinalKey($key));
		}
	}
	
	/**
	 * Return all Keys
	 * @return []
	 */
	public function getKeys() {
		$keys = [];
		$prefix = $this->getNamespace().'_';
		foreach (new \APCUIterator('/^'.$prefix.'/') as $entry) {
			$keys[] = substr($entry['key'], strlen($prefix));
		}
		return $keys;
	}
}