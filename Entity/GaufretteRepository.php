<?php

namespace rs\GaufretteBrowserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Expr\ClosureExpressionVisitor;
use Doctrine\Common\Persistence\ObjectRepository;
use Gaufrette\Filesystem;
use Gaufrette\File;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @todo dependency to ObjectRepository isnt nice
 * @author Robert Schönthal <schoenthal.robert_FR@guj.de>
 */
abstract class GaufretteRepository implements ObjectRepository
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    protected $class;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    public function __construct(EventDispatcherInterface $dispatcher, Filesystem $filesystem, $class)
    {
        $this->eventDispatcher = $dispatcher;
        $this->filesystem = $filesystem;
        $this->class = $class;
    }

    abstract protected function invokeEvent($elements);

    /**
     * Finds all objects in the repository.
     *
     * @return ArrayCollection The objects.
     */
    public function findAll()
    {
        $keys = $this->filesystem->listKeys();
        $elements = new ArrayCollection();

        foreach ($keys[$this->getKey()] as $element) {
            $elements->set($element, $this->createObject($this->filesystem->get($element)));
        }

        return $this->invokeEvent($elements);
    }

    /**
     * @param  string         $key
     * @return Directory|File
     */
    public function find($key)
    {
        $element = null;

        if ($this->filesystem->has($key)) {
            if ('dirs' == $this->getKey() && $this->filesystem->getAdapter()->isDirectory($key)) {
                $element = $this->createObject($this->filesystem->get($key));
            } elseif ('keys' == $this->getKey() && !$this->filesystem->getAdapter()->isDirectory($key)) {
                $element = $this->createObject($this->filesystem->get($key));
            }
        }

        return $this->invokeEvent($element);
    }

    /**
     * @return ArrayCollection
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        if (isset($criteria['prefix'])) {
            $keys = $this->filesystem->listKeys($criteria['prefix']);
        } else {
            $keys = $this->filesystem->listKeys();
        }

        $elements = new ArrayCollection();

        foreach (array_unique($keys[$this->getKey()]) as $element) {
            $element = $this->filesystem->get($element);

            //check prefix
            if (isset($criteria['prefix']) && $criteria['prefix'].basename($element->getKey()) !== $element->getKey()) {
                continue;
            }

            //check suffix
            if (isset($criteria['suffix']) && !preg_match($criteria['suffix'], strtolower($element->getName()))) {
                continue;
            }

            $elements->add($this->createObject($element));
        }

        if ($orderBy) {
            $next = null;
            foreach (array_reverse($orderBy) as $field => $ordering) {
                $next = ClosureExpressionVisitor::sortByField($field, $ordering == 'DESC' ? -1 : 1, $next);
            }

            $arrayElements = $elements->toArray();
            usort($arrayElements, $next);

            $elements = new ArrayCollection($arrayElements);
        }

        if ($limit) {
            $elements = $elements->slice(0, $limit);
        }

        return $this->invokeEvent($elements);
    }

    /**
     * Finds a single object by a set of criteria.
     *
     * @param  array          $criteria
     * @return File|Directory The object.
     */
    public function findOneBy(array $criteria)
    {
        if (isset($criteria['prefix'])) {
            $keys = $this->filesystem->listKeys($criteria['prefix']);
        } else {
            $keys = $this->filesystem->listKeys();
        }

        foreach ($keys[$this->getKey()] as $element) {
            $element = $this->filesystem->get($element);

            //check prefix
            if (isset($criteria['prefix']) && $criteria['prefix'].basename($element->getKey()) !== $element->getKey()) {
                continue;
            }

            //check suffix
            if (isset($criteria['suffix']) && !preg_match($criteria['suffix'], strtolower($element->getKey()))) {
                continue;
            }

            return $this->invokeEvent($this->createObject($element));
        }
    }

    private function createObject(File $file)
    {
        $class = $this->getClassName();

        return new $class($file);
    }

    private function getKey()
    {
        //TODO bad check for file or directory
        if ('rs\GaufretteBrowserBundle\Entity\File' == $this->getClassName()) {
            return 'keys';
        } elseif ('rs\GaufretteBrowserBundle\Entity\Directory' == $this->getClassName()) {
            return 'dirs';
        }

        throw new \InvalidArgumentException('unknown type '.$this->getClassName());
    }

    /**
     * Returns the class name of the object managed by the repository
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->class;
    }
}
