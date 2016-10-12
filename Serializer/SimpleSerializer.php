<?php
namespace GollumSF\CacheBundle\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * SimpleSerializer
 *
 * @author Damien Duboeuf <smeagolworms4@gmail.com>
 */
class SimpleSerializer implements SimpleSerializerInterface {
	
	/**
	 * @param mixed $object
	 * @return string
	 */
	public function serialize($object) {
		return serialize($object);
	}
	
	/**
	 * @param mixed $data
	 * @return object
	 */
	public function unserialize($data) {
		return unserialize($data);
	}
	
}