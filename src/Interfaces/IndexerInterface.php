<?php
/**
 * Encapsulated - An encapsulation micro framework
 *
 * @link      https://github.com/aaphp/encapsulated
 * @copyright Copyright (c) 2016 Kosit Supanyo
 * @license   https://github.com/aaphp/encapsulated/blob/v1.x/LICENSE.md (MIT License)
 */
namespace aaphp\Encapsulated\Interfaces;

use ArrayAccess;
use Countable;
use IteratorAggregate;

interface IndexerInterface extends ArrayAccess, Countable, IteratorAggregate
{
    public function toArray();
}
