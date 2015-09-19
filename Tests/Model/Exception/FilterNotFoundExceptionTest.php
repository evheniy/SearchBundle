<?php

namespace Evheniy\SearchBundle\Tests\Model\Exception;

use Evheniy\SearchBundle\Model\Exception\FilterNotFoundException;

/**
 * Class FilterNotFoundExceptionTest
 * @package Evheniy\SearchBundle\Tests\Model\Exception
 */
class FilterNotFoundExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @throws FilterNotFoundException
     */
    public function test()
    {
        $this->assertInstanceOf('\Exception', new FilterNotFoundException());
        $this->setExpectedException('Evheniy\SearchBundle\Model\Exception\FilterNotFoundException');
        throw new FilterNotFoundException('test');
    }
}