<?php
namespace rs\GaufretteBrowserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Gaufrette\File as GaufretteFile;

class Directory
{
    /**
     * @var File
     */
    private $info;

    /**
     * @var ArrayCollection
     */
    private $files;

    /**
     * @var Directory
     */
    private $parent;

    /**
     * @var ArrayCollection
     */
    private $directories;

    public function __construct(GaufretteFile $info)
    {
        $this->info = $info;
        $this->files = new ArrayCollection();
        $this->directories = new ArrayCollection();
    }

    public function __call($fn, $args)
    {
        if (method_exists($this->info, $fn)) {
            return $this->info->$fn($args);
        }

        if (method_exists($this->info, 'get'.ucfirst($fn))) {
            $fn = 'get'.ucfirst($fn);

            return $this->info->$fn($args);
        }

        throw new \BadMethodCallException($fn.' is not defined');
    }

    public function getSlug()
    {
        return str_replace(' ', '_', strtolower($this->info->getName()));
    }

    public function setFiles(ArrayCollection $collection)
    {
        foreach ($collection as $file) {
            /** @var $file File */
            $file->setDirectory($this);
        }

        $this->files = $collection;
    }

    public function getFiles()
    {
        return $this->files;
    }

    public function setDirectories(ArrayCollection $collection)
    {
        foreach ($collection as $directory) {
            /** @var $directory Directory */
            $directory->setParent($this);
        }

        $this->directories = $collection;
    }

    public function getDirectories()
    {
        return $this->directories;
    }

    public function setParent(Directory $directory)
    {
        $this->parent = $directory;
    }

    public function getParent()
    {
        return $this->parent;
    }
}
