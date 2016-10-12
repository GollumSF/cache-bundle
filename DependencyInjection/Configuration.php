<?php
namespace GollumSF\CacheBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration
 * @author Damien Duboeuf <smeagolworms4@gmail.com>
 */
class Configuration implements ConfigurationInterface {
	/**
	 * {@inheritDoc}
	 */
	public function getConfigTreeBuilder() {
		$treeBuilder = new TreeBuilder();
		$treeBuilder->root('gollum_sf_cache');
		
		return $treeBuilder;
	}
}
