<?php

namespace Evheniy\SearchBundle\Model;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Elasticsearch\Client;

/**
 * Class IndexAbstract
 * @package Evheniy\SearchBundle\Model
 */
abstract class IndexAbstract
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;
    /**
     * @var Client
     */
    protected $client;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->client    = new Client();
    }
    /**
     * @return string
     */
    public function getIndexName()
    {
        return  $this->container->getParameter('search')['index_name'];
    }

    /**
     * @return string
     */
    public function getIndexType()
    {
        return  $this->container->getParameter('search')['index_type'];
    }
}