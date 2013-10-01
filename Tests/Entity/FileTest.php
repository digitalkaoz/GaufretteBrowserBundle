<?php

namespace rs\GaufretteBrowserBundle\Tests\Entity;

use rs\GaufretteBrowserBundle\Entity\Directory;
use rs\GaufretteBrowserBundle\Entity\File;

/**
 * @covers rs\GaufretteBrowserBundle\Entity\File
 */
class FileTest extends \PHPUnit_Framework_TestCase
{
    /*
     * @var File
     */
    private $file;

    /**
     * @var \Gaufrette\File
     */
    private $gaufretteFile;

    public function setUp()
    {
        parent::setUp();

        $this->gaufretteFile = $this->getMockBuilder('Gaufrette\File')->disableOriginalConstructor()->getMock();
        $this->file = new File($this->gaufretteFile);
    }

    public function testDirectory()
    {
        $dir = new Directory($this->gaufretteFile);
        $this->file->setDirectory($dir);

        $this->assertSame($dir, $this->file->getDirectory());
    }

    public function testSlug()
    {
        $this->gaufretteFile->expects($this->atLeastOnce())->method('getName')->will($this->returnValue('foo/ bar.png'));

        $this->assertEquals('foo-bar.png', $this->file->getSlug());
    }

    public function testCallInterception()
    {
        $this->gaufretteFile->expects($this->atLeastOnce())->method('getName')->will($this->returnValue('foo/bar.png'));
        $this->gaufretteFile->expects($this->atLeastOnce())->method('getSize')->will($this->returnValue(1337));
        $this->gaufretteFile->expects($this->atLeastOnce())->method('setName');

        $this->assertEquals('foo/bar.png', $this->file->name());
        $this->assertEquals(1337, $this->file->getSize());
        $this->file->setName('foo');
    }
}
