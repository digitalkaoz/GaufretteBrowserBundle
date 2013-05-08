<?php

namespace rs\GaufretteBrowserBundle\Tests\EventSubscriber;

use Doctrine\Common\Collections\ArrayCollection;
use rs\GaufretteBrowserBundle\Event\FileControllerEvent;
use rs\GaufretteBrowserBundle\Event\GaufretteBrowserEvents;
use rs\GaufretteBrowserBundle\EventSubscriber\FileSubscriber;

/**
 * @covers rs\GaufretteBrowserBundle\EventSubscriber\FileSubscriber
 */
class FileSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FileSubscriber
     */
    private $subscriber;

    public function setUp()
    {
        parent::setUp();

        $this->dr = $this->getMockBuilder('rs\GaufretteBrowserBundle\Entity\DirectoryRepository')
            ->disableOriginalConstructor()
            ->setMethods(array('findBy','find'))
            ->getMock();

        $this->fr = $this->getMockBuilder('rs\GaufretteBrowserBundle\Entity\FileRepository')
            ->disableOriginalConstructor()
            ->setMethods(array('findBy','find'))
            ->getMock();

        $this->subscriber = new FileSubscriber($this->dr, $this->fr);
    }

    public function testSubscribedEvents()
    {
        $events = $this->subscriber->getSubscribedEvents();

        $this->assertInternalType('array', $events);
        $this->assertCount(2, $events);
        $this->assertArrayHasKey(GaufretteBrowserEvents::FILE_SHOW, $events);
        $this->assertArrayHasKey(GaufretteBrowserEvents::FILE_FETCH, $events);
    }

    public function testSetRelations()
    {
        $directory = $this->getMockBuilder('rs\GaufretteBrowserBundle\Entity\Directory')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->dr->expects($this->atLeastOnce())->method('find')->will($this->returnValue($directory));

        $file = $this->getMockBuilder('rs\GaufretteBrowserBundle\Entity\File')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $file->expects($this->atLeastOnce())->method('setDirectory')->with($directory);

        $event = new FileControllerEvent($file);

        $this->subscriber->setRelations($event);
    }

    public function testSetShowVariables()
    {
        $file = $this->getMockBuilder('rs\GaufretteBrowserBundle\Entity\File')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $event = new FileControllerEvent($file);

        $this->subscriber->setShowVariables($event);

        $data = $event->getTemplateData();

        $this->assertInternalType('array', $data);
        $this->assertArrayHasKey('file', $data);
        $this->assertEquals($data['file'], $file);
    }

}
