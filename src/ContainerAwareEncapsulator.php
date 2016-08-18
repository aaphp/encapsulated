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
use aaphp\Encapsulated\Traits\ContainerAwareEncapsulatorTrait;

class ContainerAwareEncapsulator implements EncapsulatorInterface
{
    use ContainerAwareEncapsulatorTrait;

    public function __construct(
        ContainerInterface $container,
        array $definitions,
        array &$values = []
    ) {
        $this->propDefs = $definitions;
        $this->props = &$values;
        $this->container = $container;
    }
}
