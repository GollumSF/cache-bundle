<?php
namespace GollumSF\CacheBundle\Tests\Cache;

use GollumSF\CacheBundle\Cache\MemcachedCache;
use GollumSF\CacheBundle\Provider\MemcachedProvider;

require_once __DIR__.'/../PHPModule/memcached.php';

// TODO Memcached is comment but but on php override https://bugs.php.net/bug.php?id=66331
class MemcachedDummy /* extends \Memcached */ {
	
	private $data = [];
	private $code = NULL;
	
	public function __construct() {
		// parent::__construct('dummy'); // TODO See previous todo
	}
	
	public function set($key, $value, $expiration = null, $udf_flags = NULL) {
		$this->data[$key] = [
			'value'    => $value,
			'lifeTime' => $expiration,
		];
		$this->code = \Memcached::RES_SUCCESS;
	}
	
	public function get($key, $cache_cb = NULL, &$cas_token = NULL, &$udf_flags = NULL) {
		$exist =
			array_key_exists($key, $this->data) &&
			(
				!$this->data[$key]['lifeTime'] ||
				time() < $this->data[$key]['lifeTime']
			)
		;
		if ($exist) {
			$this->code = \Memcached::RES_SUCCESS;
			return $this->data[$key]['value'];
		}
		$this->code = \Memcached::RES_NOTFOUND;
		return false;
	}
	
	public function delete($key, $time = 0) {
		$this->code = \Memcached::RES_NOTFOUND;
		if (array_key_exists($key, $this->data)) {
			unset($this->data[$key]);
			$this->code = \Memcached::RES_SUCCESS;
		}
	}
	
	public function cas($cas_token, $key, $value, $expiration = NULL, $flag = NULL) {
		$this->code = \Memcached::RES_NOTFOUND;
		if (array_key_exists($key, $this->data)) {
			$this->set($key, $value, $expiration);
			$this->code = \Memcached::RES_SUCCESS;
		}
		return false;
	}
	
	public function getResultCode() {
		return $this->code;
	}
	
}

/**
 * MemcachedCacheTest
 *
 * @author Damien Duboeuf <smeagolworms4@gmail.com>
 */
class MemcachedCacheTest extends CacheTestCase {
	
	/**
	 * @var MemcachedProvider
	 */
	private $memcachedProvider;
	
	public function setUp() {
		parent::setUp();
		$this->memcachedProvider = new MemcachedProvider(MemcachedDummy::class);
		$this->cache = new MemcachedCache($this->memcachedProvider, $this->serializer, $this->ns);
	}
	
	public function tearDown() {
		$this->cache->deleteAll();
		parent::tearDown();
	}
	
	/**
	 * @covers GollumSF\CacheBundle\Cache\MemcachedCache::set
	 * @dataProvider provide
	 */
	public function testSet($key, $value) {
		$this->cache->set($key, $value);
		$this->assertEquals(
			$this->memcachedProvider->getMemcached()->get($this->ns.'_'.$key),
			$this->serializer->serialize($value)
		);
	}
	
	/**
	 * @covers GollumSF\CacheBundle\Cache\MemcachedCache::get
	 * @dataProvider provide
	 * @depends testSet
	 */
	public function testGet($key, $value) {
		parent::testGet($key, $value);
	}
	
	/**
	 * @covers GollumSF\CacheBundle\Cache\MemcachedCache::get
	 * @dataProvider provide
	 * @depends testSet
	 */
	public function testGetExpired($key, $value) {
		parent::testGetExpired($key, $value);
	}
	
	/**
	 * @covers GollumSF\CacheBundle\Cache\MemcachedCache::get
	 */
	public function testGetDefaultValue() {
		parent::testGetDefaultValue();
	}
	
	/**
	 * @covers GollumSF\CacheBundle\Cache\MemcachedCache::has
	 * @dataProvider provide
	 * @depends testSet
	 */
	public function testHas($key, $value) {
		parent::testHas($key, $value);
	}
	
	/**
	 * @covers GollumSF\CacheBundle\Cache\MemcachedCache::has
	 * @dataProvider provide
	 * depends testSet
	 */
	public function testHasExpired($key, $value) {
		parent::testHasExpired($key, $value);
	}
	
	/**
	 * @covers GollumSF\CacheBundle\Cache\MemcachedCache::has
	 * @dataProvider provide
	 * @depends testSet
	 */
	public function testNotHas($key, $value) {
		parent::testNotHas($key, $value);
	}
	
	/**
	 * @covers GollumSF\CacheBundle\Cache\MemcachedCache::delete
	 * @dataProvider provide
	 * @depends testSet
	 * @depends testHas
	 * @depends testNotHas
	 */
	public function testDelete($key, $value) {
		parent::testDelete($key, $value);
	}
	
	/**
	 * @covers GollumSF\CacheBundle\Cache\MemcachedCache::getKeys
	 * @dataProvider provideKeys
	 * @depends testSet
	 * @depends testHas
	 * @depends testNotHas
	 */
	public function testGetKeys($data) {
		parent::testGetKeys($data);
	}
	
	/**
	 * @covers GollumSF\CacheBundle\Cache\MemcachedCache::deleteAll
	 * @dataProvider provideKeys
	 * @depends testSet
	 * @depends testGetKeys
	 */
	public function testDeleteAll($data) {
		parent::testGetKeys($data);
	}
	
	/**
	 * @covers GollumSF\CacheBundle\Cache\MemcachedCache::getFinalKey
	 * @dataProvider provide
	 */
	public function testGetFinalKey($key) {
		parent::testGetFinalKey($key);
	}
	
}