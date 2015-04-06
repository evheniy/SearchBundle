<?php

namespace Evheniy\SearchBundle\Model\Collection;

/**
 * Class FacetCollection
 *
 * @package Evheniy\SearchBundle\Model\Collection
 */
class FacetCollection extends AbstractCollection
{
    /**
     * @param array $fieldNames
     * @param array $entityArray
     *
     * @return array
     */
    protected function getEntities(array $fieldNames, array $entityArray = array())
    {

        return $entityArray;
    }
} 