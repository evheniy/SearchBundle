<?php

namespace Evheniy\SearchBundle\Tests\Model\Entity;

use Evheniy\SearchBundle\Model\Entity\DocumentEntity;

/**
 * Class DocumentEntityTest
 * @package Evheniy\SearchBundle\Tests\Model\Entity
 */
class DocumentEntityTest extends \PHPUnit_Framework_TestCase
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
        $this->entity = DocumentEntity::createFromArray(
            array('documentField1', 'documentField2'),
            array(
                'documentField1' => 1,
                'documentField2' => 2
            )
        );
    }

    /**
     *
     */
    public function testCreateFromArray()
    {
        $entity = DocumentEntity::createFromArray(
            array('documentField3', 'documentField4'),
            array('documentField3' => 3)
        );
        $this->assertInstanceOf('Evheniy\SearchBundle\Model\Entity\DocumentEntity', $entity);
        $this->assertTrue(!empty($entity->documentField3));
        $this->assertEquals($entity->documentField3, 3);
        $this->assertTrue(isset($entity->documentField4));
        $this->assertTrue(empty($entity->documentField4));
    }

    /**
     *
     */
    public function testGet()
    {
        $this->assertTrue(!empty($this->entity->documentField1));
        $this->assertEquals($this->entity->documentField1, 1);
        $this->assertTrue(!empty($this->entity->documentField2));
        $this->assertEquals($this->entity->documentField2, 2);
        $this->setExpectedException(
            'Evheniy\SearchBundle\Model\Exception\FieldNotFoundException',
            'Field documentField3 doesn\'t exist!'
        );
        $this->entity->documentField3;
    }

    /**
     *
     */
    public function testSet()
    {
        $this->entity->documentField1 = 5;
        $this->assertEquals($this->entity->documentField1, 5);
    }

    /**
     *
     */
    public function testIsset()
    {
        $this->assertTrue(isset($this->entity->documentField1));
        $this->assertTrue(isset($this->entity->documentField2));
    }

    /**
     *
     */
    public function testCall()
    {
        $this->assertTrue(!empty($this->entity->getDocumentField1()));
        $this->assertEquals($this->entity->getDocumentField1(), 1);
        $this->assertTrue(!empty($this->entity->getDocumentField2()));
        $this->assertEquals($this->entity->getDocumentField2(), 2);
        $this->setExpectedException(
            'Evheniy\SearchBundle\Model\Exception\MethodNotFoundException',
            'Only getFieldName() methods!'
        );
        $this->entity->documentField3();
        $this->setExpectedException(
            'Evheniy\SearchBundle\Model\Exception\FieldNotFoundException',
            'Field documentField3 doesn\'t exist!'
        );
        $this->entity->getDocumentField3();
    }
}