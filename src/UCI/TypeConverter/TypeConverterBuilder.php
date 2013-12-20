<?php
/**
 * TypeConverterBuilder.php
 * 11/13/13
 *
 * @author: Jeremy Thacker <thackerj@uci.edu>
 */

namespace UCI\TypeConverter;



/**
 * Class TypeConverterBuilder
 *
 * A factory class for TypeConverters. The builder allows you to add
 * Conversion Modules before actually building the instance.
 *
 * Usage:
 *     $converter = TypeConverterBuilder::create()
 *         ->addConversionModule(new SomeModule())
 *         ->addConversionModule(new SomeOtherModule($dependency))
 *         ->build();
 *
 * @package EEEApply\TypeConverter
 * @author Jeremy Thacker <thackerj@uci.edu>
 */
class TypeConverterBuilder
{
    protected $conversionCollection;

    /**
     * Returns a new TypeConverterBuilder. Because the constructor method
     * is a static function, the new TypeConverterBuilder can immediately
     * be chained.
     *
     * @return TypeConverterBuilder
     */
    public static function create()
    {
        return new TypeConverterBuilder();
    }

    /**
     * Sets up a new ConversionCollection to accumulate conversions in.
     * It will be handed off to the new TypeConverter.
     */
    public function __construct()
    {
        $this->conversionCollection = new ConversionCollection();
    }

    /**
     * Registers a ConversionModule for use with the TypeConverter that we
     * are building.
     *
     * This method can be chained.
     *
     * @param ConversionModuleInterface $module
     * @return $this
     */
    public function addConversionModule(ConversionModuleInterface $module)
    {
        $module->register($this->conversionCollection);

        // Allow for chaining
        return $this;
    }

    /**
     * Creates the new TypeConverter and provides it with the ConversionCollection
     * that we have built up.
     *
     * @return TypeConverter
     */
    public function build()
    {
        return new TypeConverter($this->conversionCollection);
    }
} 