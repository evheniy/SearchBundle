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
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('url', array($this, 'filter')),
        );
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

    /**
     * @param array $url
     * @param string $type
     * @param string $key
     * @param string $value
     * @return array
     */
    public function filter(array $url, $type, $key, $value)
    {
        if (!in_array($type, array('add', 'remove'))) {
            return $url;
        }
        if ($type == 'add') {
            $url[$key][] = $value;
        }

        if ($type == 'remove') {
            unset($url[$key][array_search($value, $url[$key])]);
        }

        $url[$key] = array_values($url[$key]);

        $searcher = $this->container->get('search');

        $url = $searcher->hierarchyLogic(
            array(
                'cities'    => $url['cities'],
                'towns'     => $url['towns'],
                'districts' => $url['districts']
            )
        );

        return $url;
    }
}
