<?php
namespace rs\GaufretteBrowserBundle\Tests\ParamConverter;

use rs\GaufretteBrowserBundle\Entity\Directory;
use rs\GaufretteBrowserBundle\Entity\File;
use rs\GaufretteBrowserBundle\Entity\GaufretteRepository;
use rs\GaufretteBrowserBundle\ParamConverter\DirectoryParamConverter;
use rs\GaufretteBrowserBundle\ParamConverter\FileParamConverter;
use rs\GaufretteBrowserBundle\ParamConverter\GaufretteParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers rs\GaufretteBrowserBundle\ParamConverter\DirectoryParamConverter<extended>
 * @covers rs\GaufretteBrowserBundle\ParamConverter\FileParamConverter<extended>
 */
class GaufretteParamConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider converterProvider
     */
    public function testSupports(GaufretteParamConverter $converter, GaufretteRepository $repository, $object)
    {
        $wrongConfig = $this->getMock('Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface');
        $config = new ParamConverter(array());

        $this->assertFalse($converter->supports($wrongConfig));
        $this->assertFalse($converter->supports($config));

        $config->setClass(get_class($object));
        $this->assertFalse($converter->supports($config));

        $config->setOptions(array('id' => 'slug'));
        $this->assertTrue($converter->supports($config));
    }

    /**
     * @dataProvider converterProvider
     */
    public function testApplySuccess(GaufretteParamConverter $converter, GaufretteRepository $repository, $object)
    {
        $config = new ParamConverter(array());
        $config->setClass(get_class($object));
        $config->setOptions(array('id' => 'slug'));
        $config->setName('foo');

        $request = new Request(array('slug' => 'foo-bar'));

        $repository->expects($this->atLeastOnce())->method('findOneBy')->with(array('slug' => 'foo-bar'))->will($this->returnValue($object));

        $this->assertTrue($converter->apply($request, $config));
        $this->assertSame($object, $request->attributes->get('foo'));
    }

    /**
     * @dataProvider converterProvider
     */
    public function testApplyFailure(GaufretteParamConverter $converter, GaufretteRepository $repository, $object)
    {
        $config = new ParamConverter(array());
        $config->setClass(get_class($object));
        $config->setOptions(array('id' => 'slug'));
        $config->setName('foo');

        $request = new Request(array('slug' => 'foo/bar'));

        $repository->expects($this->atLeastOnce())->method('find')->with('foo/bar')->will($this->returnValue(null));

        $this->assertFalse($converter->apply($request, $config));
        $this->assertEmpty($request->attributes->get('foo'));
    }

    public function converterProvider()
    {
        $dr = $this->getMockBuilder('rs\GaufretteBrowserBundle\Entity\DirectoryRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $fr = $this->getMockBuilder('rs\GaufretteBrowserBundle\Entity\DirectoryRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $directory = new Directory($this->getMockBuilder('Gaufrette\File')->disableOriginalConstructor()->getMock());
        $file = new File($this->getMockBuilder('Gaufrette\File')->disableOriginalConstructor()->getMock());

        return array(
            array(new DirectoryParamConverter($dr, 'rs\GaufretteBrowserBundle\Entity\Directory'), $dr, $directory),
            array(new FileParamConverter($fr, 'rs\GaufretteBrowserBundle\Entity\File'), $fr, $file),
        );
    }
}
