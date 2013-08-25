<?php
namespace rs\GaufretteBrowserBundle\Controller;

use Gaufrette\Filesystem;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use rs\GaufretteBrowserBundle\Entity\DirectoryRepository;
use rs\GaufretteBrowserBundle\Entity\FileRepository;

abstract class BaseController extends Controller
{
    /**
     * @return Filesystem
     */
    protected function getFilesystem()
    {
        return $this->get($this->container->getParameter('rs_gaufrette_browser.filesystem'));
    }

    /**
     * @return DirectoryRepository
     */
    protected function getDirectoryRepository()
    {
        return $this->get('rs_gaufrette_browser.repository.directory');
    }

    /**
     * @return FileRepository
     */
    protected function getFileRepository()
    {
        return $this->get('rs_gaufrette_browser.repository.file');
    }

    /**
     * @return string
     */
    protected function getFilePattern()
    {
        return $this->container->getParameter('rs_gaufrette_browser.file_pattern');
    }
}
