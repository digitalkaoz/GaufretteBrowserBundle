<?php

namespace rs\GaufretteBrowserBundle\Entity;

use Ferrandini\Urlizer;
use Gaufrette\File as BaseFile;

class File
{
    /**
     * @var File
     */
    private $info;

    /**
     * @var Directory
     */
    private $directory;

    public function __construct(BaseFile $info)
    {
        $this->info = $info;
    }

    public function __call($fn, $args)
    {
        if (method_exists($this->info, $fn)) {
            return $this->info->$fn($args);
        }

        if (method_exists($this->info, 'get' . ucfirst($fn))) {
            $fn = 'get' . ucfirst($fn);

            return $this->info->$fn($args);
        }

        throw new \BadMethodCallException($fn . ' doesnt exists');
    }

    public function getSlug()
    {
        return Urlizer::urlize(str_replace('.' . $this->getExtension(), '', $this->info->getName())) . '.' . $this->getExtension();
    }

    /**
     * @param Directory|\Closure $directory
     */
    public function setDirectory($directory)
    {
        if (!$directory instanceof Directory && !$directory instanceof \Closure) {
            throw new \InvalidArgumentException('invalid directory');
        }

        $this->directory = $directory;
    }

    public function getDirectory()
    {
        if ($this->directory instanceof \Closure) {
            $this->directory = $this->directory->__invoke();
        }

        return $this->directory;
    }

    public function getExtension()
    {
        return pathinfo($this->info->getName(), PATHINFO_EXTENSION);
    }
}
