<?php
/**
 * ConversionCollectionTest.php
 * 12/5/13
 *
 * @author: Jeremy Thacker <thackerj@uci.edu>
 */

namespace Tests\UCI\TypeConverter;

use UCI\TypeConverter\ConversionCollection;

class ConversionCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ConversionCollection
     */
    private $collection;

    /**
     * @var callable
     */
    private $funcA, $funcB, $funcC;

    public function setUp()
    {
        $this->funcA = function() { return 'foo'; };
        $this->funcB = function() { return 'bar'; };
        $this->funcC = function() { return 'baz'; };

        $this->collection = new ConversionCollection();
        $this->collection
            ->addConversion('MyNamespace\MyClass', 'foo', $this->funcA)
            ->addConversion('MyNamespace\MyClass', 'bar', $this->funcB)
            ->addConversion('MyNamespace\MyClass', 'baz', $this->funcC);
    }

    public function testGetConversion()
    {
        $func_a = $this->funcA;
        $func_b = $this->funcB;
        $func_c = $this->funcC;

        $func_x = $this->collection->getConversion('MyNamespace\MyClass', 'foo');
        $this->assertEquals($func_a(), $func_x());

        $func_x = $this->collection->getConversion('MyNamespace\MyClass', 'baz');
        $this->assertEquals($func_c(), $func_x());

        $func_x = $this->collection->getConversion('MyNamespace\MyClass', 'bar');
        $this->assertEquals($func_b(), $func_x());
    }

    /**
     * Make sure that an exception is thrown if an unrecognized source type is
     * passed to getConversion().
     *
     * @expectedException \UCI\TypeConverter\TypeConverterException
     */
    public function testGetConversionExceptionNoSource()
    {
        $this->collection->getConversion('SomeClass', 'foo');
    }

    /**
     * Make sure that an exception is thrown if an unrecognized target type is
     * passed to getConversion().
     *
     * @expectedException \UCI\TypeConverter\TypeConverterException
     */
    public function testGetConversionExceptionNoTarget()
    {
        $this->collection->getConversion('MyNamespace\MyClass', 'beep');
    }
} 