<?php

namespace rs\GaufretteBrowserBundle\Controller;

use rs\GaufretteBrowserBundle\Event\GaufretteBrowserEvents;
use rs\GaufretteBrowserBundle\Event\DirectoryControllerEvent;

class DirectoryController extends BaseController
{
    public function indexAction()
    {
        $event = new DirectoryControllerEvent($this->getDirectoryRepository()->findBy(array('prefix'=>''), array('mtime'=>'DESC')));
        $event = $this->get('event_dispatcher')->dispatch(GaufretteBrowserEvents::DIRECTORY_INDEX, $event);

        return $this->render('rsGaufretteBrowserBundle:Directory:index.html.twig', $event->getTemplateData());
    }

    public function showAction($slug)
    {
        if (!$directory = $this->getDirectoryRepository()->find($slug)) {
            throw $this->createNotFoundException('directory not found '.$slug);
        }

        $event = new DirectoryControllerEvent($directory);
        $event = $this->get('event_dispatcher')->dispatch(GaufretteBrowserEvents::DIRECTORY_SHOW, $event);

        return $this->render('rsGaufretteBrowserBundle:Directory:show.html.twig', $event->getTemplateData());
    }
}
