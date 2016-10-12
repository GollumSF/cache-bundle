<?php
namespace GollumSF\CacheBundle\Tests\Cache;

use GollumSF\CacheBundle\Cache\FileCache;

/**
 * FileCacheTest
 *
 * @author Damien Duboeuf <smeagolworms4@gmail.com>
 */
class FileCacheTest extends CacheTestCase {
	
	public function setUp() {
		parent::setUp();
		$this->cache = new FileCache(sys_get_temp_dir(), $this->serializer, $this->ns);
	}
	
	public function tearDown() {
		 if (file_exists(sys_get_temp_dir().DIRECTORY_SEPARATOR.$this->ns.'_'.FileCache::CACHE_FILE)) {
		 	@unlink(sys_get_temp_dir().DIRECTORY_SEPARATOR.$this->ns.'_'.FileCache::CACHE_FILE);
		 }
		 parent::tearDown();
	}
	
	/**
	 * @covers GollumSF\CacheBundle\Cache\FileCache::set
	 * @dataProvider provide
	 */
	public function testSet($key, $value) {
		$this->cache->set($key, $value);
		
		$data = [];
		require sys_get_temp_dir().DIRECTORY_SEPARATOR.$this->ns.'_'.FileCache::CACHE_FILE;
		$this->assertEquals(
			$data[$key]['value'],
			$this->serializer->serialize($value)
		);
	}
	
	/**
	 * @covers GollumSF\CacheBundle\Cache\FileCache::get
	 * @dataProvider provide
	 * @depends testSet
	 */
	public function testGet($key, $value) {
		parent::testGet($key, $value);
	}
	
	/**
	 * @covers GollumSF\CacheBundle\Cache\FileCache::get
	 * @dataProvider provide
	 * @depends testSet
	 */
	public function testGetExpired($key, $value) {
		parent::testGetExpired($key, $value);
	}
	
	/**
	 * @covers GollumSF\CacheBundle\Cache\FileCache::get
	 */
	public function testGetDefaultValue() {
		parent::testGetDefaultValue();
	}
	
	/**
	 * @covers GollumSF\CacheBundle\Cache\FileCache::has
	 * @dataProvider provide
	 * @depends testSet
	 */
	public function testHas($key, $value) {
		parent::testHas($key, $value);
	}
	
	/**
	 * @covers GollumSF\CacheBundle\Cache\FileCache::has
	 * @dataProvider provide
	 * @depends testSet
	 */
	public function testHasExpired($key, $value) {
		parent::testHasExpired($key, $value);
	}
	
	/**
	 * @covers GollumSF\CacheBundle\Cache\FileCache::has
	 * @dataProvider provide
	 * @depends testSet
	 */
	public function testNotHas($key, $value) {
		parent::testNotHas($key, $value);
	}
	
	/**
	 * @covers GollumSF\CacheBundle\Cache\FileCache::delete
	 * @dataProvider provide
	 * @depends testSet
	 * @depends testHas
	 * @depends testNotHas
	 */
	public function testDelete($key, $value) {
		parent::testDelete($key, $value);
	}
	
	/**
	 * @covers GollumSF\CacheBundle\Cache\FileCache::delete
	 * @dataProvider provideKeys
	 * @depends testSet
	 * @depends testHas
	 * @depends testNotHas
	 */
	public function testGetKeys($data) {
		parent::testGetKeys($data);
	}
	
	/**
	 * @covers GollumSF\CacheBundle\Cache\FileCache::deleteAll
	 * @dataProvider provideKeys
	 * @depends testSet
	 * @depends testGetKeys
	 */
	public function testDeleteAll($data) {
		parent::testGetKeys($data);
	}
	
	/**
	 * @covers GollumSF\CacheBundle\Cache\FileCache::getFinalKey
	 * @dataProvider provide
	 */
	public function testGetFinalKey($key) {
		parent::testGetFinalKey($key);
	}
	
}