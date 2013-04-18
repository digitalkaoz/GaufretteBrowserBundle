<?php

namespace rs\GaufretteBrowserBundle\Event;

use Doctrine\Common\Collections\Collection;
use Symfony\Component\EventDispatcher\Event;
use rs\GaufretteBrowserBundle\Entity\Directory;

class DirectoryControllerEvent extends Event
{
    private $directories = array();

    private $templateData = array();

    public function __construct($directory = null)
    {
        if ($directory) {
            $this->addDirectory($directory);
        }
    }

    public function getDirectories()
    {
        return $this->directories;
    }

    public function removeDirectory(Directory $deleteFolder)
    {
        foreach ($this->directories as $key => $folder) {
            if ($deleteFolder->getKey() == $folder->getKey()) {
                unset($this->directories[$key]);
                break;
            }
        }
    }

    public function addDirectory($folder)
    {
        if ($folder instanceof Collection) {
            $this->directories = array_merge($this->directories, $folder->toArray());
        } elseif ($folder instanceof Directory) {
            $this->directories[] = $folder;
        } elseif (is_array($folder)) {
            $this->directories = array_merge($this->directories, $folder);
        } else {
            throw new \InvalidArgumentException('dont know how to add folder');
        }
    }

    public function setDirectories($folders)
    {
        $this->directories = array();
        $this->addDirectory($folders);
    }

    public function addTemplateData($key, $value)
    {
        $this->templateData[$key] = $value;
    }

    public function getTemplateData()
    {
        return $this->templateData;
    }
}