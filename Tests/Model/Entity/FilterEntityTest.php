<?php

namespace Evheniy\SearchBundle\Tests\Model\Entity;

use Evheniy\SearchBundle\Model\Entity\FilterEntity;

/**
 * Class FilterEntityTest
 * @package Evheniy\SearchBundle\Tests\Model\Entity
 */
class FilterEntityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FilterEntity
     */
    protected $entity;

    /**
     *
     */
    public function setUp()
    {
        $this->entity = FilterEntity::createFromArray(
            array('filterField1', 'filterField2'),
            array(
                'filterField1' => 1,
                'filterField2' => 2
            )
        );
    }

    /**
     *
     */
    public function testCreateFromArray()
    {
        $entity = FilterEntity::createFromArray(
            array('filterField3', 'filterField4'),
            array('filterField3' => 3)
        );
        $this->assertInstanceOf('Evheniy\SearchBundle\Model\Entity\FilterEntity', $entity);
        $this->assertTrue(!empty($entity->filterField3));
        $this->assertEquals($entity->filterField3, 3);
        $this->assertTrue(isset($entity->filterField4));
        $this->assertTrue(empty($entity->filterField4));
    }

    /**
     *
     */
    public function testGet()
    {
        $this->assertTrue(!empty($this->entity->filterField1));
        $this->assertEquals($this->entity->filterField1, 1);
        $this->assertTrue(!empty($this->entity->filterField2));
        $this->assertEquals($this->entity->filterField2, 2);
        $this->setExpectedException(
            'Evheniy\SearchBundle\Model\Exception\FieldNotFoundException',
            'Field filterField3 doesn\'t exist!'
        );
        $this->entity->filterField3;
    }

    /**
     *
     */
    public function testSet()
    {
        $this->entity->filterField1 = 5;
        $this->assertEquals($this->entity->filterField1, 5);
    }

    /**
     *
     */
    public function testIsset()
    {
        $this->assertTrue(isset($this->entity->filterField1));
        $this->assertTrue(isset($this->entity->filterField2));
    }

    /**
     *
     */
    public function testCall()
    {
        $this->assertTrue(!empty($this->entity->getFilterField1()));
        $this->assertEquals($this->entity->getFilterField1(), 1);
        $this->assertTrue(!empty($this->entity->getFilterField2()));
        $this->assertEquals($this->entity->getFilterField2(), 2);
        $this->setExpectedException(
            'Evheniy\SearchBundle\Model\Exception\MethodNotFoundException',
            'Only getFieldName() methods!'
        );
        $this->entity->filterField3();
        $this->setExpectedException(
            'Evheniy\SearchBundle\Model\Exception\FieldNotFoundException',
            'Field filterField3 doesn\'t exist!'
        );
        $this->entity->getFilterField3();
    }
}