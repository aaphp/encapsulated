<?php
/**
 * Encapsulated - An encapsulation micro framework
 *
 * @link      https://github.com/webdevxp/encapsulated
 * @copyright Copyright (c) 2016 Kosit Supanyo
 * @license   https://github.com/webdevxp/encapsulated/blob/v1.x/LICENSE.md (MIT License)
 */
namespace Encapsulated\Tests;

use Encapsulated\Traits\ContainerAwareEncapsulatorTrait;
use Interop\Container\ContainerInterface;

/**
 * @coversDefaultClass Encapsulated\Traits\ContainerAwareEncapsulatorTrait
 */
class ContainerAwareEncapsulatorTest extends EncapsulatorTest implements
    ContainerInterface
{
    use ContainerAwareEncapsulatorTrait;

    protected $container;

    public function setup()
    {
        parent::setup();
        $this->container = $this;
    }

    public function has($id)
    {
        return false;
    }

    public function get($id)
    {

    }
}
