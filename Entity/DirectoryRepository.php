<?php

namespace rs\GaufretteBrowserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use rs\GaufretteBrowserBundle\Event\DirectoryControllerEvent;
use rs\GaufretteBrowserBundle\Event\GaufretteBrowserEvents;

class DirectoryRepository extends GaufretteRepository
{
    protected function invokeEvent($directories)
    {
        $event = $this->eventDispatcher->dispatch(GaufretteBrowserEvents::DIRECTORY_FETCH, new DirectoryControllerEvent($directories));

        if ($directories instanceof Collection) {
            return new ArrayCollection($event->getDirectories());
        } else {
            $directories = $event->getDirectories();

            return array_pop($directories);
        }
    }
}
