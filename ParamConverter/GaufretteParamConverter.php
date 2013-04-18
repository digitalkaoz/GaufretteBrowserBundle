<?php
namespace rs\GaufretteBrowserBundle\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use rs\GaufretteBrowserBundle\Entity\GaufretteRepository;

abstract class GaufretteParamConverter implements ParamConverterInterface
{
    /**
     * @var GaufretteRepository
     */
    private $repository;

    private $class;

    public function __construct(GaufretteRepository $repository, $class)
    {
        $this->repository = $repository;
        $this->class = $class;
    }

    /**
     * Stores the object in the request.
     *
     * @param Request                $request       The request
     * @param ConfigurationInterface $configuration Contains the name, class and options of the object
     *
     * @return boolean True if the object has been successfully set, else false
     */
    public function apply(Request $request, ConfigurationInterface $configuration)
    {
        /** @var $configuration ParamConverter */
        $name    = $configuration->getName();
        $options = $configuration->getOptions();

        $object = $this->repository->find($request->get($options['id']));

        if ($object) {
            $request->attributes->set($name, $object);

            return true;
        }

        return false;
    }

    /**
     * Checks if the object is supported.
     *
     * @param ConfigurationInterface $configuration Should be an instance of ParamConverter
     *
     * @return boolean True if the object is supported, else false
     */
    public function supports(ConfigurationInterface $configuration)
    {
        if (!$configuration instanceof ParamConverter) {
            return false;
        }

        if (null === $configuration->getClass()) {
            return false;
        }

        if(!array_key_exists('id', $configuration->getOptions())) {
            return false;
        }

        return $this->class == $configuration->getClass();
    }
}
