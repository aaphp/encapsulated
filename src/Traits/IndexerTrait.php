<?php
/**
 * Encapsulated - An encapsulation micro framework
 *
 * @link      https://github.com/aaphp/encapsulated
 * @copyright Copyright (c) 2016 Kosit Supanyo
 * @license   https://github.com/aaphp/encapsulated/blob/v1.x/LICENSE.md (MIT License)
 */
namespace aaphp\Encapsulated\Traits;

use ArrayIterator;
use InvalidArgumentException;

trait IndexerTrait
{
    // private $storage = [];

    public function offsetExists($offset)
    {
        return isset($this->storage[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->storage[$offset])
            ? $this->storage[$offset]
            : null;
    }

    public function offsetSet($offset, $value)
    {
        throw new InvalidArgumentException('Offsets are read-only');
    }

    public function offsetUnset($offset)
    {
        throw new InvalidArgumentException('Offsets are read-only');
    }

    public function count()
    {
        return count($this->storage);
    }

    public function getIterator()
    {
        return new ArrayIterator($this->storage);
    }

    public function toArray()
    {
        return $this->storage;
    }
}
