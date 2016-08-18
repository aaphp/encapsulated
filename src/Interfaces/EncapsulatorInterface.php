<?php
/**
 * Encapsulated - An encapsulation micro framework
 *
 * @link      https://github.com/aaphp/encapsulated
 * @copyright Copyright (c) 2016 Kosit Supanyo
 * @license   https://github.com/aaphp/encapsulated/blob/v1.x/LICENSE.md (MIT License)
 */
namespace aaphp\Encapsulated\Interfaces;

interface EncapsulatorInterface
{
    const EAGER = false;
    const LAZY  = true;

    public function __get($name);
    public function __set($name, $value);
    public function __isset($name);
    public function __unset($name);
}
