<?php
/**
 * ConversionModuleInterface.php
 * 11/12/13
 *
 * @author: Jeremy Thacker <thackerj@uci.edu>
 */

namespace UCI\TypeConverter;


/**
 * Interface ConversionModuleInterface
 *
 * A conversion module contains algorithms for converting between data types.
 * A class that implements this interface can be passed to a TypeConverterBuilder
 * to have its conversion methods added to a TypeConverter. The TypeConverter
 * can then utilize these methods to perform conversions between data types.
 *
 * Each conversion consists of:
 *    A) The "from" data type. It should be a fully-qualified class name, or
 *       the name of a primitive that would result from gettype().
 *    B) The "to" data type. It should be named as above.
 *    C) A callback that accepts the "from" type as an argument and returns
 *       the "to" type.
 *
 * The method implementations that perform the conversions can go in this class,
 * but don't have to. The callback that is provided to the ConversionCollection
 * can be any valid callback whether its a member of this class, a member of
 * another class, or an anonymous function.
 *
 * Any necessary factories or providers for making "to" types can be added to
 * the Conversion Module on construction before passing the Conversion Module
 * to the TypeConverterBuilder.
 *
 * @package UCI\TypeConverter
 */
interface ConversionModuleInterface
{
    /**
     * Adds a module's conversion methods to a ConversionCollection.
     *
     * This method receives a ConversionCollection instance, which the module is
     * to add its own conversion methods to.
     *
     * An example for a conversion module that converts between the classes
     * Foo and bar:
     *
     *     $conversion_collection
     *         ->addConversion(
     *             'MyNamespace\Foo',    // from
     *             'MyNamespace\Bar',    // to
     *             [$this, 'fooToBar']   // callback
     *         )
     *         ->addConversion(
     *             'MyNamespace\Bar',
     *             'MyNamespace\Foo',
     *             [$this, 'barToFoo']
     *         )
     *
     * In the example above, the conversion module should contain the following
     * two methods:
     *
     *     public function fooToBar(\MyNamespace\Foo $foo) {
     *        // do stuff
     *        return $bar;
     *     }
     *
     *     public function barToFoo(\MyNamespace\Bar $bar) {
     *        // do stuff
     *        return $foo;
     *     }
     *
     * @param ConversionCollection $conversion_collection
     * @return void
     */
    public function register(ConversionCollection $conversion_collection);
} 