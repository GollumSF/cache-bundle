<?php
namespace GollumSF\CacheBundle\Serializer;

/**
 * SimpleSerializerInterface
 *
 * @author Damien Duboeuf <smeagolworms4@gmail.com>
 */
interface SimpleSerializerInterface {
	
	/**
	 * @param mixed $data
	 * @return string
	 */
	public function serialize($object);
	
	/**
	 * @param mixed $data
	 * @return mixed
	 */
	public function unserialize($data);
	
}