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
use aaphp\Encapsulated\Traits\IndexerMutableTrait;

class IndexerMutable implements IndexerInterface
{
    use IndexerMutableTrait;

    private $storageDefs;
    private $storage;
    private $allowsUndefinedKey;

    public function __construct(
        array $definitions,
        array &$values = [],
        $allowsUndefinedKey = true
    ) {
        $this->storageDefs = $definitions;
        $this->storage = &$values;
        $this->allowsUndefinedKey = (bool)$allowsUndefinedKey;
        foreach ($values as $offset => $value) {
            $this->offsetSet($offset, $value);
        }
    }
}
