<?php

namespace rs\GaufretteBrowserBundle\Tests\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use rs\GaufretteBrowserBundle\DependencyInjection\Configuration;

/**
 * @covers rs\GaufretteBrowserBundle\DependencyInjection\Configuration
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider configProvider
     */
    public function testConfig($input, $output)
    {
        $input = json_decode(file_get_contents($input), true);
        $output = json_decode(file_get_contents($output), true);

        $processor = new Processor();
        $expectedOutput = $processor->processConfiguration(new Configuration(), $input);

        $this->assertEquals($output, $expectedOutput);
    }

    public function configProvider()
    {
        $path = __DIR__.'/../fixtures/';

        return array(
            array($path.'input_01.json', $path.'output_01.json'),
            array($path.'input_02.json', $path.'output_02.json')
        );
    }
}
