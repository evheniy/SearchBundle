<?php

namespace Evheniy\SearchBundle\Model;

class Searcher extends IndexAbstract
{
    public function search()
    {

    }

    public function getFilters()
    {

    }

    /**
     * @param string $query
     * @return string
     */
    public function getFilteredQuery($query)
    {
        return preg_replace('/\b(' . implode('|', $this->getStopWordsArray()) . ')\b/', '', $query);
    }
}