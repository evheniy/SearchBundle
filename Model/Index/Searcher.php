<?php

namespace Evheniy\SearchBundle\Model\Index;

use Evheniy\SearchBundle\Model\Collection\DocumentCollection;
use Evheniy\SearchBundle\Model\Collection\FilterCollection;
use Evheniy\SearchBundle\Model\Collection\FacetCollection;
use Symfony\Component\Config\Definition\Exception\Exception;

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
     * @param int $size
     * @param int $page
     * @param array $filters
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
        $filters = array_map(
            function ($v) {
                return array_values($v);
            },
            $filters
        );
        $this->searchText = $searchText;
        $this->filters = $this->hierarchyLogic($filters);
        //echo json_encode($this->getSearchArray($searchText, $size, $page, $this->filters));
        $queryResponse = $this->client->search($this->getSearchArray($searchText, $size, $page, $this->filters));
        $this->countResults = $queryResponse['hits']['total'];
        $this->facets = $queryResponse['aggregations'];


        return new DocumentCollection(
            $this->getIndexFieldNames(),
            $this->transformData(
                $queryResponse['hits']['hits'],
                $this->filters
            )
        );
    }

    /**
     * @return string
     */
    public function getQueryParameterName()
    {
        return $this->params['query_parameter'];
    }

    /**
     * @return FilterCollection
     */
    public function getFilters()
    {
        $facets = $this->getMappedFacets();
        $filters = array();
        foreach ($this->params['search']['filter']['fields'] as $field => $filterData) {
            if (!empty($facets[$field])) {
                $filters[$field] = new FilterCollection(
                    $this->filterFields,
                    $facets[$field]
                );
            }
        }

        return new FacetCollection(
            array_keys($this->params['search']['filter']['fields']),
            $filters
        );
    }

    /**
     * @return array
     */
    protected function getMappedFacets()
    {

        return $this->getRecursiveAggs($this->facets);
    }

    protected function getRecursiveAggs($aggs)
    {
        $facets = array();
        foreach ($aggs as $aggName => $buckets) {
            foreach ($buckets['buckets'] as $agg) {
                $facets[$aggName][] = array_combine(
                    $this->filterFields,
                    array(
                        $agg['key'],
                        $agg['doc_count'],
                        in_array($agg['key'], $this->filters[$aggName]),
                        $this->getUrlArray($aggName, $agg['key'], $this->filters)
                    )
                );
                if (in_array($agg['key'], $this->filters[$aggName])) {
                    foreach (array_keys($this->params['search']['filter']['fields']) as $field) {
                        if (!empty($agg[$field])) {
                            $facets = array_merge($facets, $this->getRecursiveAggs(array($field => $agg[$field])));
                        }
                    }
                }
            }
        }

        return $facets;
    }

    /**
     * @param string $filterName
     * @param string $filterValue
     * @param array $filters
     * @return string
     */
    public function getUrlArray($filterName, $filterValue, array $filters = array())
    {
        if (in_array($filterValue, $filters[$filterName])) {
            $key = array_search($filterValue, $filters[$filterName]);
            unset($filters[$filterName][$key]);
        } else {
            if (!$this->params['search']['filter']['fields'][$filterName]['multi']) {
                $filters[$filterName] = [$filterValue];
            } else {
                $filters[$filterName][] = $filterValue;
            }
        }

        foreach ($this->getChildren($filterName) as $child) {
            $filters[$child] = array();
        }

        $newUrlArray = array_merge(array($this->getQueryParameterName() => $this->searchText), $filters);

        return $newUrlArray;
    }

    /**
     * @param string $parent
     * @param array $children
     * @return array
     */
    protected function getChildren($parent, array $children = array())
    {
        foreach ($this->params['search']['filter']['fields'] as $key => $val) {
            if (!empty($this->params['search']['filter']['fields'][$key]['parent']) && $this->params['search']['filter']['fields'][$key]['parent'] == $parent) {
                $children = array_merge($children, $this->getChildren($key), array($key));
            }
        }

        return $children;
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
     * @param int $size
     * @param int $page
     * @param array $filters
     *
     * @return array
     */
    protected function getSearchArray($searchText, $size, $page, array $filters = array())
    {
        $searchParams = array(
            'index' => $this->getIndexName(),
            'type' => $this->getIndexType(),
            'body' => array(
                'query' => $this->params['search']['query'],
                'size' => $size,
                'from' => ($page - 1) * $size,
                'highlight' => array(
                    'pre_tags' => array($this->params['color_tag_open']),
                    'post_tags' => array($this->params['color_tag_close']),
                    'fields' => $this->params['search']['highlight']['fields']
                )
            )
        );
        array_walk_recursive($searchParams['body']['query'], function (&$item, $key) use ($searchText) {
            if ($key === 'query') {
                $item = $this->getFilteredQuery($searchText);
            }
        });
        if (!empty($this->params['search']['_source'])) {
            $searchParams['body']['_source'] = $this->params['search']['_source'];
        }

        //aggregations
        foreach ($filters as $id => $filter) {
            $_filter = array(
                'terms' => array(
                    'field' => $id,
                    'order' => array('_count' => "desc"),
                    'size' => $size
                )
            );
            $parent = $id;
            while (!empty($this->params['search']['filter']['fields'][$parent]['parent'])) {
                $_filter = array(
                    'terms' => array(
                        'field' => $this->params['search']['filter']['fields'][$parent]['parent'],
                        'order' => array('_count' => "desc"),
                        'size' => $size,
                        'include' => $filters[$this->params['search']['filter']['fields'][$parent]['parent']]
                    ),
                    'aggs' => array(
                        $parent => $_filter
                    )
                );
                $parent = $this->params['search']['filter']['fields'][$parent]['parent'];
                if (!empty($this->params['search']['filter']['fields'][$parent]['parent'])) {
                    unset($_filter['terms']['include']);
                }
            }
            $searchParams['body']['aggs'][$parent] = $_filter;
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
                if (!empty($restaurant['_source'][$filtersKey])) {
                    if (!is_array($restaurant['_source'][$filtersKey])) {
                        if (false !== array_search(strtoupper($restaurant['_source'][$filtersKey]), $filtersVal)) {
                            if (in_array($filtersKey, array_keys($this->params['search']['highlight']['fields']))) {
                                $restaurant['_source'][$filtersKey] =
                                    $this->params['color_tag_open']
                                    . ucwords($restaurant['_source'][$filtersKey])
                                    . $this->params['color_tag_close'];
                            }
                        }
                    } else {
                        foreach ($restaurant['_source'][$filtersKey] as $k => $v) {
                            if (in_array($filtersKey, array_keys($this->params['search']['highlight']['fields'])) && false !== array_search(strtoupper($v), $filtersVal)
                            ) {
                                $restaurant['_source'][$filtersKey][$k] =
                                    $this->params['color_tag_open']
                                    . ucwords($restaurant['_source'][$filtersKey][$k])
                                    . $this->params['color_tag_close'];
                            }
                        }
                        $restaurant['_source'][$filtersKey] = array_unique($restaurant['_source'][$filtersKey]);
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
                    $restaurant['_source'][$key] = array_map('ucfirst', $restaurant['_source'][$key]);
                    $restaurant['_source'][$key] = array_unique($restaurant['_source'][$key]);
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
        if (!empty(array_diff(array_keys($filters), array_keys($this->params['search']['filter']['fields'])))) {
            throw new Exception('Filters are not in config');
        }

        foreach ($filters as $id => $value) {
            if (!empty($this->params['search']['filter']['fields'][$id]['parent']) && empty($filters[$this->params['search']['filter']['fields'][$id]['parent']])) {
                $filters[$id] = array();
            }
        }

        return $filters;
    }
}
