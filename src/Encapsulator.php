<?php
/**
 * Encapsulated - An encapsulation micro framework
 *
 * @link      https://github.com/aaphp/encapsulated
 * @copyright Copyright (c) 2016 Kosit Supanyo
 * @license   https://github.com/aaphp/encapsulated/blob/v1.x/LICENSE.md (MIT License)
 */
namespace aaphp\Encapsulated;

use aaphp\Encapsulated\Interfaces\EncapsulatorInterface;
use aaphp\Encapsulated\Traits\EncapsulatorTrait;

class Encapsulator implements EncapsulatorInterface
{
    use EncapsulatorTrait;

    public function __construct(array $definitions, array &$values = [])
    {
        $this->propDefs = $definitions;
        $this->props = &$values;
    }
}
