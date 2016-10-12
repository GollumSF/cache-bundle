# GollumSFCacheBundle

[![Build Status](https://travis-ci.org/GollumSF/cache-bundle.svg?branch=master)](https://travis-ci.org/GollumSF/cache-bundle)
[![License](https://poser.pugx.org/gollumsf/cache-bundle/license)](https://packagist.org/packages/gollumsf/cache-bundle)
[![Latest Stable Version](https://poser.pugx.org/gollumsf/cache-bundle/v/stable)](https://packagist.org/packages/gollumsf/cache-bundle)
[![Latest Unstable Version](https://poser.pugx.org/gollumsf/cache-bundle/v/unstable)](https://packagist.org/packages/gollumsf/cache-bundle)


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

