<?php

namespace rs\GaufretteBrowserBundle\Tests\EventSubscriber;

use Doctrine\Common\Collections\ArrayCollection;
use rs\GaufretteBrowserBundle\Event\DirectoryControllerEvent;
use rs\GaufretteBrowserBundle\Event\GaufretteBrowserEvents;
use rs\GaufretteBrowserBundle\EventSubscriber\DirectorySubscriber;

/**
 * @covers rs\GaufretteBrowserBundle\EventSubscriber\DirectorySubscriber
 */
class DirectorySubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DirectorySubscriber
     */
    private $subscriber;

    public function setUp()
    {
        parent::setUp();

        $this->dr = $this->getMockBuilder('rs\GaufretteBrowserBundle\Entity\DirectoryRepository')
            ->disableOriginalConstructor()
            ->setMethods(array('findBy'))
            ->getMock();

        $this->fr = $this->getMockBuilder('rs\GaufretteBrowserBundle\Entity\FileRepository')
            ->disableOriginalConstructor()
            ->setMethods(array('findBy'))
            ->getMock();

        $this->subscriber = new DirectorySubscriber($this->dr, $this->fr, '');
    }

    public function testSubscribedEvents()
    {
        $events = $this->subscriber->getSubscribedEvents();

        $this->assertInternalType('array', $events);
        $this->assertCount(3, $events);
        $this->assertArrayHasKey(GaufretteBrowserEvents::DIRECTORY_INDEX, $events);
        $this->assertArrayHasKey(GaufretteBrowserEvents::DIRECTORY_SHOW, $events);
        $this->assertArrayHasKey(GaufretteBrowserEvents::DIRECTORY_FETCH, $events);
    }

    public function testSetRelations()
    {
        $this->dr->expects($this->atLeastOnce())->method('findBy')->will($this->returnValue(new ArrayCollection()));
        $this->fr->expects($this->atLeastOnce())->method('findBy')->will($this->returnValue(new ArrayCollection()));

        $directory = $this->getMockBuilder('rs\GaufretteBrowserBundle\Entity\Directory')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $directory->expects($this->atLeastOnce())->method('setDirectories')->with($this->isInstanceOf('Doctrine\Common\Collections\ArrayCollection'));
        $directory->expects($this->atLeastOnce())->method('setFiles')->with($this->isInstanceOf('Doctrine\Common\Collections\ArrayCollection'));

        $event = $this->getMockBuilder('rs\GaufretteBrowserBundle\Event\DirectoryControllerEvent')
            ->setConstructorArgs(array($directory))
            ->setMethods(array('getDirectories'))
            ->getMock()
        ;
        $event->expects($this->atLeastOnce())->method('getDirectories')->will($this->returnValue(array($directory)));

        $this->subscriber->setRelations($event);
    }

    public function testSetIndexVariables()
    {
        $directory = $this->getMockBuilder('rs\GaufretteBrowserBundle\Entity\Directory')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $event = $this->getMockBuilder('rs\GaufretteBrowserBundle\Event\DirectoryControllerEvent')
            ->setConstructorArgs(array($directory))
            ->setMethods(array('getDirectories','addTemplateData'))
            ->getMock()
        ;
        $event->expects($this->atLeastOnce())->method('getDirectories')->will($this->returnValue(array($directory)));
        $event->expects($this->atLeastOnce())->method('addTemplateData')->with('folders', array($directory));

        $this->subscriber->setIndexVariables($event);
    }

    public function testSetShowVariables()
    {
        $directory = $this->getMockBuilder('rs\GaufretteBrowserBundle\Entity\Directory')
            ->disableOriginalConstructor()
            ->setMethods(array('getDirectories'))
            ->getMock()
        ;
        $directory->expects($this->atLeastOnce())->method('getDirectories')->will($this->returnValue(array($directory)));
        $event = new DirectoryControllerEvent($directory);

        $this->subscriber->setShowVariables($event);

        $data = $event->getTemplateData();

        $this->assertInternalType('array', $data);
        $this->assertArrayHasKey('folder', $data);
        $this->assertEquals($data['folder'], $directory);
        $this->assertArrayHasKey('folders', $data);
        $this->assertEquals($data['folders'], array($directory));
    }

}
