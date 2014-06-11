<?php
namespace rs\GaufretteBrowserBundle\Tests\Event;

use Doctrine\Common\Collections\ArrayCollection;
use rs\GaufretteBrowserBundle\Entity\Directory;
use rs\GaufretteBrowserBundle\Event\DirectoryControllerEvent;

/**
 * @covers rs\GaufretteBrowserBundle\Event\DirectoryControllerEvent
 * @covers rs\GaufretteBrowserBundle\Entity\Directory
 */
class DirectoryControllerEventTest extends \PHPUnit_Framework_TestCase
{
    public function testDirectories()
    {
        //add by constructor
        $e = new DirectoryControllerEvent($this->generateDir());
        $this->assertCount(1, $e->getDirectories());
        $this->assertInternalType('array', $e->getDirectories());

        //add single object
        $e->addDirectory($this->generateDir());
        $this->assertCount(2, $e->getDirectories());

        //add from array
        $e->addDirectory(array($this->generateDir(),$this->generateDir()));
        $this->assertCount(4, $e->getDirectories());

        //add from array-collection
        $e->addDirectory(new ArrayCollection(array($this->generateDir(),$this->generateDir())));
        $this->assertCount(6, $e->getDirectories());

        //set
        $dir = $this->generateDir();
        $e->setDirectories(array($dir));
        $this->assertCount(1, $e->getDirectories());

        //remove
        $e->removeDirectory($dir);
        $this->assertCount(0, $e->getDirectories());
    }

    public function testTemplateData()
    {
        $e = new DirectoryControllerEvent();
        $this->assertInternalType('array', $e->getTemplateData());
        $this->assertEmpty($e->getTemplateData());

        $e->addTemplateData('foo','bar');
        $this->assertNotEmpty($e->getTemplateData());
        $this->assertArrayHasKey('foo', $e->getTemplateData());
    }

    private function generateDir()
    {
        $gaufretteFile = $this->getMockBuilder('Gaufrette\File')->disableOriginalConstructor()->getMock();

        return new Directory($gaufretteFile);
    }
}
