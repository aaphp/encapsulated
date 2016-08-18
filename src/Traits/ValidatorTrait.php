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

trait ValidatorTrait
{
    protected function validate($name, &$value, array $def)
    {
        if (isset($def['type'])) {
            if (is_null($value)) {
                if (isset($def['default'])) {
                    $value = $def['default'];
                } elseif (empty($def['null'])) {
                    return [$name, $value, $def, 'type'];
                }
                return;
            }
            if (($type = $def['type']) === 'array') {
                if (!is_array($value)) {
                    return [$name, $value, $def, 'type'];
                }
                if (isset($def['keys'])) {
                    foreach ($def['keys'] as $key => $childDef) {
                        if (!is_array($childDef)) {
                            $childDef = ['type' => $childDef];
                        }
                        $error = $this->validate(
                            "{$name}[{$key}]",
                            $value[$key],
                            $childDef
                        );
                        if (isset($error)) {
                            return $error;
                        }
                    }
                }
                return;
            }
            if (is_null($tmp = VarUtil::setType($value, $type))) {
                return [$name, $value, $def, 'type'];
            }
            $value = $tmp;
        }
        if (isset($def['enum'])) {
            if (in_array($value, (array)$def['enum'], true)) {
                return;
            }
            return [$name, $value, $def, 'enum'];
        }
        if (isset($def['pattern'])) {
            if (preg_match($def['pattern'], $value)) {
                return;
            }
            return [$name, $value, $def, 'pattern'];
        }
        if (isset($def['min']) && $value < $def['min']) {
            if (!empty($def['strict'])) {
                return [$name, $value, $def, 'min'];
            }
            $value = $def['min'];
        } elseif (isset($def['max']) && $value > $def['max']) {
            if (!empty($def['strict'])) {
                return [$name, $value, $def, 'max'];
            }
            $value = $def['max'];
        }
    }

    protected function getErrorMessage(array $error, $nameFormat = null)
    {
        static $default = [null, null, null, null];
        list($name, $value, $def, $rule) = $error + $default;
        if (isset($nameFormat)) {
            $name = sprintf($nameFormat, $name);
        }
        switch ($rule) {
            case 'type':
                return sprintf(
                    '%s must be %s, %s given',
                    $name,
                    $def['type'],
                    VarUtil::getType($value)
                );
            case 'enum':
                return sprintf(
                    '%s must be one of [%s], %s given',
                    $name,
                    "'" . implode("', '", $def['enum']) . "'",
                    VarUtil::stringConvertible($value)
                        ? "'" . $value . "'"
                        : VarUtil::getType($value)
                );
            case 'pattern':
                return sprintf(
                    "%s does not match pattern '%s'",
                    $name,
                    $def['pattern']
                );
            case 'min':
                return sprintf(
                    '%s must not be less than %s',
                    $name,
                    $def['min']
                );
            case 'max':
                return sprintf(
                    '%s must not be greater than %s',
                    $name,
                    $def['max']
                );
            // @codeCoverageIgnoreStart
            default:
                break;
        }
    }
    // @codeCoverageIgnoreEnd
}
