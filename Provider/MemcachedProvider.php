<?php
namespace GollumSF\CacheBundle\Provider;
use GollumSF\CacheBundle\Exception\MemcachedOptionException;

/**
 * MemcachedProvider
 *
 * @author Damien Duboeuf <smeagolworms4@gmail.com>
 */
class MemcachedProvider implements MemcachedProviderInterface {
	
	/**
	 * @var string
	 */
	private $memcachedId;
	
	/**
	 * @var string
	 */
	private $memcachedClass;
	
	/**
	 * @var \Memcached
	 */
	private $memcached;
	
	/**
	 * @var []
	 */
	private $options;
	
	public function __construct($memcachedClass, $memcachedId = 'gsf_cache', $options = []) {
		$this->memcachedId    = $memcachedId;
		$this->memcachedClass = $memcachedClass;
		$this->options        = $options;
	}
	
	/**
	 * @return \Memcached
	 */
	public function getMemcached() {
		if (!$this->memcached) {
			$this->memcached = new $this->memcachedClass($this->memcachedId);
			
			$this->addServers($this->memcached, $this->options);
			$this->authentification($this->memcached, $this->options);
			
			foreach ($this->options as $option => $value) {
				if (
					$option == 'servers' ||
					$option == 'username' ||
					$option == 'password'
				) {
					continue;
				}
				$this->memcached->setOption($this->convertMemcachedOption($option), $value);
			}
		}
		return $this->memcached;
	}
	
	/**
	 * @param \Memcached $memcached
	 * @param [] $options
	 */
	protected function addServers($memcached, $options) {
		$servers = array_key_exists('servers', $options) ? $options['servers'] : [];
		foreach ($servers as $server) {
			$host   = $server['host'];
			$port   = $server['port'];
			$weight = array_key_exists('weight', $server) ? $server['weight'] : 0;
			$memcached->addServer($host, $port, $weight);
		}
	}
	
	/**
	 * @param \Memcached $memcached
	 * @param [] $options
	 */
	protected function authentification($memcached, $options) {
		$username = array_key_exists('username', $options) ? $options['username'] : NULL;
		$password = array_key_exists('password', $options) ? $options['password'] : '';
		if ($username) {
			$memcached->setSaslAuthData($username, $password);
		}
	}
	
	/**
	 * @param string $strOpt
	 * @return int
	 * @throws MemcachedOptionException
	 */
	protected function convertMemcachedOption($strOpt) {
		$option =  @constant('\Memcached::OPT_'.strtoupper($strOpt));
		if ($option === NULL) {
			throw new MemcachedOptionException(sprintf('The option %s not exist in memcached', $strOpt));
		}
		return $option;
	}
}