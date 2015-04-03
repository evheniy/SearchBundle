<?php

namespace Evheniy\SearchBundle\Model;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Elasticsearch\Client;

/**
 * Class Indexer
 * @package Evheniy\SearchBundle\Model
 */
class Indexer
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
     * @var string
     */
    protected $synonymsPath;


    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->client    = new Client();
        $this->synonymsPath = $this->container->get('kernel')->getRootDir() . '/config/synonyms.txt';
    }

    public function deleteIndex()
    {
        if ($this->client->indices()->exists(array('index' => $this->getIndexName()))) {
            $this->client->indices()->delete(array('index' => $this->getIndexName()));
        }
    }

    public function createIndex()
    {
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
                                    'synonyms_path' => $this->synonymsPath
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
            'body'  => array(
                'restaurantId'         => (int)$data['restaurantId'],
                'restaurantName'       => !empty($data['restaurantName']) ? $data['restaurantName'] : '',
                'address'              => !empty($data['address']) ? $data['address'] : '',
                'addressCity'          => !empty($data['addressCity']) ? $data['addressCity'] : '',
                'addressPostcode'      => !empty($data['addressPostcode']) ? $data['addressPostcode'] : '',
                'menuUrl'              => !empty($data['menuUrl']) ? $data['menuUrl'] : '',
                'deliveryDistrict'     => !empty($data['deliveryDistrict']) ? $data['deliveryDistrict'] : '',
                'deliveryTown'         => !empty($data['deliveryTown']) ? $data['deliveryTown'] : '',
                'primaryCuisine'       => !empty($data['primaryCuisine']) ? $data['primaryCuisine'] : '',
                'secondaryCuisine'     => !empty($data['secondaryCuisine']) ? $data['secondaryCuisine'] : '',
                'uniqueName'           => !empty($data['uniqueName']) ? $data['uniqueName'] : '',
                'ratingStars'          => !empty($data['ratingStars']) ? $data['ratingStars'] : '',
                'numberOfRatings'      => !empty($data['numberOfRatings']) ? $data['numberOfRatings'] : '',
                'logo'                 => !empty($data['logo']) ? $data['logo'] : '',
                'dealsDescription'     => !empty($data['dealsDescription']) ? $data['dealsDescription'] : '',
                'dealsDiscountPercent' => !empty($data['dealsDiscountPercent']) ? $data['dealsDiscountPercent'] : '',
                'dealsQualifyingPrice' => !empty($data['dealsQualifyingPrice']) ? $data['dealsQualifyingPrice'] : ''
            )
        );
        $this->client->index($params);
    }

    public function getIndexName()
    {
        return  $this->container->getParameter('search')['index_name'];
    }

    public function getIndexType()
    {
        return  $this->container->getParameter('search')['index_type'];
    }
}