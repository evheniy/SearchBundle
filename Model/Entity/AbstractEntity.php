<?php

namespace Evheniy\SearchBundle\Model\Entity;

use Evheniy\SearchBundle\Model\Exception\FieldNotFoundException;
use Evheniy\SearchBundle\Model\Exception\MethodNotFoundException;

/**
 * Class AbstractEntity
 *
 * @package Evheniy\SearchBundle\Model\Entity
 */
abstract class AbstractEntity
{
    /**
     * @var array
     */
    protected $fields = array();

    /**
     * @param array $fieldNames
     * @param array $data
     *
     * @return mixed
     */
    static public function createFromArray(array $fieldNames, array $data = array())
    {
        $class = get_called_class();
        $entity = new $class();
        foreach ($fieldNames as $fieldName) {
            $entity->$fieldName = !empty($data[$fieldName]) ? $data[$fieldName] : '';
        }

        return $entity;
    }

    /**
     * @param string $fieldName
     *
     * @return mixed
     * @throws \Evheniy\SearchBundle\Model\Exception\FieldNotFoundException
     */
    public function __get($fieldName)
    {
        if (isset($this->fields[$fieldName])) {
            return $this->fields[$fieldName];
        } else {
            throw new FieldNotFoundException("Field {$fieldName} doesn't exist!");
        }
    }

    /**
     * @param string $fieldName
     * @param string $data
     */
    public function __set($fieldName, $data = '')
    {
        $this->fields[$fieldName] = $data;
    }

    /**
     * @param string $fieldName
     *
     * @return bool
     */
    public function __isset($fieldName)
    {
        return isset($this->fields[$fieldName]);
    }

    /**
     * @param string $name
     * @param array  $arguments
     *
     * @throws \Evheniy\SearchBundle\Model\Exception\MethodNotFoundException
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (strpos($name, 'get') !== 0) {
            throw new MethodNotFoundException('Only getFieldName() methods!');
        } else {
            return $this->__get(lcfirst(substr($name, 3)));
        }
    }
} 