<?php

namespace Evheniy\SearchBundle\Tests\Model\Exception;

use Evheniy\SearchBundle\Model\Exception\FieldNotFoundException;

/**
 * Class FieldNotFoundExceptionTest
 * @package Evheniy\SearchBundle\Tests\Model\Exception
 */
class FieldNotFoundExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @throws FieldNotFoundException
     */
    public function test()
    {
        $this->assertInstanceOf('\Exception', new FieldNotFoundException());
        $this->setExpectedException('Evheniy\SearchBundle\Model\Exception\FieldNotFoundException');
        throw new FieldNotFoundException('test');
    }
}