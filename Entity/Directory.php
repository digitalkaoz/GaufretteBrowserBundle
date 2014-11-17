<?php
namespace rs\GaufretteBrowserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gaufrette\File as GaufretteFile;
use Ferrandini\Urlizer;

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
        return Urlizer::urlize($this->info->getName());
    }

    /**
     * @param Collection|\Closure $collection
     */
    public function setFiles($collection)
    {
        if (!$collection instanceof Collection && !$collection instanceof \Closure) {
            throw new \InvalidArgumentException('invalid collection');
        }

        $this->files = $collection;
    }

    public function getFiles()
    {
        if ($this->files instanceof \Closure) {
            $this->files = $this->files->__invoke();
        }

        foreach ($this->files as $file) {
            /** @var $file File */
            $file->setDirectory($this);
        }

        return $this->files;
    }

    /**
     * @param Collection|\Closure $collection
     */
    public function setDirectories($collection)
    {
        if (!$collection instanceof Collection && !$collection instanceof \Closure) {
            throw new \InvalidArgumentException('invalid collection');
        }

        $this->directories = $collection;
    }

    public function getDirectories()
    {
        if ($this->directories instanceof \Closure) {
            $this->directories = $this->directories->__invoke();
        }

        foreach ($this->directories as $directory) {
            /** @var $directory Directory */
            $directory->setParent($this);
        }

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
