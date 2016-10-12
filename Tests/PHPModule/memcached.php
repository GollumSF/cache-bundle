<?php

/**
 * polyfill-memcached for test
 */
if (!class_exists('Memcached')) {
	class Memcached {
		
		const RES_SUCCESS = 0;
		const RES_NOTFOUND = 16;
		
		const OPT_COMPRESSION = -1001;
		const OPT_COMPRESSION_TYPE = -1004;
		const OPT_PREFIX_KEY = -1002;
		const OPT_SERIALIZER = -1003;
		const OPT_HASH = 2;
		const OPT_DISTRIBUTION = 9;
		const OPT_LIBKETAMA_COMPATIBLE = 16;
		const OPT_LIBKETAMA_HASH = 17;
		const OPT_TCP_KEEPALIVE = 32;
		const OPT_BUFFER_WRITES = 10;
		const OPT_BINARY_PROTOCOL = 18;
		const OPT_NO_BLOCK = 0;
		const OPT_TCP_NODELAY = 1;
		const OPT_SOCKET_SEND_SIZE = 4;
		const OPT_SOCKET_RECV_SIZE = 5;
		const OPT_CONNECT_TIMEOUT = 14;
		const OPT_RETRY_TIMEOUT = 15;
		const OPT_SEND_TIMEOUT = 19;
		const OPT_RECV_TIMEOUT = 20;
		const OPT_POLL_TIMEOUT = 8;
		const OPT_CACHE_LOOKUPS = 6;
		const OPT_SERVER_FAILURE_LIMIT = 21;
		const OPT_AUTO_EJECT_HOSTS = 28;
		const OPT_HASH_WITH_PREFIX_KEY = 25;
		const OPT_NOREPLY = 26;
		const OPT_SORT_HOSTS = 12;
		const OPT_VERIFY_KEY = 13;
		const OPT_USE_UDP = 27;
		const OPT_NUMBER_OF_REPLICAS = 29;
		const OPT_RANDOMIZE_REPLICA_READ = 30;
		const OPT_REMOVE_FAILED_SERVERS = 35;
		
	}
}