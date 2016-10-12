<?php
namespace GollumSF\CacheBundle\Cache;

/**
 * CacheInterface
 *
 * @author Damien Duboeuf <smeagolworms4@gmail.com>
 */
interface CacheInterface {
	
	/**
	 * Save value in cache key
	 * @param $key
	 * @param $value
	 * @param int $lifeTime
	 */
	public function set($key, $value, $lifeTime = 0);
	
	/**
	 * Return value if key exist
	 * @param $key
	 * @param $defaultValue
	 * @return mixed
	 */
	public function get($key, $defaultValue = NULL);
	
	/**
	 * Return rue if key exist
	 * @param $key
	 * @return boolean
	 */
	public function has($key);
	
	/**
	 * Dalete key
	 * @param $key
	 */
	public function delete($key);
	
	/**
	 * Delete all keys
	 */
	public function deleteAll();
	
	/**
	 * Return all Keys
	 * @return []
	 */
	public function getKeys();
	
}