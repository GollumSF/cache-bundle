<?php
namespace GollumSF\CacheBundle\Cache;

use GollumSF\CacheBundle\Serializer\SimpleSerializerInterface;

/**
 * AbstractCache
 *
 * @author Damien Duboeuf <smeagolworms4@gmail.com>
 */
abstract class AbstractCache implements CacheInterface {
	
	/**
	 * @var SimpleSerializerInterface
	 */
	private $serializer;
	
	/**
	 * @var string
	 */
	private $namespace;
	
	/**
	 * @param string $namespace
	 */
	public function __construct(SimpleSerializerInterface $serializer, $namespace) {
		$this->serializer = $serializer;
		$this->namespace = $namespace;
	}
	
	/**
	 * @return string
	 */
	protected function getNamespace() {
		return $this->namespace;
	}
	
	/**
	 * Delete all keys
	 */
	public function deleteAll() {
		foreach ($this->getKeys() as $key) {
			$this->delete($key);
		}
	}
	
	/**
	 * @param $value
	 * @return string
	 */
	protected function serialize($value) {
		return $this->serializer->serialize($value);
	}
	
	/**
	 * @param $data
	 * @return mixed
	 */
	protected function unserialize($data) {
		return $this->serializer->unserialize($data);
	}
	
	/**
	 * @param string $key
	 * @return string
	 */
	protected function getFinalKey($key) {
		return $this->getNamespace().'_'.$key;
	}
	
}