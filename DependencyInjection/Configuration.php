<?php

namespace rs\GaufretteBrowserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('rs_gaufrette_browser')
            ->children()
                ->scalarNode('file_class')
                    ->info('The file class to use')
                    ->cannotBeEmpty()
                    ->defaultValue('rs\GaufretteBrowserBundle\Entity\File')
                    ->validate()
                        ->ifTrue(function($v) {
                            return !class_exists($v);
                        })
                        ->thenInvalid('file_class couldnt be autoloaded')
                    ->end()
                ->end()

                ->scalarNode('directory_class')
                    ->info('The directory class to use')
                    ->cannotBeEmpty()
                    ->defaultValue('rs\GaufretteBrowserBundle\Entity\Directory')
                    ->validate()
                        ->ifTrue(function($v) {
                            return !class_exists($v);
                        })
                        ->thenInvalid('directory_class couldnt be autoloaded')
                    ->end()
                ->end()

                ->scalarNode('file_pattern')
                    ->info('the file pattern to filter, must be a valid regex')
                    ->defaultNull()
                ->end()

                ->scalarNode('filesystem')
                    ->info('the gaufrette filesystem to use')
                    ->cannotBeEmpty()
                    ->isRequired()
                ->end()

            ->end()
        ;

        return $treeBuilder;
    }
}
