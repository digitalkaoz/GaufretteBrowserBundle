<?php

namespace rs\GaufretteBrowserBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class rsGaufretteBrowserExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $config = $this->processConfiguration(new Configuration(), $configs);

        //set the file extension filter
        $container->setParameter('rs_gaufrette_browser.file_pattern', $config['file_pattern']);
        $container->setParameter('rs_gaufrette_browser.filesystem', $config['filesystem']);

        //add file/directory class to repositories
        $container->getDefinition('rs_gaufrette_browser.repository.directory')->addArgument(new Reference($config['filesystem']));
        $container->getDefinition('rs_gaufrette_browser.repository.directory')->addArgument($config['directory_class']);
        $container->getDefinition('rs_gaufrette_browser.repository.file')->addArgument(new Reference($config['filesystem']));
        $container->getDefinition('rs_gaufrette_browser.repository.file')->addArgument($config['file_class']);

        //add file/directory class to param converters
        $container->getDefinition('rs_gaufrette_browser.param_converter.directory')->addArgument($config['directory_class']);
        $container->getDefinition('rs_gaufrette_browser.param_converter.file')->addArgument($config['file_class']);

        //add arguments to event listener
        $container->getDefinition('rs_gaufrette_browser.event_subscriber.directory')->addArgument($config['file_pattern']);
    }
}
