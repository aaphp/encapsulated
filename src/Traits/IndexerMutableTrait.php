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
use aaphp\Utilized\VarUtil;
use InvalidArgumentException;

trait IndexerMutableTrait
{
    use ValidatorTrait;

    // private $storageDefs = [];
    // private $storage = [];
    // private $allowsUndefinedKey = true;

    public function offsetGet($offset)
    {
        if (isset($this->storage[$offset])) {
            return $this->storage[$offset];
        }
        if (!isset($this->storageDefs[$offset])) {
            if (empty($this->allowsUndefinedKey)) {
                throw new InvalidArgumentException(
                    sprintf("Key '%s' does not exists", $offset)
                );
            }
            return;
        }
        if (is_array($def = $this->storageDefs[$offset])) {
            if (isset($def['getter'])) {
                return $this->{$def['getter']}();
            }
            if (isset($def['default'])) {
                if (   is_int($def['default'])
                    && isset($def['enum'][$def['default']])
                ) {
                    return $this->storage[$offset] = $def['enum'][$def['default']];
                }
                return $this->storage[$offset] = $def['default'];
            }
        }
    }

    public function offsetSet($offset, $value)
    {
        if (!isset($this->storageDefs[$offset])) {
            if (empty($this->allowsUndefinedKey)) {
                throw new InvalidArgumentException(
                    sprintf("Key '%s' does not exists", $offset)
                );
            }
            $this->storage[$offset] = $value;
            return;
        }
        if (is_array($def = $this->storageDefs[$offset])) {
            if (isset($def['setter'])) {
                $this->{$def['setter']}($value);
                return;
            }
            if (isset($def['getter'])) {
                throw new InvalidArgumentException(
                    sprintf("Key '%s' is read-only", $offset)
                );
            }
            if (is_null($error = $this->validate($offset, $value, $def))) {
                $this->storage[$offset] = $value;
                return;
            }
            throw new InvalidArgumentException(
                $this->getErrorMessage($error, "Key '%s'")
            );
        }
        if (empty($def)) {
            throw new InvalidArgumentException(
                sprintf("Key '%s' is read-only", $offset)
            );
        }
        if (is_string($def)) {
            if (is_null($tmp = VarUtil::setType($value, $def))) {
                throw new InvalidArgumentException(
                    sprintf(
                        "Key '%s' must be %s, %s given",
                        $offset,
                        $def,
                        VarUtil::getType($value)
                    )
                );
            }
            $this->storage[$offset] = $tmp;
        } else {
            $this->storage[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        if (isset($this->storage[$offset])) {
            return true;
        }
        if (!isset($this->storageDefs[$offset])) {
            return false;
        }
        if (is_array($def = $this->storageDefs[$offset])) {
            if (isset($def['getter'])) {
                return $this->{$def['getter']}() !== null;
            }
            if (isset($def['default'])) {
                if (   is_int($def['default'])
                    && isset($def['enum'][$def['default']])
                ) {
                    $this->storage[$offset] = $def['enum'][$def['default']];
                } else {
                    $this->storage[$offset] = $def['default'];
                }
                return isset($this->storage[$offset]);
            }
        }
        return false;
    }

    public function offsetUnset($offset)
    {
        if (!isset($this->storageDefs[$offset])) {
            if (empty($this->allowsUndefinedKey)) {
                throw new InvalidArgumentException(
                    sprintf("Key '%s' does not exists", $offset)
                );
            }
            unset($this->storage[$offset]);
            return;
        }
        if (is_array($def = $this->storageDefs[$offset])) {
            if (isset($def['setter'])) {
                $this->{$def['setter']}(null);
                return;
            }
            if (isset($def['getter'])) {
                throw new InvalidArgumentException(
                    sprintf("Key '%s' is read-only", $offset)
                );
            }
            if (isset($def['type']) && empty($def['null'])) {
                throw new InvalidArgumentException(
                    sprintf(
                        "Key '%s' must be %s, null given",
                        $offset,
                        is_array($def['type'])
                            ? 'array'
                            : $def['type']
                    )
                );
            }
            $this->storage[$offset] = null;
            return;
        }
        if (empty($def)) {
            throw new InvalidArgumentException(
                sprintf("Key '%s' is read-only", $offset)
            );
        }
        if (is_string($def)) {
            throw new InvalidArgumentException(
                sprintf(
                    "Key '%s' must be %s, null given",
                    $offset,
                    $def
                )
            );
        }
        if (isset($def['default'])) {
            if (   is_int($def['default'])
                && isset($def['enum'][$def['default']])
            ) {
                $this->storage[$offset] = $def['enum'][$def['default']];
            } else {
                $this->storage[$offset] = $def['default'];
            }
        } else {
            $this->storage[$offset] = null;
        }
    }

    public function count()
    {
        return count($this->toArray());
    }

    public function getIterator()
    {
        return new ArrayIterator($this->toArray());
    }

    public function toArray()
    {
        foreach ($this->storageDefs as $offset => $def) {
            if (isset($this->storage[$offset])) {
                continue;
            }
            if (isset($def['getter'])) {
                $array[$offset] = $this->{$def['getter']}();
                continue;
            }
            if (isset($def['default'])) {
                if (   is_int($def['default'])
                    && isset($def['enum'][$def['default']])
                ) {
                    $array[$offset] = $this->storage[$offset] = $def['enum'][$def['default']];
                } else {
                    $array[$offset] = $this->storage[$offset] = $def['default'];
                }
            }
        }
        if (empty($array)) {
            return $this->storage;
        }
        return $array + $this->storage;
    }
}
