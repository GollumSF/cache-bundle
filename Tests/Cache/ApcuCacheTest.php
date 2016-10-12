<?php
namespace GollumSF\CacheBundle\Tests\Cache;

use GollumSF\CacheBundle\Cache\ApcuCache;

require_once __DIR__.'/../PHPModule/apcu.php';

/**
 * ApcuCacheTest
 *
 * @author Damien Duboeuf <smeagolworms4@gmail.com>
 */
class ApcuCacheTest extends CacheTestCase {
	
	public function setUp() {
		parent::setUp();
		$this->cache = new ApcuCache($this->serializer, $this->ns);
	}
	
	public function tearDown() {
		 foreach (new \APCUIterator('/^'.self::NS_PREFIX.'/') as $entry) {
			 apcu_delete($entry['key']);
		 }
		 parent::tearDown();
	 }
	
	/**
	 * @covers GollumSF\CacheBundle\Cache\ApcuCache::set
	 * @dataProvider provide
	 */
	public function testSet($key, $value) {
		$this->cache->set($key, $value);
		$this->assertEquals(
			apcu_fetch($this->ns.'_'.$key),
			$this->serializer->serialize([
				'value'    => $value,
				'lifeTime' => 0,
				'time'     => time(),
			])
		);
	}
	
	/**
	 * @covers GollumSF\CacheBundle\Cache\ApcuCache::get
	 * @dataProvider provide
	 * @depends testSet
	 */
	public function testGet($key, $value) {
		parent::testGet($key, $value);
	}
	
	/**
	 * @covers GollumSF\CacheBundle\Cache\ApcuCache::get
	 * @dataProvider provide
	 * @depends testSet
	 */
	public function testGetExpired($key, $value) {
		parent::testGetExpired($key, $value);
	}
	
	/**
	 * @covers GollumSF\CacheBundle\Cache\ApcuCache::get
	 */
	public function testGetDefaultValue() {
		parent::testGetDefaultValue();
	}
	
	/**
	 * @covers GollumSF\CacheBundle\Cache\ApcuCache::has
	 * @dataProvider provide
	 * @depends testSet
	 */
	public function testHas($key, $value) {
		parent::testHas($key, $value);
	}
	
	/**
	 * @covers GollumSF\CacheBundle\Cache\ApcuCache::has
	 * @dataProvider provide
	 * depends testSet
	 */
	public function testHasExpired($key, $value) {
		parent::testHasExpired($key, $value);
	}
	
	/**
	 * @covers GollumSF\CacheBundle\Cache\ApcuCache::has                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           he\ApcuCache::has
	 * @dataProvider provide
	 * @depends testSet
	 */
	public function testNotHas($key, $value) {
		parent::testNotHas($key, $value);
	}
	
	/**
	 * @covers GollumSF\CacheBundle\Cache\ApcuCache::delete
	 * @dataProvider provide
	 * @depends testSet
	 * @depends testHas
	 * @depends testNotHas
	 */
	public function testDelete($key, $value) {
		parent::testDelete($key, $value);
	}
	
	/**
	 * @covers GollumSF\CacheBundle\Cache\ApcuCache::getKeys
	 * @dataProvider provideKeys
	 * @depends testHas
	 * @depends testNotHas
	 */
	public function testGetKeys($data) {
		parent::testGetKeys($data);
	}
	
	/**
	 * @covers GollumSF\CacheBundle\Cache\ApcCache::deleteAll
	 * @dataProvider provideKeys
	 * @depends testSet
	 * @depends testGetKeys
	 */
	public function testDeleteAll($data) {
		parent::testGetKeys($data);
	}
	
	/**
	 * @covers GollumSF\CacheBundle\Cache\ApcuCache::getFinalKey
	 * @dataProvider provide
	 */
	public function testGetFinalKey($key) {
		parent::testGetFinalKey($key);
	}
	
}