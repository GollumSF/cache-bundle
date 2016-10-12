<?php
namespace GollumSF\CacheBundle\Tests\Serializer;

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
 * SimpleSerializerTest
 *
 * @author Damien Duboeuf <smeagolworms4@gmail.com>
 */
class SimpleSerializerTest extends WebTestCase {
	
	/**@
	 * @var SimpleSerializer
	 */
	protected $serializer;
	
	public function setUp() {
		$this->serializer = new SimpleSerializer();
	}
	
	public function provide() {
		return [
			[ false                                , 'b:0;' ],
			[ true                                 , 'b:1;' ],
			[ 1                                    , 'i:1;' ],
			[ 2.5                                  , 'd:2.5;' ],
			[ '3'                                  , 's:1:"3";' ],
			[ 'three'                              , 's:5:"three";' ],
			[ new DummyObject(1, '2', 'three', 4.0), "O:49:\"".DummyObject::class."\":4:{s:52:\"\x00".DummyObject::class."\x00a\";i:1;s:52:\"\x00".DummyObject::class."\x00b\";s:1:\"2\";s:52:\"\x00".DummyObject::class."\x00c\";s:5:\"three\";s:52:\"\x00".DummyObject::class."\x00d\";d:4;}" ],
		];
	}
	
	/**
	 * @covers GollumSF\CacheBundle\Serializer\SimpleSerializer::serialize
	 * @dataProvider provide
	 */
	public function testsSerialize($value, $serialized) {
		$this->assertEquals($this->serializer->serialize($value), $serialized);
	}
	
	/**
	 * @covers GollumSF\CacheBundle\Serializer\SimpleSerializer::unserialize
	 * @dataProvider provide
	 */
	public function testsUnserialize($value, $serialized) {
		$this->assertEquals($value, $this->serializer->unserialize($serialized));
		if (is_object($value)) {
			$this->assertEquals(get_class($value), get_class($this->serializer->unserialize($serialized)));
			$this->assertTrue($value->compare($this->serializer->unserialize($serialized)));
		} else {
			$this->assertEquals(gettype($value), gettype($this->serializer->unserialize($serialized)));
		}
	}
	
}