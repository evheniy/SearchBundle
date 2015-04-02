<?php

namespace Evheniy\SearchBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class SearchExtension
 *
 * @package Evheniy\SearchBundle\Twig
 */
class SearchExtension extends \Twig_Extension
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return array
     */
    public function getGlobals()
    {
        return array(
            'search' => $this->container->getParameter('search')
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'search';
    }
}
