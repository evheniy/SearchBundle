<?php

namespace Evheniy\SearchBundle\Model\Collection;

use Evheniy\SearchBundle\Model\Entity\DocumentEntity;

/**
 * Class DocumentCollection
 *
 * @package Evheniy\SearchBundle\Model\Collection
 */
class DocumentCollection extends AbstractCollection
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
        foreach ($entityArray as $entity) {
            $entityCollection[] = DocumentEntity::createFromArray($fieldNames, $entity);
        }

        return $entityCollection;
    }
} 