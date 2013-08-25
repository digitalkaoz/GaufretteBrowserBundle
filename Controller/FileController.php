<?php

namespace rs\GaufretteBrowserBundle\Controller;

use rs\GaufretteBrowserBundle\Event\FileControllerEvent;
use rs\GaufretteBrowserBundle\Event\GaufretteBrowserEvents;

class FileController extends BaseController
{
    public function showAction($slug)
    {
        if (!$file = $this->getFileRepository()->find($slug)) {
            throw $this->createNotFoundException('file not found '.$slug);
        }

        $event = new FileControllerEvent($file);
        $event = $this->get('event_dispatcher')->dispatch(GaufretteBrowserEvents::FILE_SHOW, $event);

        return $this->render('rsGaufretteBrowserBundle:File:show.html.twig',$event->getTemplateData());
    }
}
