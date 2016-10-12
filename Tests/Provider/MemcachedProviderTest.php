<?php
namespace GollumSF\CacheBundle\Tests\Provider;

use GollumSF\CacheBundle\Provider\MemcachedProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

require_once __DIR__.'/../PHPModule/memcached.php';

class MemcachedDummy {
	
	public $servers = [];
	public $options = [];
	public $username = NULL;
	public $password = NULL;
	
	public function __construct($id) {
	}
	
	public function setOption($key, $value) {
		if ($key === NULL) {
			throw new \Exception('$key must be not NULL');
		}
		$this->options[$key] = $value;
	}
	
	public function addServer($host, $port, $weight = 0) {
		if ($host === NULL) {
			throw new \Exception('$host must be not NULL');
		}
		if ($port === NULL) {
			throw new \Exception('$port must be not NULL');
		}
		if ($weight === NULL) {
			throw new \Exception('$weight must be not NULL');
		}
		$this->servers[] = [ $host, $port, $weight ];
	}
	
	public function setSaslAuthData($username, $password){
		if ($username === NULL) {
			throw new \Exception('$username must be not NULL');
		}
		$this->username = $username;
		$this->password = $password;
	}
	
}

/**
 * MemcachedProviderTest
 *
 * @author Damien Duboeuf <smeagolworms4@gmail.com>
 */
class MemcachedProviderTest extends WebTestCase {
	
	public function provideAddServers() {
		return [
			[
				[
					'servers' => [ 'server1' => [ 'host' => '127.0.0.1', 'port' => 1234 ] ]
				],
				[ [ "127.0.0.1" , 1234 , 0  ], ],
			],
			[
				[
					'servers' => [ 'server1' => [ 'host' => '127.0.0.1', 'port' => 1234 ], 'server2' => [ 'host' => 'server2', 'port' => 2345, 'weight' => 20 ] ]
				],
				[ [ "127.0.0.1" , 1234 , 0  ], [ "server2" , 2345 , 20 , ], ],
			],
			[
				[
					'servers' => [ 'server1' => [ 'host' => '127.0.0.1', 'port' => 1234 ] ],
					'username' => 'username',
					'password' => 'password',
					'compression' => true,
				],
				[ [ "127.0.0.1" , 1234 , 0  ], ],
			],
			[
				[
					'username' => 'username',
					'password' => 'password',
					'compression' => true,
				],
				[],
			],
		];
	}
	
	public function provideAuthentification() {
		return [
			[
				[
					'username' => 'username',
					'password' => 'password',
				],
				'username',
				'password',
			],
			[
				[
					'username' => 'username',
				],
				'username',
				'',
			],
			[
				[
					'servers' => [ 'server1' => [ 'host' => '127.0.0.1', 'port' => 1234 ] ]
				],
				NULL,
				NULL,
			],
			[
				[
					'servers' => [ 'server1' => [ 'host' => '127.0.0.1', 'port' => 1234 ] ],
					'username' => 'username',
					'password' => 'password',
					'compression' => true,
				],
				'username',
				'password',
			],
			[
				[
					'username' => 'username',
					'password' => 'password',
					'compression' => true,
				],
				'username',
				'password',
			],
		];
	}
	
	public function provideConvertMemcachedOption() {
		return [
			[ 'compression'         , \Memcached::OPT_COMPRESSION          ], [ 'retry_timeout'         , \Memcached::OPT_RETRY_TIMEOUT          ],
			[ 'connect_timeout'     , \Memcached::OPT_CONNECT_TIMEOUT      ], [ 'send_timeout'          , \Memcached::OPT_SEND_TIMEOUT           ],
			[ 'prefix_key'          , \Memcached::OPT_PREFIX_KEY           ], [ 'recv_timeout'          , \Memcached::OPT_RECV_TIMEOUT           ],
			[ 'serializer'          , \Memcached::OPT_SERIALIZER           ], [ 'poll_timeout'          , \Memcached::OPT_POLL_TIMEOUT           ],
			[ 'hash'                , \Memcached::OPT_HASH                 ], [ 'cache_lookups'         , \Memcached::OPT_CACHE_LOOKUPS          ],
			[ 'distribution'        , \Memcached::OPT_DISTRIBUTION         ], [ 'server_failure_limit'  , \Memcached::OPT_SERVER_FAILURE_LIMIT   ],
			[ 'libketama_compatible', \Memcached::OPT_LIBKETAMA_COMPATIBLE ], 
			[ 'buffer_writes'       , \Memcached::OPT_BUFFER_WRITES        ], [ 'sort_hosts'            , \Memcached::OPT_SORT_HOSTS             ],
			[ 'binary_protocol'     , \Memcached::OPT_BINARY_PROTOCOL      ], [ 'verify_key'            , \Memcached::OPT_VERIFY_KEY             ],
			[ 'no_block'            , \Memcached::OPT_NO_BLOCK             ], 
			[ 'tcp_nodelay'         , \Memcached::OPT_TCP_NODELAY          ], 
			[ 'socket_send_size'    , \Memcached::OPT_SOCKET_SEND_SIZE     ], [ 'randomize_replica_read', \Memcached::OPT_RANDOMIZE_REPLICA_READ ],
			[ 'socket_recv_size'    , \Memcached::OPT_SOCKET_RECV_SIZE     ], [ 'remove_failed_servers' , \Memcached::OPT_REMOVE_FAILED_SERVERS  ],
		];
		
	}
	
	public function provideGetMemcached() {
		return [
			[ 
				[
					'servers' => [
						'server1' => [
							'host' => '127.0.0.1',
							'port' => 1234,
						]
					],
					'compression'      => 1,
					'compression_type' => 2,
					'username'         => 'username',
					'password'         => 'password',
				],
				[ [ "127.0.0.1" , 1234 , 0  ] ],
				'username',
				'password',
				[
					\Memcached::OPT_COMPRESSION      => 1,
					\Memcached::OPT_COMPRESSION_TYPE => 2,
				]
			]
		];
		
	}
	
	/**
	 * @covers GollumSF\CacheBundle\Provider\MemcachedProvider::addServers
	 * @dataProvider provideAddServers
	 */
	public function testAddServers($options, $result) {
		
		$memcached = new MemcachedDummy('dummy');
		$memcachedProvider = new MemcachedProvider(MemcachedDummy::class);
		$rClass = new \Reflectionclass($memcachedProvider);
		$method = $rClass->getMethod('addServers');
		$method->setAccessible(true);
		
		$method->invoke($memcachedProvider, $memcached, $options);
		
		$this->assertEquals($memcached->servers, $result);
	}
	
	/**
	 * @covers GollumSF\CacheBundle\Provider\MemcachedProvider::authentification
	 * @dataProvider provideAuthentification
	 */
	public function testAuthentification($options, $username, $password) {
		
		$memcached = new MemcachedDummy('dummy');
		$memcachedProvider = new MemcachedProvider(MemcachedDummy::class);
		$rClass = new \Reflectionclass($memcachedProvider);
		$method = $rClass->getMethod('authentification');
		$method->setAccessible(true);
		
		$method->invoke($memcachedProvider, $memcached, $options);
		
		$this->assertEquals($memcached->username, $username);
		$this->assertEquals($memcached->password, $password);
	}
	
	/**
	 * @covers GollumSF\CacheBundle\Provider\MemcachedProvider::convertMemcachedOption
	 * @dataProvider provideConvertMemcachedOption
	 */
	public function testConvertMemcachedOption($option, $result) {
		$memcachedProvider = new MemcachedProvider(MemcachedDummy::class);
		$rClass = new \ReflectionClass($memcachedProvider);
		$method = $rClass->getMethod('convertMemcachedOption');
		$method->setAccessible(true);
		$this->assertEquals($method->invoke($memcachedProvider, $option), $result);
	}
	
	/**
	 * @covers GollumSF\CacheBundle\Provider\MemcachedProvider::convertMemcachedOption
	 * @dataProvider provideConvertMemcachedOption
	 * @expectedException GollumSF\CacheBundle\Exception\MemcachedOptionException
	 */
	public function testConvertMemcachedOptionException($option, $result) {
		$memcachedProvider = new MemcachedProvider(MemcachedDummy::class);
		$rClass = new \ReflectionClass($memcachedProvider);
		$method = $rClass->getMethod('convertMemcachedOption');
		$method->setAccessible(true);
		$method->invoke($memcachedProvider, NULL);
	}
	
	/**
	 * @covers GollumSF\CacheBundle\Provider\MemcachedProvider::getMemcached
	 * @dataProvider provideGetMemcached
	 */
	public function testGetMemcached($options, $servers, $username, $password, $memcachedOptions) {
		
		
		$memcachedProvider = new MemcachedProvider(MemcachedDummy::class, 'dummy', $options);
		
		/** @var MemcachedDummy $memcached */
		$memcached = $memcachedProvider->getMemcached();
		$this->assertInstanceOf(MemcachedDummy::class, $memcached);
		
		$this->assertEquals($memcached->servers , $servers);
		$this->assertEquals($memcached->username, $username);
		$this->assertEquals($memcached->password, $password);
		$this->assertEquals($memcached->options , $memcachedOptions);
	}
	
}