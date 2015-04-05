<?php

namespace Evheniy\SearchBundle\Model\Collection;

/**
 * Class AbstractCollection
 *
 * @package Evheniy\SearchBundle\Model\Collection
 */
abstract class AbstractCollection extends \ArrayObject
{
    /**
     * @param array $fieldNames
     * @param array $entityArray
     */
    public function __construct(array $fieldNames, array $entityArray = array())
    {
        parent::__construct($this->getEntities($fieldNames, $entityArray));
    }

    /**
     * @param array $fieldNames
     * @param array $entityArray
     *
     * @return array
     */
    abstract protected function getEntities(array $fieldNames, array $entityArray = array());
} 