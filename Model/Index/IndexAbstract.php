<?php

namespace Evheniy\SearchBundle\Model\Index;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Elasticsearch\ClientBuilder;
use Evheniy\SearchBundle\DependencyInjection\Configuration;


/**
 * Class IndexAbstract
 * @package Evheniy\SearchBundle\Model
 */
abstract class IndexAbstract
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;
    /**
     * @var Client
     */
    protected $client;
    /**
     * @var array
     */
    protected $params = array();

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->params = $container->getParameter('search');
        $clientBuilder = ClientBuilder::create();
        $clientBuilder->setHosts(
            array(
                $this->params['search_url']
            )
        );
        $this->client = $clientBuilder->build();
    }

    /**
     * @param array $configs
     * @return $this
     */
    public function load(array $configs)
    {
        $configuration = new Configuration($configs);
        $this->params = $configuration->validate()->filter()->getConfig();

        return $this;
    }

    /**
     * @return string
     */
    public function getIndexName()
    {
        return $this->params['index_name'];
    }

    /**
     * @return string
     */
    public function getIndexType()
    {
        return $this->params['index_type'];
    }

    /**
     * @return array
     */
    public function getIndexFieldNames()
    {
        return $this->params['index']['fields'];
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
                return $this->trimFilter($string);
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
                return $this->trimFilter($string);
            },
            file($this->getSynonyms())
        );
    }

    /**
     * @param string $string
     *
     * @return string
     */
    protected function trimFilter($string)
    {
        return trim(preg_replace('/\s\s+/', ' ', $string));
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
        copy(__DIR__ . '/../Resources/config/synonyms.txt.dist', $this->getSynonyms());
    }

    /**
     * @return string
     */
    protected function getSynonyms()
    {
        return $this->container->get('kernel')->getRootDir() . '/config/synonyms.txt';
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
        copy(__DIR__ . '/../Resources/config/stopwords.txt.dist', $this->getStopWords());
    }

    /**
     * @return string
     */
    protected function getStopWords()
    {
        return $this->container->get('kernel')->getRootDir() . '/config/stopwords.txt';
    }
}