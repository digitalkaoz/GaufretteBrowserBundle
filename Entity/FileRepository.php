<?php

namespace rs\GaufretteBrowserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use rs\GaufretteBrowserBundle\Event\FileControllerEvent;
use rs\GaufretteBrowserBundle\Event\GaufretteBrowserEvents;

class FileRepository extends GaufretteRepository
{

    protected function invokeEvent($files)
    {
        return $files;

        //1000 nesting level reached
        /*$event = $this->eventDispatcher->dispatch(GaufretteBrowserEvents::FILE_FETCH, new FileControllerEvent($files));

        if ($files instanceof Collection) {
            return new ArrayCollection($event->getFiles());
        } else {
            return array_pop($event->getFiles());
        }*/
    }
}