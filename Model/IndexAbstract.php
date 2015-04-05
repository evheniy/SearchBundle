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

    /**
     * @return array
     */
    protected function getStopWordsArray()
    {
        if (!$this->isStopWordsExists()) {
            $this->createStopWords();
        }
        return array_map(
            function ($string) {
                return trim(preg_replace('/\s\s+/', '', $string));
            },
            file($this->getStopWords())
        );
    }

    /**
     * @return array
     */
    protected function getSynonymsArray()
    {
        if (!$this->isSynonymsExists()) {
            $this->createSynonyms();
        }
        return array_map(
            function ($string) {
                return trim(preg_replace('/\s\s+/', '', $string));
            },
            file($this->getSynonyms())
        );
    }

    /**
     * @return bool
     */
    protected function isSynonymsExists()
    {
        return file_exists($this->getSynonyms());
    }

    /**
     * Create synonyms file
     */
    protected function createSynonyms()
    {
        copy(__DIR__.'/../Resources/config/synonyms.txt.dist', $this->getSynonyms());
    }

    /**
     * @return string
     */
    protected function getSynonyms()
    {
        return $this->container->get('kernel')->getRootDir().'/config/synonyms.txt';
    }

    /**
     * @return bool
     */
    protected function isStopWordsExists()
    {
        return file_exists($this->getStopWords());
    }

    /**
     * Create stopwords file
     */
    protected function createStopWords()
    {
        copy(__DIR__.'/../Resources/config/stopwords.txt.dist', $this->getStopWords());
    }

    /**
     * @return string
     */
    protected function getStopWords()
    {
        return $this->container->get('kernel')->getRootDir().'/config/stopwords.txt';
    }
}