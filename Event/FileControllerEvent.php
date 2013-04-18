<?php

namespace rs\GaufretteBrowserBundle\Event;

use Doctrine\Common\Collections\Collection;
use Symfony\Component\EventDispatcher\Event;
use rs\GaufretteBrowserBundle\Entity\File;

class FileControllerEvent extends Event
{
    private $files = array();

    private $templateData = array();

    public function __construct($file = null)
    {
        if ($file) {
            $this->addFile($file);
        }
    }

    public function getFiles()
    {
        return $this->files;
    }

    public function removeFile(File $deleteFile)
    {
        foreach ($this->files as $key => $file) {
            if ($deleteFile->getKey() == $file->getKey()) {
                unset($this->files[$key]);
                break;
            }
        }
    }

    public function addFile($file)
    {
        if ($file instanceof Collection) {
            $this->files = array_merge($this->files, $file->toArray());
        } elseif ($file instanceof File) {
            $this->files[] = $file;
        } elseif (is_array($file)) {
            $this->files = array_merge($this->files, $file);
        } else {
            throw new \InvalidArgumentException('dont know how to add file');
        }
    }

    public function setFiles($files)
    {
        $this->files = array();
        $this->addFile($files);
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