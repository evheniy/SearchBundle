<?php

namespace Evheniy\SearchBundle\Tests\Model\Collection;
use Evheniy\SearchBundle\Model\Collection\FacetCollection;

/**
 * Class FacetCollectionTest
 *
 * @package Evheniy\SearchBundle\Tests\Model\Collection
 */
class FacetCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DocumentCollection
     */
    protected $collection;

    /**
     *
     */
    public function setUp()
    {
        $this->collection = new FacetCollection(
            array('field1', 'field2'),
            array(
                array(1,1),
                array(2,2)
            )
        );
    }
    /**
     *
     */
    public function testConstruct()
    {
        $this->assertInstanceOf('Evheniy\SearchBundle\Model\Collection\AbstractCollection', $this->collection);
        $this->assertInstanceOf('ArrayObject', $this->collection);
    }

    /**
     *
     */
    public function testGetEntities()
    {
        $this->assertCount(2, $this->collection);
        $this->assertEquals(array(1,1), $this->collection[0]);
    }
}