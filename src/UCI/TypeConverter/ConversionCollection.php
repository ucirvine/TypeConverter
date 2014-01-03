<?php
/**
 * ConversionCollection.php
 * 11/13/13
 *
 * @author: Jeremy Thacker <thackerj@uci.edu>
 */

namespace UCI\TypeConverter;


/**
 * Class ConversionCollection
 *
 * Accumulates an index of known conversion methods, so that they can
 * be called upon during application execution. Think of it as a
 * recipe book for converting between data types.
 *
 * @package EEEApply\TypeConverter
 * @author Jeremy Thacker <thackerj@uci.edu>
 */
class ConversionCollection
{
    /**
     * A two dimensional array for the storage of conversion methods.
     *
     * It takes the form of:
     *     $conversion_function = $conversions['from_type']['to_type']
     *
     * @var array
     */
    protected $conversions = [];

    /**
     * Adds a conversion to the ConversionCollection.
     *
     * A conversion is a function that converts from one data type to
     * another. This method accepts the name of the incoming type, the
     * outgoing type, and a callback for the conversion function.
     *
     * The from_type and to_type should be fully-qualified class names or
     * a primitive type that would be returned by gettype().
     *
     * The callback method should accept an instance of from_type as its only
     * parameter and return an instance of to_type.
     *
     * This method can be chained.
     *
     * @param string $from_type
     * @param string $to_type
     * @param callable $callback
     * @return $this
     */
    public function addConversion($from_type, $to_type, callable $callback)
    {
        // If we don't have any entries for this from_type yet, initialize
        // a child array to store associated to_types
        if(!isset($this->conversions[$from_type])) {
            $this->conversions[$from_type] = [];
        }
        $this->conversions[$from_type][$to_type] = $callback;

        // return $this for chaining
        return $this;
    }

    /**
     * Returns a conversion method callback that has already been added to
     * this collection.
     *
     * @param string $from_type
     * @param string $to_type
     * @return callable
     * @throws TypeConverterException If a callback cannot be found for the
     *                                from and to types provided.
     */
    public function getConversion($from_type, $to_type)
    {
        if(isset($this->conversions[$from_type][$to_type])) {
            return $this->conversions[$from_type][$to_type];
        }
        throw new TypeConverterException(
            "Conversion not found for converting '$from_type' to '$to_type'"
        );
    }
} 