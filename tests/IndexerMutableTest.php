<?php
/**
 * Encapsulated - An encapsulation micro framework
 *
 * @link      https://github.com/webdevxp/encapsulated
 * @copyright Copyright (c) 2016 Kosit Supanyo
 * @license   https://github.com/webdevxp/encapsulated/blob/v1.x/LICENSE.md (MIT License)
 */
namespace Encapsulated\Tests;

use Encapsulated\Traits\IndexerMutableTrait;

/**
 * @coversDefaultClass Encapsulated\Traits\IndexerMutableTrait
 */
class IndexerMutableTest extends IndexerTest
{
    use IndexerMutableTrait;

    protected $storageDefs = [];
    protected $allowsUndefinedKey = false;
}
