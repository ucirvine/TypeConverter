<?php
/**
 * TypeConverterTest.php
 * 12/6/13
 *
 * @author: Jeremy Thacker <thackerj@uci.edu>
 */

namespace Tests\UCI\TypeConverter;

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
                'Tests\UCI\TypeConverter\TypeA',
                'Tests\UCI\TypeConverter\TypeB'
            )
            ->andReturn([$this, 'aToB']);
        $collection
            ->shouldReceive('getConversion')
            ->with(
                'Tests\UCI\TypeConverter\TypeB',
                'Tests\UCI\TypeConverter\TypeA'
            )
            ->andReturn([$this, 'BToA']);

        $this->converter = new TypeConverter($collection);
    }

    public function testConvert()
    {
        // Convert TypeA to TypeB
        $a = new TypeA();
        $a->valueA = 'foo';
        $b = $this->converter->convert($a, 'Tests\UCI\TypeConverter\TypeB');
        $this->assertInstanceOf('Tests\UCI\TypeConverter\TypeB', $b);
        $this->assertEquals($a->valueA, $b->valueB);

        // Convert TypeB back to TypeA
        $b->valueB = 'bar';
        $a = $this->converter->convert($b, 'Tests\UCI\TypeConverter\TypeA');
        $this->assertInstanceOf('Tests\UCI\TypeConverter\TypeA', $a);
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