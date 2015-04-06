<?php

namespace Evheniy\SearchBundle\Model\Collection;

use Evheniy\SearchBundle\Model\Entity\FilterEntity;

/**
 * Class FilterCollection
 *
 * @package Evheniy\SearchBundle\Model\Collection
 */
class FilterCollection extends AbstractCollection
{
    /**
     * @param array $fieldNames
     * @param array $entityArray
     *
     * @return array
     */
    protected function getEntities(array $fieldNames, array $entityArray = array())
    {
        $entityCollection = array();
        foreach ($entityArray as $key => $entity) {
            $entityCollection[$key] = FilterEntity::createFromArray($fieldNames, $entity);
        }

        return $entityCollection;
    }
} 