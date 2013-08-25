<?php
namespace rs\GaufretteBrowserBundle\Tests\Event;

use Doctrine\Common\Collections\ArrayCollection;
use rs\GaufretteBrowserBundle\Entity\File;
use rs\GaufretteBrowserBundle\Event\FileControllerEvent;

/**
 * @covers rs\GaufretteBrowserBundle\Event\FileControllerEvent
 */
class FileControllerEventTest extends \PHPUnit_Framework_TestCase
{
    public function testFiles()
    {
        //add by constructor
        $e = new FileControllerEvent($this->generateFile());
        $this->assertCount(1, $e->getFiles());
        $this->assertInternalType('array', $e->getFiles());

        //add single object
        $e->addFile($this->generateFile());
        $this->assertCount(2, $e->getFiles());

        //add from array
        $e->addFile(array($this->generateFile(),$this->generateFile()));
        $this->assertCount(4, $e->getFiles());

        //add from array-collection
        $e->addFile(new ArrayCollection(array($this->generateFile(),$this->generateFile())));
        $this->assertCount(6, $e->getFiles());

        //set
        $file = $this->generateFile();
        $e->setFiles(array($file));
        $this->assertCount(1, $e->getFiles());

        //remove
        $e->removeFile($file);
        $this->assertCount(0, $e->getFiles());
    }

    public function testTemplateData()
    {
        $e = new FileControllerEvent();
        $this->assertInternalType('array', $e->getTemplateData());
        $this->assertEmpty($e->getTemplateData());

        $e->addTemplateData('foo','bar');
        $this->assertNotEmpty($e->getTemplateData());
        $this->assertArrayHasKey('foo', $e->getTemplateData());
    }

    private function generateFile()
    {
        $gaufretteFile = $this->getMockBuilder('Gaufrette\File')->disableOriginalConstructor()->getMock();

        return new File($gaufretteFile);
    }
}
