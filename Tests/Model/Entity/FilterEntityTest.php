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
     * @var DocumentEntity
     */
    protected $entity;

    /**
     *
     */
    public function setUp()
    {
        $this->entity = FilterEntity::createFromArray(
            array('field1', 'field2'),
            array(
                'field1' => 1,
                'field2' => 2
            )
        );
    }

    /**
     *
     */
    public function testCreateFromArray()
    {
        $entity = FilterEntity::createFromArray(
            array('field3', 'field4'),
            array('field3' => 3)
        );
        $this->assertInstanceOf('Evheniy\SearchBundle\Model\Entity\FilterEntity', $entity);
        $this->assertTrue(!empty($entity->field3));
        $this->assertEquals($entity->field3, 3);
        $this->assertTrue(isset($entity->field4));
        $this->assertTrue(empty($entity->field4));
    }

    /**
     *
     */
    public function testGet()
    {
        $this->assertTrue(!empty($this->entity->field1));
        $this->assertEquals($this->entity->field1, 1);
        $this->assertTrue(!empty($this->entity->field2));
        $this->assertEquals($this->entity->field2, 2);
        $this->setExpectedException(
            'Evheniy\SearchBundle\Model\Exception\FieldNotFoundException',
            'Field field3 doesn\'t exist!'
        );
        $this->entity->field3;
    }

    /**
     *
     */
    public function testSet()
    {
        $this->entity->field1 = 5;
        $this->assertEquals($this->entity->field1, 5);
    }

    /**
     *
     */
    public function testIsset()
    {
        $this->assertTrue(isset($this->entity->field1));
        $this->assertTrue(isset($this->entity->field2));
    }

    /**
     *
     */
    public function testCall()
    {
        $this->assertTrue(!empty($this->entity->getField1()));
        $this->assertEquals($this->entity->getField1(), 1);
        $this->assertTrue(!empty($this->entity->getField2()));
        $this->assertEquals($this->entity->getField2(), 2);
        $this->setExpectedException(
            'Evheniy\SearchBundle\Model\Exception\MethodNotFoundException',
            'Only getFieldName() methods!'
        );
        $this->entity->field3();
        $this->setExpectedException(
            'Evheniy\SearchBundle\Model\Exception\FieldNotFoundException',
            'Field field3 doesn\'t exist!'
        );
        $this->entity->getField3();
    }
}