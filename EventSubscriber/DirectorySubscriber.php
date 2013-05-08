<?php

namespace rs\GaufretteBrowserBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use rs\GaufretteBrowserBundle\Entity\Directory;
use rs\GaufretteBrowserBundle\Entity\DirectoryRepository;
use rs\GaufretteBrowserBundle\Entity\FileRepository;
use rs\GaufretteBrowserBundle\Event\DirectoryControllerEvent;
use rs\GaufretteBrowserBundle\Event\GaufretteBrowserEvents;

class DirectorySubscriber implements EventSubscriberInterface
{
    /**
     * @var FileRepository
     */
    private $fileRepository;

    /**
     * @var DirectoryRepository
     */
    private $directoryRepository;

    private $filePattern;

    public function __construct(DirectoryRepository $directoryRepository, FileRepository $fileRepository, $filePattern = null)
    {
        $this->directoryRepository = $directoryRepository;
        $this->fileRepository = $fileRepository;
        $this->filePattern = $filePattern;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            GaufretteBrowserEvents::DIRECTORY_INDEX => array(
                array('setIndexVariables')
            ),
            GaufretteBrowserEvents::DIRECTORY_SHOW => array(
                array('setShowVariables')
            ),
            GaufretteBrowserEvents::DIRECTORY_FETCH => array(
                array('setRelations', -255)
            )
        );
    }

    /**
     * set the file and directory relations
     *
     * @param DirectoryControllerEvent $event
     */
    public function setRelations(DirectoryControllerEvent $event)
    {
        foreach ($event->getDirectories() as $folder) {
            /** @var $folder Directory */
            $folder->setDirectories($this->directoryRepository->findBy(array(
                'prefix' => $folder->getName() . DIRECTORY_SEPARATOR,
            )));

            $folder->setFiles($this->fileRepository->findBy(array(
                'prefix' => $folder->getName() . DIRECTORY_SEPARATOR,
                'suffix' => $this->filePattern
            )));
        }

    }

    /**
     * add default vars for the index template
     * @param DirectoryControllerEvent $event
     */
    public function setIndexVariables(DirectoryControllerEvent $event)
    {
        $event->addTemplateData('folders', $event->getDirectories());
    }

    /**
     * add default vars for the show template
     * @param DirectoryControllerEvent $event
     */
    public function setShowVariables(DirectoryControllerEvent $event)
    {
        $folders = $event->getDirectories();
        $folder = array_pop($folders);
        /** @var $folder Directory */

        $event->addTemplateData('folder', $folder);
        $event->addTemplateData('folders', $folder->getDirectories());
    }
}