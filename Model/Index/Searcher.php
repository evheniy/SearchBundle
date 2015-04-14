<?php

namespace Evheniy\SearchBundle\Model\Index;

use Evheniy\SearchBundle\Model\Collection\DocumentCollection;
use Evheniy\SearchBundle\Model\Collection\FilterCollection;
use Evheniy\SearchBundle\Model\Collection\FacetCollection;

/**
 * Class Searcher
 *
 * @package Evheniy\SearchBundle\Model\Index
 */
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
     * @var array
     */
    protected $filters = array();
    /**
     * @var array
     */
    protected $filterFields = array('name', 'count', 'isActive', 'url');
    /**
     * @var string
     */
    protected $searchText = '';

    /**
     * @param string $searchText
     * @param int    $size
     * @param int    $page
     * @param array  $filters
     * @return array
     */
    public function search($searchText, $size = 10, $page = 1, array $filters = array())
    {
        if ($size < 1) {
            $size = 10;
        }
        if ($page < 1) {
            $page = 1;
        }
        $this->searchText   = $searchText;
        $this->filters      = $this->hierarchyLogic($filters);
        $queryResponse      = $this->client->search($this->getSearchArray($searchText, $size, $page, $this->filters));
        $this->countResults = $queryResponse['hits']['total'];
        $this->facets       = $queryResponse['facets'];

        return new DocumentCollection(
            $this->getIndexFieldNames(),
            $this->transformData(
                $queryResponse['hits']['hits'],
                $this->filters
            )
        );
    }

    /**
     * @return FilterCollection
     */
    public function getFilters()
    {
        $facets  = $this->getMappedFacets();
        $filters = array();
        foreach ($this->container->getParameter('search')['search']['filter']['fields'] as $field) {
            if (!empty($facets[$field])) {
                $filters[$field] = new FilterCollection(
                    $this->filterFields,
                    $facets[$field]
                );
            }
        }

        return new FacetCollection(
            $this->container->getParameter('search')['search']['filter']['fields'],
            $filters
        );
    }

    /**
     * @return array
     */
    protected function getMappedFacets()
    {
        $facets = array();
        foreach ($this->container->getParameter('search')['search']['filter']['fields'] as $field) {
            foreach ($this->facets[$field]['terms'] as $facet) {
                $facets[$field][] = array_combine(
                    $this->filterFields,
                    array(
                        $facet['term'],
                        $facet['count'],
                        in_array($facet['term'], $this->filters[$field]),
                        $this->getUrlArray($field, $facet['term'], $this->filters)
                    )
                );
            }
        }

        return $facets;
    }

    /**
     * @param string $filterType
     * @param string $filterName
     * @param array  $filters
     * @return string
     */
    public function getUrlArray($filterType, $filterName, array $filters = array())
    {
        if (in_array($filterName, $filters[$filterType])) {
            $key = array_search($filterName, $filters[$filterType]);
            unset($filters[$filterType][$key]);
            $empty = false;
            foreach ($filters as $id => $value) {
                if ($id == $filterType) {
                    $empty = true;
                }
                if ($empty) {
                    $filters[$id] = array();
                }
            }
        } else {
            $filters[$filterType] = [$filterName];
            $empty = false;
            foreach ($filters as $id => $value) {
                if ($empty) {
                    $filters[$id] = array();
                }
                if ($id == $filterType) {
                    $empty = true;
                }
            }
        }
        $newUrlArray = array_merge(array('q' => $this->searchText), $filters);

        return $newUrlArray;
    }

    /**
     * @return int
     */
    public function getResultsCount()
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
     * @param string $searchText
     * @param int    $size
     * @param int    $page
     * @param array  $filters
     *
     * @return array
     */
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
            $field_ = $field;
            $priorities = $this->container->getParameter('search')['search']['parameters']['priorities'];
            foreach ($priorities as $priority) {
                if (!empty($priority[$field_])) {
                    $field_ = $field_ . '^' . $priority[$field_];
                }
            }
            $searchParams['body']['query']['multi_match']['fields'][] = $field_;
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

    /**
     * @param array $filters
     *
     * @return array
     */
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
