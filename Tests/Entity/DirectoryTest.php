<?php

namespace rs\GaufretteBrowserBundle\Tests\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use rs\GaufretteBrowserBundle\Entity\Directory;
use rs\GaufretteBrowserBundle\Entity\File;

/**
 * @covers rs\GaufretteBrowserBundle\Entity\Directory
 */
class DirectoryTest extends \PHPUnit_Framework_TestCase
{
    /*
     * @var Directory
     */
    private $directory;

    /**
     * @var \Gaufrette\File
     */
    private $gaufretteFile;

    public function setUp()
    {
        parent::setUp();

        $this->gaufretteFile = $this->getMockBuilder('Gaufrette\File')->disableOriginalConstructor()->getMock();
        $this->directory = new Directory($this->gaufretteFile);
    }

    public function testFiles()
    {
        $files = new ArrayCollection(array(new File($this->gaufretteFile)));
        $this->directory->setFiles($files);

        $this->assertSame($files, $this->directory->getFiles());
    }

    public function testDirectories()
    {
        $dirs = new ArrayCollection(array(new Directory($this->gaufretteFile)));
        $this->directory->setDirectories($dirs);

        $this->assertSame($dirs, $this->directory->getDirectories());
    }

    public function testParent()
    {
        $parent = new Directory($this->gaufretteFile);
        $this->directory->setParent($parent);

        $this->assertSame($parent, $this->directory->getParent());
    }

    public function testSlug()
    {
        $this->gaufretteFile->expects($this->atLeastOnce())->method('getName')->will($this->returnValue('foo/ bar.png'));

        $this->assertEquals('foo/_bar.png', $this->directory->getSlug());
    }

    public function testCallInterception()
    {
        $this->gaufretteFile->expects($this->atLeastOnce())->method('getName')->will($this->returnValue('foo/bar.png'));
        $this->gaufretteFile->expects($this->atLeastOnce())->method('getSize')->will($this->returnValue(1337));
        $this->gaufretteFile->expects($this->atLeastOnce())->method('setName');

        $this->assertEquals('foo/bar.png', $this->directory->name());
        $this->assertEquals(1337, $this->directory->getSize());
        $this->directory->setName('foo');
    }
}
