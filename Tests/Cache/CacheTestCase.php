<?php
namespace GollumSF\CacheBundle\Tests\Cache;

use GollumSF\CacheBundle\Cache\AbstractCache;
use GollumSF\CacheBundle\Serializer\SimpleSerializer;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DummyObject {
	
	private $a;
	private $b;
	private $c;
	private $d;
	
	public function __construct($a, $b, $c, $d) {
		$this->a = $a;
		$this->b = $b;
		$this->c = $c;
		$this->d = $d;
	}
	
	public function compare($object) {
		return
			$object instanceof DummyObject &&
			$this->a = $object->a &&
			$this->b = $object->b &&
			$this->c = $object->c &&
			$this->d = $object->d
		;
	}
}

/**
 * CacheTestCase
 *
 * @author Damien Duboeuf <smeagolworms4@gmail.com>
 */
class CacheTestCase extends WebTestCase {
	
	const NS_PREFIX = 'GSF_CACHE_TEST_';
	
	/**@
	 * @var SimpleSerializer
	 */
	protected $serializer;
	
	/**
	 * @var AbstractCache
	 */
	protected $cache;
	
	/**
	 * @var string
	 */
	protected $ns;
	
	public function setUp() {
		$this->serializer = new SimpleSerializer();
		$this->ns = self::NS_PREFIX.uniqid();
	}
	
	public function provide() {
		return [
			[ 'key1', false   ],
			[ 'key2', true    ],
			[ 'key3', 3       ],
			[ 'key4', 4.5     ],
			[ 'key5', '5'     ],
			[ 'key6', 'six'   ],
			[ 'key7', new DummyObject(1, '2', 'three', 4.0) ],
		];
	}
	
	public function provideKeys() {
		return [
			[ [
				'key1' => false,
				'key2' => true ,
				'key3' => 3    ,
				'key4' => 4.5  ,
				'key5' => '5'  ,
				'key6' => 'six',
				'key7' => new DummyObject(1, '2', 'three', 4.0),
			] ],
		];
	}
	
	public function testGet($key, $value) {
		$this->cache->set($key, $value);
		
		$getted = $this->cache->get($key);
		
		$this->assertEquals($value, $getted);
		if (is_object($value)) {
			$this->assertEquals(get_class($value), get_class($getted));
			$this->assertTrue($value->compare($getted));
		} else {
			$this->assertEquals(gettype($value), gettype($getted));
		}
	}
	
	public function testGetExpired($key, $value) {
		$this->cache->set($key, $value, 1);
		sleep(2);
		$this->assertEquals($this->cache->get($key), NULL);
	}
	
	public function testGetDefaultValue() {
		$this->assertEquals($this->cache->get('UNEXIST_VALUE', 'DEFAULT_VALUE'), 'DEFAULT_VALUE');
	}
	
	public function testHas($key, $value) {
		$this->cache->set($key, $value);
		$this->assertTrue($this->cache->has($key));
	}
	
	public function testHasExpired($key, $value) {
		$this->cache->set($key, $value, 1);
		sleep(2);
		$this->assertFalse($this->cache->has($key));
	}
	
	public function testNotHas($key, $value) {
		$this->cache->set($key, $value);
		$this->assertFalse($this->cache->has($key.'_notExist'));
	}
	
	public function testDelete($key, $value) {
		$this->cache->set($key, $value);
		$this->cache->delete($key, $value);
		$this->assertFalse($this->cache->has($key));
	}
	
	public function testGetKeys($data) {
		foreach ($data as $key => $value) {
			$this->cache->set($key, $value);
		}
		$keys = $this->cache->getKeys();
		
		foreach ($keys as $i => $key) {
			foreach ($data as $originalKey => $value) {
				if ($originalKey == $key) {
					unset($keys[$i]);
					unset($data[$key]);
					break;
				}
			}
		}
		$this->assertEmpty($data);
		$this->assertEmpty($keys);
	}
	
	public function testDeleteAll($data) {
		foreach ($data as $key => $value) {
			$this->cache->set($key, $value);
		}
		$this->cache->deleteAll();
		$this->assertEquals($this->cache->getKeys(), []);
	}
	
	protected function testGetFinalKey($key) {
		$rClass = new \ReflectionClass($this->cache);
		$method = $rClass->getMethod('getFinalKey');
		$method->setAccessible(true);
		$this->assertEquals($this->ns.'_'.$key, $method->invoke($this->cache, $key));
	}
	
}