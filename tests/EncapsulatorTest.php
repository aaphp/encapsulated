<?php
/**
 * Encapsulated - An encapsulation micro framework
 *
 * @link      https://github.com/webdevxp/encapsulated
 * @copyright Copyright (c) 2016 Kosit Supanyo
 * @license   https://github.com/webdevxp/encapsulated/blob/v1.x/LICENSE.md (MIT License)
 */
namespace Encapsulated\Tests;

use Encapsulated\Encapsulator;
use Encapsulated\Traits\EncapsulatorTrait;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @coversDefaultClass Encapsulated\Traits\EncapsulatorTrait
 */
class EncapsulatorTest extends TestCase
{
    use EncapsulatorTrait;

    protected static $defaultPropDefs = [
        'hello'     => Encapsulator::EAGER,
        'std'       => Encapsulator::LAZY,
        'arr'       => Encapsulator::LAZY,
        'err'       => Encapsulator::LAZY,
        'hasDefaultValue' => [
            'default' => 'This is a default value',
        ],
        'enumWithDefault' => [
            'enum' => [1, 2, 3],
            'default' => 0,
        ],
        'something' => [
            'getter' => 'getSomething',
            'setter' => 'setSomething',
        ],
        'readOnly' => [
            'getter' => 'getterWithoutSetter',
        ],
        'writeOnly' => [
            'setter' => 'setterWithoutGetter',
        ],
        'intValue' => 'int',
        'floatValue' => ['type' => 'float'],
        'arrayValue' => [
            'type' => 'array',
            'keys' => [
                'default' => ['type' => 'int', 'default' => 123],
                'int' => 'int',
                'str' => 'string',
                'obj' => 'stdClass',
            ],
        ],
        'arrayValueNoKeys' => ['type' => 'array'],
        'enumValue' => ['enum' => [1, 2, 3], 'default' => 0],
        'patternValue' => ['pattern' => '/\d+$/A'],
        'minMaxValue' => ['min' => 1, 'max' => 10],
        'strictMinMaxValue' => ['min' => 1, 'max' => 10, 'strict' => true],
    ];

    protected $propDefs;
    protected $props = [
        'hello' => 'Hello World!!!',
    ];
    protected $allowsUndefinedName = false;
    protected $something = 'something';

    protected function getLazyPropertyValue($name)
    {
        switch ($name) {
            case 'std':
                return new \stdClass();
            case 'arr':
                return new \ArrayObject();
            case 'err':
                return new \Exception();
            default:
                break;
        }
    }

    protected function getSomething()
    {
        return $this->something;
    }

    protected function setSomething($value)
    {
        $this->something = $value;
    }

    protected function getterWithoutSetter()
    {

    }

    protected function setterWithoutGetter($value)
    {

    }

    public function setup()
    {
        $this->propDefs = self::$defaultPropDefs;
    }

    /**
     * @covers ::__get
     */
    public function testEagerGet()
    {
        $this->assertSame($this->hello, 'Hello World!!!');
    }

    /**
     * @covers ::__get
     */
    public function testLazyGet()
    {
        $this->assertInstanceOf('stdClass', $this->std);
        $this->assertInstanceOf('ArrayObject', $this->arr);
        $this->assertInstanceOf('Exception', $this->err);
    }
    
    /**
     * @covers ::__get
     */
    public function testDefaultValue()
    {
        $this->assertFalse(is_null($this->hasDefaultValue));
    }
    
    /**
     * @covers ::__get
     * @covers ::__set
     */
    public function testAllowUndefinedName()
    {
        $this->allowsUndefinedName = true;
        $this->assertNull($this->undefinedValue);
        $this->undefinedValue = true;
        $this->assertTrue($this->undefinedValue);
        $this->allowsUndefinedName = false;
    }
    
    /**
     * @covers ::__isset
     */
    public function testIsset()
    {
        foreach ($this->propDefs as $name => $unused) {
            isset($this->{$name});
        }
        $this->assertFalse(isset($this->xyz));
    }
    
    /**
     * @covers ::__unset
     * @expectedException InvalidArgumentException
     */
    public function testUndefinedPropertyUnset()
    {
        unset($this->xyz);
    }

    /**
     * @covers ::__unset
     * @expectedException InvalidArgumentException
     */
    public function testUnset()
    {
        foreach ($this->propDefs as $name => $unused) {
            unset($this->{$name});
        }
    }
    
    /**
     * @covers ::__unset
     */
    public function testDefaultUnset()
    {
        $this->props['hasDefaultValue'] = 123;
        $this->assertSame($this->hasDefaultValue, 123);
        unset($this->hasDefaultValue);
        $this->assertSame(
            $this->hasDefaultValue,
            $this->propDefs['hasDefaultValue']['default']
        );
        $this->enumWithDefault = 3;
        unset($this->enumWithDefault);
        $this->assertSame(
            $this->enumWithDefault,
            $this->propDefs['enumWithDefault']['enum'][$this->propDefs['enumWithDefault']['default']]
        );
    }

    /**
     * @covers ::__get
     */
    public function testGetterSetter()
    {
        $this->assertSame($this->something, 'something');
        $this->something = 'changed';
        $this->assertSame($this->something, 'changed');
    }

    /**
     * @covers ::__get
     * @expectedException InvalidArgumentException
     */
    public function testUndefinedPropertyGet()
    {
        $this->helloWorld;
    }

    /**
     * @covers ::__get
     * @expectedException InvalidArgumentException
     */
    public function testUndefinedPropertySet()
    {
        $this->helloWorld = 'Hello World!!!';
    }

    /**
     * @covers ::__set
     * @expectedException InvalidArgumentException
     */
    public function testReadOnlyPropertySet()
    {
        $this->hello = 'This should throw exception';
    }

    /**
     * @covers ::__set
     * @expectedException InvalidArgumentException
     */
    public function testGetterWithoutSetter()
    {
        $this->readOnly = 'This should throw exception';
    }

    /**
     * @covers ::__get
     * @expectedException InvalidArgumentException
     */
    public function testSetterWithoutGetter()
    {
        $this->writeOnly + 1;
    }

    /**
     * @covers ::__set
     * @expectedException InvalidArgumentException
     */
    public function testTypeMismatch()
    {
        $this->intValue = 'Hello';
    }

    /**
     * @covers ::__set
     * @covers Encapsulated\Traits\ValidatorTrait
     * @expectedException InvalidArgumentException
     */
    public function testTypeMismatch2()
    {
        $this->floatValue = 'Hello';
    }

    /**
     * @covers ::__set
     * @covers Encapsulated\Traits\ValidatorTrait
     */
    public function testArrayMember()
    {
        $this->arrayValueNoKeys = [];
        $this->arrayValue = [
            'default' => null,
            'int'     => '123',
            'str'     => 'Hello',
            'obj'     => new \stdClass(),
        ];
        $this->assertTrue(!is_null($this->arrayValue['default']));
        $this->assertSame($this->arrayValue['int'], 123);
    }

    /**
     * @covers ::__set
     * @covers Encapsulated\Traits\ValidatorTrait
     * @expectedException InvalidArgumentException
     */
    public function testArrayNotArray()
    {
        $this->arrayValue = 123;
    }

    /**
     * @covers ::__set
     * @covers Encapsulated\Traits\ValidatorTrait
     * @expectedException InvalidArgumentException
     */
    public function testArrayMemberTypeMismatch()
    {
        $this->arrayValue = [
            'str' => 'Hello',
            'obj' => 123,
        ];
    }

    /**
     * @covers ::__set
     * @covers Encapsulated\Traits\ValidatorTrait
     * @expectedException InvalidArgumentException
     */
    public function testEnum()
    {
        $this->assertSame($this->enumValue, 1);
        $this->enumValue = 1;
        $this->enumValue = 2;
        $this->enumValue = 3;
        $this->enumValue = 4;
    }

    /**
     * @covers ::__set
     * @covers Encapsulated\Traits\ValidatorTrait
     * @expectedException InvalidArgumentException
     */
    public function testPattern()
    {
        $this->patternValue = 10;
        $this->patternValue = 'Hello';
    }

    /**
     * @covers ::__set
     * @covers Encapsulated\Traits\ValidatorTrait
     */
    public function testMinMax()
    {
        $this->minMaxValue = 1;
        $this->minMaxValue = 0;
        $this->assertTrue($this->minMaxValue === 1);
        $this->minMaxValue = 11;
        $this->assertTrue($this->minMaxValue === 10);
    }

    /**
     * @covers ::__set
     * @covers Encapsulated\Traits\ValidatorTrait
     * @expectedException InvalidArgumentException
     */
    public function testStrictMin()
    {
        $this->strictMinMaxValue = 0;
    }

    /**
     * @covers ::__set
     * @covers Encapsulated\Traits\ValidatorTrait
     * @expectedException InvalidArgumentException
     */
    public function testStrictMax()
    {
        $this->strictMinMaxValue = 11;
    }
}
