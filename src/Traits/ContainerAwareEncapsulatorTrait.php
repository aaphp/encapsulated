<?php
/**
 * Encapsulated - An encapsulation micro framework
 *
 * @link      https://github.com/aaphp/encapsulated
 * @copyright Copyright (c) 2016 Kosit Supanyo
 * @license   https://github.com/aaphp/encapsulated/blob/v1.x/LICENSE.md (MIT License)
 */
namespace aaphp\Encapsulated\Traits;

use aaphp\Utilized\VarUtil;
use InvalidArgumentException;

trait ContainerAwareEncapsulatorTrait
{
    use ValidatorTrait;

    // private $propDefs = [];
    // private $props = [];
    // private $allowsUndefinedName = false;
    // private $container;

    public function __get($name)
    {
        if (isset($this->props[$name])) {
            return $this->props[$name];
        }
        if (!isset($this->propDefs[$name])) {
            if ($name === 'container') {
                return $this->container;
            }
            if ($this->container->has($name)) {
                return $this->container->get($name);
            }
            if (empty($this->allowsUndefinedName)) {
                throw new InvalidArgumentException(
                    sprintf('%s::$%s does not exist', get_class($this), $name)
                );
            }
            return;
        }
        if (is_array($def = $this->propDefs[$name])) {
            if (isset($def['default'])) {
                if (   is_int($def['default'])
                    && isset($def['enum'][$def['default']])
                ) {
                    return $this->props[$name] = $def['enum'][$def['default']];
                }
                return $this->props[$name] = $def['default'];
            }
            if (isset($def['getter'])) {
                return $this->{$def['getter']}();
            }
            if (isset($def['setter'])) {
                throw new InvalidArgumentException(
                    sprintf(
                        '%s::$%s is write-only',
                        get_class($this),
                        $name
                    )
                );
            }
            return;
        }
        if (empty($def)) {
            return;
        }
        if (is_string($def)) {
            return;
        }
        return $this->props[$name] = $this->getLazyPropertyValue($name);
    }

    public function __set($name, $value)
    {
        if (!isset($this->propDefs[$name])) {
            if (empty($this->allowsUndefinedName)) {
                throw new InvalidArgumentException(
                    sprintf(
                        $name === 'container' || $this->container->has($name)
                            ? '%s::$%s is read-only'
                            : '%s::$%s does not exist',
                        get_class($this),
                        $name
                    )
                );
            }
            $this->props[$name] = $value;
            return;
        }
        if (is_array($def = $this->propDefs[$name])) {
            if (isset($def['setter'])) {
                $this->{$def['setter']}($name, $value);
                return;
            }
            if (isset($def['getter'])) {
                throw new InvalidArgumentException(
                    sprintf('%s::$%s is read-only', get_class($this), $name)
                );
            }
            if (is_null($error = $this->validate($name, $value, $def))) {
                $this->props[$name] = $value;
                return;
            }
            throw new InvalidArgumentException(
                $this->getErrorMessage($error, get_class($this) . '::$%s')
            );
        }
        if ($def && is_string($def)) {
            if (is_null($tmp = VarUtil::setType($value, $def))) {
                throw new InvalidArgumentException(
                    sprintf(
                        '%s must be %s, %s given',
                        $name,
                        $def,
                        VarUtil::getType($value)
                    )
                );
            }
            $this->props[$name] = $tmp;
            return;
        }
        throw new InvalidArgumentException(
            sprintf('%s::$%s is read-only', get_class($this), $name)
        );
    }

    public function __isset($name)
    {
        if (isset($this->props[$name])) {
            return true;
        }
        if (!isset($this->propDefs[$name])) {
            return $name === 'container'
                ? true
                : $this->container->has($name);
        }
        if (is_array($def = $this->propDefs[$name])) {
            if (isset($def['default'])) {
                $this->props[$name] = is_int($def['default'])
                    && isset($def['enum'][$def['default']])
                    ? $def['enum'][$def['default']]
                    : $def['default'];
                return isset($this->props[$name]);
            }
            if (isset($def['getter'])) {
                return $this->{$def['getter']}() !== null;
            }
            return false;
        }
        if (empty($def)) {
            return false;
        }
        if (is_string($def)) {
            return false;
        }
        $this->props[$name] = $this->getLazyPropertyValue($name);
        return isset($this->props[$name]);
    }

    public function __unset($name)
    {
        if (!isset($this->propDefs[$name])) {
            if (empty($this->allowsUndefinedName)) {
                throw new InvalidArgumentException(
                    sprintf(
                        $name === 'container' || $this->container->has($name)
                            ? '%s::$%s is read-only'
                            : '%s::$%s does not exist',
                        get_class($this),
                        $name
                    )
                );
            }
            unset($this->props[$name]);
            return;
        }
        if (is_array($def = $this->propDefs[$name])) {
            if (isset($def['default'])) {
                if (   is_int($def['default'])
                    && isset($def['enum'][$def['default']])
                ) {
                    $this->props[$name] = $def['enum'][$def['default']];
                } else {
                    $this->props[$name] = $def['default'];
                }
                return;
            }
            if (isset($def['setter'])) {
                $this->{$def['setter']}(null);
                return;
            }
            if (isset($def['getter'])) {
                throw new InvalidArgumentException(
                    sprintf('%s::$%s is read-only', get_class($this), $name)
                );
            }
            if (isset($def['type']) && empty($def['null'])) {
                throw new InvalidArgumentException(
                    sprintf(
                        '%s::$%s must be %s, null given',
                        get_class($this),
                        $name,
                        is_array($def['type'])
                            ? 'array'
                            : $def['type']
                    )
                );
            }
            $this->props[$name] = null;
            return;
        }
        if ($def && is_string($def)) {
            throw new InvalidArgumentException(
                sprintf(
                    '%s::$%s must be %s, null given',
                    get_class($this),
                    $name,
                    $def
                )
            );
        }
        throw new InvalidArgumentException(
            sprintf('%s::$%s is read-only', get_class($this), $name)
        );
    }
}
