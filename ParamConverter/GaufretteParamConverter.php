<?php
namespace rs\GaufretteBrowserBundle\ParamConverter;

use rs\GaufretteBrowserBundle\Entity\GaufretteRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

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
     * {@inheritDoc}
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        /** @var $configuration ParamConverter */
        $name = $configuration->getName();
        $options = $configuration->getOptions();

        $object = $this->repository->findOneBy(array('slug' => $request->get($options['id'])));

        if ($object) {
            $request->attributes->set($name, $object);

            return true;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function supports(ParamConverter $configuration)
    {
        if (null === $configuration->getClass()) {
            return false;
        }

        if (!array_key_exists('id', $configuration->getOptions())) {
            return false;
        }

        return $this->class == $configuration->getClass();
    }
}
