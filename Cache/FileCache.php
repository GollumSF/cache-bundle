<?php
namespace GollumSF\CacheBundle\Cache;
use GollumSF\CacheBundle\Serializer\SimpleSerializerInterface;

/**
 * FileCache
 *
 * @author Damien Duboeuf <smeagolworms4@gmail.com>
 */
class FileCache extends AbstractCache {
	
	const CACHE_FILE = 'gsf_cache.php';
	
	private $pathCache;
	
	private $data = null;
	
	public function __construct($pathCache, SimpleSerializerInterface $serializer, $namespace) {
		parent::__construct($serializer, $namespace);
		$this->pathCache = $pathCache;
	}
	
	public function __destruct() {
		if ($this->data !== NULL) {
			$this->write();
		}
	}
	
	/**
	 * Save value in cache key
	 * @param $key
	 * @param $value
	 * @param int $lifeTime
	 */
	public function set($key, $value, $lifeTime = 0) {
		$this->data[$key] = [
			'value'    => $this->serialize($value),
			'lifeTime' => (int)$lifeTime,
			'time'     => time(),
		];
		$this->write();
	}
	
	/**
	 * Return value if key exist
	 * @param $key
	 * @param $defaultValue
	 * @return mixed
	 */
	public function get($key, $defaultValue = NULL) {
		if ($this->has($key)) {
			return $this->unserialize($this->data[$key]['value']);
		}
		return $defaultValue;
	}
	
	/**
	 * Return rue if key exist
	 * @param $key
	 * @return boolean
	 */
	public function has($key) {
		$this->read();
		if (array_key_exists($key, $this->data)) {
			$exist = 
				!$this->data[$key]['lifeTime'] ||
				time() - $this->data[$key]['time'] < $this->data[$key]['lifeTime']
			;
			if (!$exist) {
				unset($key);
				$this->write();
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
		if (array_key_exists($key, $this->data)) {
			unset($this->data[$key]);
		}
		$this->write();
	}
	
	/**
	 * Return all Keys
	 * @return []
	 */
	public function getKeys() {
		return array_keys($this->data);
	}
	
	/**
	 * @return string
	 */
	protected function getCacheFilePath() {
		return $this->pathCache.DIRECTORY_SEPARATOR.$this->getNamespace().'_'.self::CACHE_FILE;
	}
	
	/**
	 * Write the cache
	 */
	protected function write() {
		file_put_contents($this->getCacheFilePath(), "<?php\n".'$data = '.var_export($this->data, true).';');
	}
	
	/**
	 * Read the cache
	 */
	protected function read() {
		$this->data = [];
		if (file_exists($this->getCacheFilePath())) {
			require $this->getCacheFilePath();
			if (isset($data)) {
				$this->data = $data;
			}
		}
	}
}