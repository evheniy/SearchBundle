<?php

namespace Evheniy\SearchBundle\Model;

class Searcher extends IndexAbstract
{
    /**
     * @var int
     */
    protected $countResults = 0;
    /**
     * @var array
     */
    protected $facets = array();
    /**
     * @var \Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination
     */
    protected $paginator;
    /**
     * @var array
     */
    protected $filters = array();

    /**
     * @param $searchText
     * @param $size
     * @param $page
     * @param $filters
     * @return array
     */
    public function search($searchText, $size, $page, array $filters = array())
    {
        $this->filters = $this->hierarchyLogic($filters);
        $queryResponse = $this->client->search($this->getSearchArray($searchText, $size, $page, $this->filters));
        $this->countResults = $queryResponse['hits']['total'];
        $this->facets = $queryResponse['facets'];
        $this->paginator = $this->container->get('knp_paginator')->paginate(array_pad(array(), $this->getCountResults(), 0), $page, $size);
        return $this->transformData(
            $queryResponse['hits']['hits'],
            $this->filters
        );
    }

    /**
     * @param string $name
     * @return array
     */
    public function getFilterByName($name)
    {
        return !empty($this->filters[$name]) ? $this->filters[$name] : array();
    }

    /**
     * @return int
     */
    public function getCountResults()
    {
        return $this->countResults;
    }

    /**
     * @return array
     */
    public function getFacets()
    {
        return $this->facets;
    }

    /**
     * @return \Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination
     */
    public function getPaginator()
    {
        return $this->paginator;
    }

    protected function getSearchArray($searchText, $size, $page, array $filters = array())
    {
        $searchParams = array(
            'index' => $this->getIndexName(),
            'type'  => $this->getIndexType(),
            'body'  => array(
                'query'     => array(
                    'multi_match' => array(
                        'query'                => $this->getFilteredQuery($searchText),
                        'fuzziness'            => $this->container->getParameter('search')['search']['parameters']['fuzziness'],
                        'operator'             => $this->container->getParameter('search')['search']['parameters']['operator'],
                        'analyzer'             => 'synonym',
                        'type'                 => $this->container->getParameter('search')['search']['parameters']['type'],
                        'tie_breaker'          => $this->container->getParameter('search')['search']['parameters']['tie_breaker'],
                        'minimum_should_match' => $this->container->getParameter('search')['search']['parameters']['minimum_should_match'],
                        'fields'               => array()
                    )
                ),
                'size'      => $size,
                'from'      => ($page - 1) * $size,
                'highlight' => array(
                    'pre_tags'  => array($this->container->getParameter('search')['color_tag_open']),
                    'post_tags' => array($this->container->getParameter('search')['color_tag_close']),
                    'fields'    => array()
                )
            )
        );

        //query fields
        foreach ($this->container->getParameter('search')['search']['fields'] as $field) {
            $searchParams['body']['query']['multi_match']['fields'][] = $field;
            $searchParams['body']['highlight']['fields'][$field] = array(
                'fragment_size'       => 1500,
                'number_of_fragments' => 3
            );
        }

        //facets
        $_filters = array();
        foreach ($filters as $id => $filter) {
            $searchParams['body']['facets'][$id]['terms'] = array(
                'field' => $id,
                'order' => 'count',
                'size'  => $size
            );
            if (!empty($_filters)) {
                if (count($_filters) == 1) {
                    $searchParams['body']['facets'][$id]['facet_filter'] =  $_filters;
                } else {
                    $searchParams['body']['facets'][$id]['facet_filter']['and']['filters'] =  $_filters;
                }
            }
            $_filters[] = array(
                'terms' => array(
                    $id => $filter
                )
            );
        }

        //filters
        foreach ($filters as $id => $filter) {
            if (!empty($filter)) {
                $searchParams['body']['filter']['bool']['must'][] = array(
                    'terms' => array(
                        $id => $filter
                    )
                );
            }
        }

        return $searchParams;
    }

    /**
     * @param string $query
     * @return string
     */
    protected function getFilteredQuery($query)
    {
        return preg_replace('/\b(' . implode('|', $this->getStopWordsArray()) . ')\b/', '', $query);
    }

    /**
     * @param array $data
     * @param array $filters
     * @return array
     */
    protected function transformData(array $data = array(), array $filters = array())
    {
        $restaurants = array();
        foreach ($data as $restaurant) {
            foreach ($filters as $filtersKey => $filtersVal) {
                $filtersVal = array_map(
                    function ($val) {
                        return strtoupper($val);
                    },
                    $filtersVal
                );
                if (!is_array($restaurant['_source'][$filtersKey])) {
                    if (false !== array_search(strtoupper($restaurant['_source'][$filtersKey]), $filtersVal)) {
                        $restaurant['_source'][$filtersKey] =
                            $this->container->getParameter('search')['color_tag_open']
                            . ucwords($restaurant['_source'][$filtersKey])
                            . $this->container->getParameter('search')['color_tag_close'];
                    }
                } else {
                    foreach ($restaurant['_source'][$filtersKey] as $k => $v) {
                        if (false !== array_search(strtoupper($v), $filtersVal)
                        ) {
                            $restaurant['_source'][$filtersKey][$k] =
                                $this->container->getParameter('search')['color_tag_open']
                                . ucwords($restaurant['_source'][$filtersKey][$k])
                                . $this->container->getParameter('search')['color_tag_close'];
                        }
                    }
                }
            }
            foreach ($restaurant['_source'] as $key => $val) {
                if (!empty($restaurant['highlight'][$key])) {
                    if (!is_array($val)) {
                        $restaurant['_source'][$key] = $restaurant['highlight'][$key][0];
                    } else {
                        $k = array_search(strip_tags($restaurant['highlight'][$key][0]), $val);
                        $restaurant['_source'][$key][$k] = $restaurant['highlight'][$key][0];
                    }
                }
                if (is_array($restaurant['_source'][$key])) {
                    $restaurant['_source'][$key] = implode(', ', $restaurant['_source'][$key]);
                }
            }
            $restaurants[] = $restaurant['_source'];
        }

        return $restaurants;
    }

    public function hierarchyLogic(array $filters = array())
    {
        $empty = false;
        foreach ($filters as $id => $value) {
            if (empty($value)) {
                $empty = true;
            }
            if ($empty) {
                $filters[$id] = array();
            }
        }

        return $filters;
    }
}