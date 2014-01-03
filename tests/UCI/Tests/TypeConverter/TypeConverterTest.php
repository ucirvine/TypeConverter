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