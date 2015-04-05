<?php

namespace Evheniy\SearchBundle\Model\Index;

use \Evheniy\SearchBundle\Model\Entity\DocumentEntity;

/**
 * Class Indexer
 * @package Evheniy\SearchBundle\Model
 */
class Indexer extends IndexAbstract
{
    /**
     * Delete index
     */
    public function deleteIndex()
    {
        if ($this->client->indices()->exists(array('index' => $this->getIndexName()))) {
            $this->client->indices()->delete(array('index' => $this->getIndexName()));
        }
    }

    /**
     * Create index
     */
    public function createIndex()
    {
        if (!$this->isSynonymsExists()) {
            $this->createSynonyms();
        }

        if (!$this->isStopWordsExists()) {
            $this->createStopWords();
        }

        $this->client->indices()->create(
            $this->getIndexStructure()
        );
    }

    /**
     * @param DocumentEntity $document
     */
    public function indexDocument(DocumentEntity $document)
    {
        $params = array(
            'index' => $this->getIndexName(),
            'type'  => $this->getIndexType(),
            'body'  => array()
        );
        $fields = array_merge(
            $this->container->getParameter('search')['search']['fields'],
            $this->container->getParameter('search')['search']['filter']['fields']
        );
        foreach ($fields as $field) {
            $params['body'][$field] = !empty($document->$field) ? $document->$field : '';
        }
        $this->client->index($params);
    }

    /**
     * @return array
     */
    protected function getIndexStructure()
    {
        $structure = array(
            'index' => $this->getIndexName(),
            'body'  => array(
                'settings' => array(
                    'analysis'   => array(
                        'analyzer' => array(
                            'synonym' => array(
                                'tokenizer' => 'whitespace',
                                'filter'    => array('synonym', 'stopwords')
                            )
                        ),
                        'filter'   => array(
                            'synonym' => array(
                                'type'          => 'synonym',
                                'synonyms' => $this->getSynonymsArray()
                            ),
                            'stopwords' => array(
                                'type'          => 'standard',
                                'stopwords' => $this->getStopWordsArray()
                            )
                        )
                    ),
                    'properties' => array()
                ),
                'mappings' => array(
                    $this->getIndexType() => array(
                        'properties' => array()
                    )
                )
            )
        );

        //properties
        foreach ($this->container->getParameter('search')['search']['fields'] as $field) {
            $structure['body']['settings']['properties'][$field] = array(
                'type'        => 'string',
                'analyzer'    => 'synonym',
                'term_vector' => 'with_positions_offsets'
            );
        }
        foreach ($this->container->getParameter('search')['search']['filter']['fields'] as $field) {
            if ($this->container->getParameter('search')['search']['filter']['analyze']) {
                $structure['body']['settings']['properties'][$field] = array(
                    'type'        => 'string',
                    'analyzer'    => 'synonym',
                    'term_vector' => 'with_positions_offsets'
                );
            } else {
                $structure['body']['settings']['properties'][$field] = array(
                    'type'  => 'string',
                    'index' => 'not_analyzed'
                );
            }

        }

        //mappings
        foreach ($this->container->getParameter('search')['search']['fields'] as $field) {
            $structure['body']['mappings'][$this->getIndexType()]['properties'][$field] = array(
                'type'        => 'string',
                'analyzer'    => 'synonym',
                'term_vector' => 'with_positions_offsets'
            );
        }
        foreach ($this->container->getParameter('search')['search']['filter']['fields'] as $field) {
            if ($this->container->getParameter('search')['search']['filter']['analyze']) {
                $structure['body']['mappings'][$this->getIndexType()]['properties'][$field] = array(
                    'type'            => 'string',
                    'search_analyzer' => 'synonym',
                    'index_analyzer'  => 'synonym'
                );
            } else {
                $structure['body']['mappings'][$this->getIndexType()]['properties'][$field] = array(
                    'type'            => 'string',
                    'search_analyzer' => 'synonym',
                    'index'           => 'not_analyzed'
                );
            }

        }

        return $structure;
    }
}