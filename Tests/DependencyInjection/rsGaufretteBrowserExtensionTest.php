<?php

namespace rs\GaufretteBrowserBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use rs\GaufretteBrowserBundle\DependencyInjection\rsGaufretteBrowserExtension;

/**
 * @covers rs\GaufretteBrowserBundle\DependencyInjection\rsGaufretteBrowserExtension
 */
class rsGaufretteBrowserExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        $extension = new rsGaufretteBrowserExtension();
        $container = new ContainerBuilder();

        $extension->load(json_decode(file_get_contents(__DIR__.'/../fixtures/input_01.json'), true), $container);

        $this->assertTrue($container->hasDefinition('rs_gaufrette_browser.repository.directory'));
        $this->assertTrue($container->hasDefinition('rs_gaufrette_browser.repository.file'));

        $this->assertTrue($container->hasDefinition('rs_gaufrette_browser.param_converter.directory'));
        $this->assertTrue($container->hasDefinition('rs_gaufrette_browser.param_converter.file'));

        $this->assertTrue($container->hasDefinition('rs_gaufrette_browser.event_subscriber.directory'));
    }
}
