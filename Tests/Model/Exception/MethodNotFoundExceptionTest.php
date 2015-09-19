<?php

namespace Evheniy\SearchBundle\Tests\Model\Exception;

use Evheniy\SearchBundle\Model\Exception\MethodNotFoundException;

/**
 * Class MethodNotFoundExceptionTest
 * @package Evheniy\SearchBundle\Tests\Model\Exception
 */
class MethodNotFoundExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @throws MethodNotFoundException
     */
    public function test()
    {
        $this->assertInstanceOf('\Exception', new MethodNotFoundException());
        $this->setExpectedException('Evheniy\SearchBundle\Model\Exception\MethodNotFoundException');
        throw new MethodNotFoundException('test');
    }
}