# GollumSFCacheBundle

## Installation:

### Composer
```shell
composer require gollumsf/cache-bundle
```

### AppKernel.php
```php
class AppKernel extends Kernel {
	
	public function registerBundles() {
		
		$bundles = [
			
			// [...] //
			
			new GollumSF\CoreBundle\GollumSFCacheBundle(),
			
			// [...] // 
		}
	}
}
```

### config.yml

```yml
gollum_sf_cache:
    
```

## Usages:

```php
$cache = $container->get('gsf_cache.cache')
$cache->set('key', 'Hello World');
echo $cache->get('key'); // Display: Hello World
```

