<?php

namespace rs\GaufretteBrowserBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use rs\GaufretteBrowserBundle\Entity\Directory;
use rs\GaufretteBrowserBundle\Entity\DirectoryRepository;
use rs\GaufretteBrowserBundle\Entity\File;
use rs\GaufretteBrowserBundle\Entity\FileRepository;
use rs\GaufretteBrowserBundle\Event\FileControllerEvent;
use rs\GaufretteBrowserBundle\Event\GaufretteBrowserEvents;

class FileSubscriber implements EventSubscriberInterface
{
    /**
     * @var FileRepository
     */
    private $fileRepository;

    /**
     * @var DirectoryRepository
     */
    private $directoryRepository;

    public function __construct(DirectoryRepository $directoryRepository, FileRepository $fileRepository)
    {
        $this->directoryRepository = $directoryRepository;
        $this->fileRepository = $fileRepository;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            GaufretteBrowserEvents::FILE_SHOW => array(
                array('setRelations', -255),
                array('setShowVariables')
            ),
            GaufretteBrowserEvents::FILE_FETCH => array(
                array('setRelations', -255),
            )
        );
    }

    /**
     * set the file and directory relations
     *
     * @param FileControllerEvent $event
     */
    public function setRelations(FileControllerEvent $event)
    {
        foreach ($event->getFiles() as $file) {
            /** @var $file File */
            $file->setDirectory($this->directoryRepository->find(pathinfo($file->getKey(), PATHINFO_DIRNAME)));
        }
    }

    /**
     * add default vars for the show template
     * @param FileControllerEvent $event
     */
    public function setShowVariables(FileControllerEvent $event)
    {
        $files = $event->getFiles();
        $file = array_pop($files);
        /** @var $file File */

        $event->addTemplateData('file', $file);
    }
}
