<?php
namespace GollumSF\CacheBundle\Provider;

/**
 * MemcachedProviderInterface
 *
 * @author Damien Duboeuf <smeagolworms4@gmail.com>
 */
interface MemcachedProviderInterface {
	
	/**
	 * @return \Memcached
	 */
	public function getMemcached();
	
}