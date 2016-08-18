<?php
/**
 * Encapsulated - An encapsulation micro framework
 *
 * @link      https://github.com/webdevxp/encapsulated
 * @copyright Copyright (c) 2016 Kosit Supanyo
 * @license   https://github.com/webdevxp/encapsulated/blob/v1.x/LICENSE.md (MIT License)
 */
namespace Encapsulated\Tests;

use Encapsulated\Traits\IndexerTrait;
use Encapsulated\Interfaces\IndexerInterface;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @coversDefaultClass Encapsulated\Traits\IndexerTrait
 */
class IndexerTest extends TestCase implements IndexerInterface
{
    use IndexerTrait;

    protected $storage = [
        'a' => 1,
        'b' => 2,
        'c' => 3,
    ];

    /**
     * @covers ::offsetGet
     * @covers ::offsetExists
     */
    public function testGet()
    {
        $this->assertSame($this['a'], 1);
        $this->assertSame($this['b'], 2);
        $this->assertSame($this['c'], 3);
        $this->assertTrue(isset($this['a']));
        $this->assertFalse(isset($this['x']));
    }

    /**
     * @covers ::offsetSet
     * @expectedException InvalidArgumentException
     */
    public function testSet()
    {
        $this['a'] = 'Hello';
    }

    /**
     * @covers ::offsetUnset
     * @expectedException InvalidArgumentException
     */
    public function testUnset()
    {
        unset($this['a']);
    }

    /**
     * @covers ::count
     * @covers ::getIterator
     * @covers ::toArray
     */
    public function testMisc()
    {
        $this->assertSame(count($this), 3);
        $this->assertInstanceOf('ArrayIterator', $this->getIterator());
        $this->assertInternalType('array', $this->toArray());
    }
}
