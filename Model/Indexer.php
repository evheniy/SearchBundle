<?php

namespace Evheniy\SearchBundle\Model;

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

    public function createIndex()
    {
        if (!$this->isSynonymsExists()) {
            $this->createSynonyms();
        }
        $this->client->indices()->create(
            array(
                'index' => $this->getIndexName(),
                'body'  => array(
                    'settings' => array(
                        'analysis'   => array(
                            'analyzer' => array(
                                'synonym' => array(
                                    'tokenizer' => 'whitespace',
                                    'filter'    => array('synonym')
                                )
                            ),
                            'filter'   => array(
                                'synonym' => array(
                                    'type'          => 'synonym',
                                    'synonyms_path' => $this->getSynonyms()
                                )
                            )
                        ),
                        'properties' => array(
                            'restaurantId'         => array(
                                'type' => 'long'
                            ),
                            'restaurantName'       => array(
                                'type'        => 'string',
                                'analyzer'    => 'synonym',
                                'term_vector' => 'with_positions_offsets'
                            ),
                            'address'              => array(
                                'type'        => 'string',
                                'analyzer'    => 'stem',
                                'term_vector' => 'with_positions_offsets'
                            ),
                            'addressPostcode'      => array(
                                'type'        => 'string',
                                'analyzer'    => 'stem',
                                'term_vector' => 'with_positions_offsets'
                            ),
                            'menuUrl'              => array(
                                'type'        => 'string',
                                'analyzer'    => 'stem',
                                'term_vector' => 'with_positions_offsets'
                            ),
                            'deliveryArea'         => array(
                                'type'        => 'string',
                                'analyzer'    => 'stem',
                                'term_vector' => 'with_positions_offsets'
                            ),
                            'primaryCuisine'       => array(
                                'type'        => 'string',
                                'analyzer'    => 'stem',
                                'term_vector' => 'with_positions_offsets'
                            ),
                            'secondaryCuisine'     => array(
                                'type'        => 'string',
                                'analyzer'    => 'stem',
                                'term_vector' => 'with_positions_offsets'
                            ),
                            'uniqueName'           => array(
                                'type'        => 'string',
                                'analyzer'    => 'stem',
                                'term_vector' => 'with_positions_offsets'
                            ),
                            'ratingStars'          => array(
                                'type'        => 'string',
                                'analyzer'    => 'stem',
                                'term_vector' => 'with_positions_offsets'
                            ),
                            'numberOfRatings'      => array(
                                'type'        => 'string',
                                'analyzer'    => 'stem',
                                'term_vector' => 'with_positions_offsets'
                            ),
                            'logo'                 => array(
                                'type'        => 'string',
                                'analyzer'    => 'stem',
                                'term_vector' => 'with_positions_offsets'
                            ),
                            'dealsDescription'     => array(
                                'type'        => 'string',
                                'analyzer'    => 'stem',
                                'term_vector' => 'with_positions_offsets'
                            ),
                            'dealsDiscountPercent' => array(
                                'type'        => 'string',
                                'analyzer'    => 'stem',
                                'term_vector' => 'with_positions_offsets'
                            ),
                            'dealsQualifyingPrice' => array(
                                'type'        => 'string',
                                'analyzer'    => 'stem',
                                'term_vector' => 'with_positions_offsets'
                            ),
                            'addressCity'          => array(
                                'type'  => 'string',
                                'index' => 'not_analyzed'
                            ),
                            'deliveryDistrict'     => array(
                                'type'  => 'string',
                                'index' => 'not_analyzed'
                            ),
                            'deliveryTown'         => array(
                                'type'  => 'string',
                                'index' => 'not_analyzed'
                            )
                        )
                    ),
                    'mappings' => array(
                        'restaurants' => array(
                            'properties' => array(
                                'restaurantId'         => array(
                                    'type' => 'long'
                                ),
                                'restaurantName'       => array(
                                    'type'            => 'string',
                                    'search_analyzer' => 'synonym',
                                    'index_analyzer'  => 'synonym'
                                ),
                                'address'              => array(
                                    'type'            => 'string',
                                    'search_analyzer' => 'synonym',
                                    'index_analyzer'  => 'synonym'
                                ),
                                'addressPostcode'      => array(
                                    'type'            => 'string',
                                    'search_analyzer' => 'synonym',
                                    'index_analyzer'  => 'synonym'
                                ),
                                'menuUrl'              => array(
                                    'type'            => 'string',
                                    'search_analyzer' => 'synonym',
                                    'index_analyzer'  => 'synonym'
                                ),
                                'deliveryArea'         => array(
                                    'type'            => 'string',
                                    'search_analyzer' => 'synonym',
                                    'index_analyzer'  => 'synonym'
                                ),
                                'primaryCuisine'       => array(
                                    'type'            => 'string',
                                    'search_analyzer' => 'synonym',
                                    'index_analyzer'  => 'synonym'
                                ),
                                'secondaryCuisine'     => array(
                                    'type'            => 'string',
                                    'search_analyzer' => 'synonym',
                                    'index_analyzer'  => 'synonym'
                                ),
                                'uniqueName'           => array(
                                    'type'            => 'string',
                                    'search_analyzer' => 'synonym',
                                    'index_analyzer'  => 'synonym'
                                ),
                                'ratingStars'          => array(
                                    'type'            => 'string',
                                    'search_analyzer' => 'synonym',
                                    'index_analyzer'  => 'synonym'
                                ),
                                'numberOfRatings'      => array(
                                    'type'            => 'string',
                                    'search_analyzer' => 'synonym',
                                    'index_analyzer'  => 'synonym'
                                ),
                                'logo'                 => array(
                                    'type'            => 'string',
                                    'search_analyzer' => 'synonym',
                                    'index_analyzer'  => 'synonym'
                                ),
                                'dealsDescription'     => array(
                                    'type'            => 'string',
                                    'search_analyzer' => 'synonym',
                                    'index_analyzer'  => 'synonym'
                                ),
                                'dealsDiscountPercent' => array(
                                    'type'            => 'string',
                                    'search_analyzer' => 'synonym',
                                    'index_analyzer'  => 'synonym'
                                ),
                                'dealsQualifyingPrice' => array(
                                    'type'            => 'string',
                                    'search_analyzer' => 'synonym',
                                    'index_analyzer'  => 'synonym'
                                ),
                                'addressCity'          => array(
                                    'type'  => 'string',
                                    'index' => 'not_analyzed'
                                ),
                                'deliveryTown'         => array(
                                    'type'            => 'string',
                                    'search_analyzer' => 'synonym',
                                    'index'           => 'not_analyzed'
                                ),
                                'deliveryDistrict'     => array(
                                    'type'            => 'string',
                                    'search_analyzer' => 'synonym',
                                    'index'           => 'not_analyzed'
                                )
                            )

                        )
                    )
                )
            )
        );
    }

    public function indexDocument(array $data)
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
            $params['body'][$field] = !empty($data[$field]) ? $data[$field] : '';
        }
        $this->client->index($params);
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

}