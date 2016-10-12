<?php
namespace GollumSF\CacheBundle\Cache;

use GollumSF\CacheBundle\Provider\MemcachedProvider;
use GollumSF\CacheBundle\Serializer\SimpleSerializerInterface;

/**
 * MemcachedCache
 *
 * @author Damien Duboeuf <smeagolworms4@gmail.com>
 */
class MemcachedCache extends AbstractCache {
	
	const PREFIX_KEYS = 'gsf_keys';
	
	/**
	 * @var MemcachedProvider
	 */
	protected $memcachedProvider;
	
	/**
	 * @var float
	 */
	protected $casToken;
	
	// TODO Memcached is comment but but on php override https://bugs.php.net/bug.php?id=66331
	public function __construct(MemcachedProvider $memcachedProvider, SimpleSerializerInterface $serializer, $namespace) {
		$this->memcachedProvider = $memcachedProvider;
		parent::__construct($serializer, $namespace);
	}
	
	/**
	 * Save value in cache key
	 * @param $key
	 * @param $value
	 * @param int $lifeTime
	 */
	public function set($key, $value, $lifeTime = 0) {
		$lifeTime = $lifeTime ? time() + (int)$lifeTime : 0;
		$this->getMemcached()->set($this->getFinalKey($key), $this->serialize($value), $lifeTime);
		$this->addKey($key);
	}
	
	/**
	 * Return value if key exist
	 * @param $key
	 * @param $defaultValue
	 * @return mixed
	 */
	public function get($key, $defaultValue = NULL) {
		$data = $this->getMemcached()->get($this->getFinalKey($key));
		if ($data !== false && $this->getMemcached()->getResultCode() !== \Memcached::RES_NOTFOUND) {
			return $this->unserialize($data);
		}
		return $defaultValue;
	}
	
	/**
	 * Return rue if key exist
	 * @param $key
	 * @return boolean
	 */
	public function has($key) {
		$has = 
			$this->getMemcached()->get($this->getFinalKey($key)) !== false ||
			$this->getMemcached()->getResultCode() !== \Memcached::RES_NOTFOUND
		;
		if (!$has) {
			$this->deleteKey($key);
		}
		return $has;
	}
	
	/**
	 * Dalete key
	 * @param $key
	 */
	public function delete($key) {
		$this->getMemcached()->delete($this->getFinalKey($key));
		$this->deleteKey($key);
	}
	
	/**
	 * Return all Keys
	 * @return []
	 */
	public function getKeys() {
		$keys = $this->getMemcached()->get($this->getFinalKey(self::PREFIX_KEYS), NULL, $this->casToken);
		if ($keys !== false) {
			$keys = $this->unserialize($keys);
		}
		if (!is_array($keys)) {
			$keys = [];
			$this->getMemcached()->set($this->getFinalKey(self::PREFIX_KEYS), $this->serialize($keys));
			$this->getMemcached()->get($this->getFinalKey(self::PREFIX_KEYS), NULL, $this->casToken);
		}
		return $keys;
	}
	
	public function deleteAll() {
		parent::deleteAll();
		$this->getMemcached()->delete($this->getFinalKey(self::PREFIX_KEYS));
	}
	
	/**
	 * @return \Memcached
	 */
	protected function getMemcached() {
		return $this->memcachedProvider->getMemcached();
	}
	
	/**
	 * Add key list
	 * @param $key
	 */
	protected function addKey($key) {
		$key = $key.'';
		$keys = $this->getKeys();
		$change = false;
		if (!in_array($key, $keys)) {
			$keys[] = $key;
			$change = true;
		}
		if (
			$change && 
			$this->getMemcached()->cas($this->casToken, $this->getFinalKey(self::PREFIX_KEYS), $this->serialize($keys)) === false &&
			$this->getMemcached()->getResultCode() === \Memcached::RES_NOTFOUND
		) {
			$this->addKey($key);
		}
	}
	
	/**
	 * Remvoe key list
	 * @param $key
	 */
	protected function deleteKey($key) {
		$key = $key.'';
		$keys = $this->getKeys();
		$change = false;
		if(($i = array_search($key, $keys)) !== false) {
			unset($keys[$i]);
			$change = true;
		}
		if ($change && !$this->getMemcached()->cas($this->casToken, $this->getFinalKey(self::PREFIX_KEYS), $this->serialize($keys))) {
			$this->deleteKey($key);
		}
	}
}