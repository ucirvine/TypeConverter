<?php
/**
 * TypeConverterTest.php
 * 12/6/13
 *
 * @author: Jeremy Thacker <thackerj@uci.edu>
 */

namespace UCI\Tests\TypeConverter;

use UCI\TypeConverter\TypeConverter;
use Mockery;

class TypeConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TypeConverter
     */
    private $converter;

    public function setUp()
    {
        // Set up a mock ConversionCollection with our test conversions
        $collection = Mockery::mock('UCI\TypeConverter\ConversionCollection');
        $collection
            ->shouldReceive('getConversion')
            ->with(
                'UCI\Tests\TypeConverter\TypeA',
                'UCI\Tests\TypeConverter\TypeB'
            )
            ->andReturn([$this, 'aToB']);
        $collection
            ->shouldReceive('getConversion')
            ->with(
                'UCI\Tests\TypeConverter\TypeB',
                'UCI\Tests\TypeConverter\TypeA'
            )
            ->andReturn([$this, 'BToA']);
        $collection
            ->shouldReceive('getConversion')
            ->with(
                'UCI\Tests\TypeConverter\SuperTypeA',
                'UCI\Tests\TypeConverter\SuperTypeB'
            )
            ->andReturn([$this, 'superTypeAToSuperTypeB']);

        $this->converter = new TypeConverter($collection);
    }

    public function testConvert()
    {
        // Convert TypeA to TypeB
        $a = new TypeA();
        $a->valueA = 'foo';
        $b = $this->converter->convert($a, 'UCI\Tests\TypeConverter\TypeB');
        $this->assertInstanceOf('UCI\Tests\TypeConverter\TypeB', $b);
        $this->assertEquals($a->valueA, $b->valueB);

        // Convert TypeB back to TypeA
        $b->valueB = 'bar';
        $a = $this->converter->convert($b, 'UCI\Tests\TypeConverter\TypeA');
        $this->assertInstanceOf('UCI\Tests\TypeConverter\TypeA', $a);
        $this->assertEquals($b->valueB, $a->valueA);
    }

    public function testRecursiveConvert()
    {
        $super_a = new SuperTypeA();
        $super_a->typeA = new TypeA();
        $super_a->typeA->valueA = 'foo';
        $super_b = $this->converter->convert($super_a, 'UCI\Tests\TypeConverter\SuperTypeB');
        $this->assertInstanceOf('UCI\Tests\TypeConverter\SuperTypeB', $super_b);
        $this->assertInstanceOf('UCI\Tests\TypeConverter\TypeB', $super_b->typeB);
        $this->assertEquals($super_b->typeB->valueB, $super_a->typeA->valueA);
    }

    /**
     * A conversion function for converting TypeA to TypeB
     *
     * @param TypeA $a
     * @return TypeB
     */
    public function aToB(TypeA $a)
    {
        $b = new TypeB();
        $b->valueB = $a->valueA;
        return $b;
    }

    /**
     * A conversion function for converting TypeB to TypeA
     *
     * @param TypeB $b
     * @return TypeA
     */
    public function bToA(TypeB $b)
    {
        $a = new TypeA();
        $a->valueA = $b->valueB;
        return $a;
    }

    /**
     * A conversion function for converting SuperTypeA to SuperTypeB
     * @param SuperTypeA $super_a
     * @param TypeConverter $type_converter
     * @return SuperTypeB
     */
    public function superTypeAToSuperTypeB(SuperTypeA $super_a, TypeConverter $type_converter)
    {
        $super_b = new SuperTypeB();
        $super_b->typeB = $type_converter->convert($super_a->typeA, 'UCI\Tests\TypeConverter\TypeB');
        return $super_b;
    }
}

/**
 * Class TypeA
 *
 * A lightweight class for testing type conversion
 */
class TypeA
{
    public $valueA;
}

/**
 * Class TypeB
 *
 * Another lightweight class for testing type conversion
 */
class TypeB
{
    public $valueB;
}

/**
 * Class SuperTypeA
 *
 * A class that contains a TypeA
 */
class SuperTypeA
{
    /**
     * @var TypeA
     */
    public $typeA;
}

/**
 * Class SuperTypeB
 *
 * A class that contains a TypeB
 */
class SuperTypeB
{
    /**
     * @var TypeB
     */
    public $typeB;
}