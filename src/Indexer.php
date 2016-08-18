<?php
/**
 * Encapsulated - An encapsulation micro framework
 *
 * @link      https://github.com/aaphp/encapsulated
 * @copyright Copyright (c) 2016 Kosit Supanyo
 * @license   https://github.com/aaphp/encapsulated/blob/v1.x/LICENSE.md (MIT License)
 */
namespace aaphp\Encapsulated;

use aaphp\Encapsulated\Interfaces\IndexerInterface;
use aaphp\Encapsulated\Traits\IndexerTrait;

class Indexer implements IndexerInterface
{
    use IndexerTrait;

    private $storage;

    public function __construct(array &$storage = null)
    {
        if (is_null($storage)) {
            $this->storage = [];
        } else {
            $this->storage = &$storage;
        }
    }
}
