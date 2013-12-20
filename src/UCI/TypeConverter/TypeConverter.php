<?php
/**
 * TypeConverter.php
 * 11/12/13
 *
 * @author: Jeremy Thacker <thackerj@uci.edu>
 */

namespace UCI\TypeConverter;

/**
 * Class TypeConverter
 *
 * Performs data type conversions.
 *
 *
 *
 * @package EEEApply\TypeConverter
 * @author Jeremy Thacker <thackerj@uci.edu>
 */
class TypeConverter
{
    /**
     * Stores known conversion methods
     *
     * @var ConversionCollection
     */
    protected $conversionCollection;

    /**
     * Accepts a ConversionCollection with required conversion methods
     *
     * @param ConversionCollection $conversion_collection
     */
    public function __construct(ConversionCollection $conversion_collection)
    {
        $this->conversionCollection = $conversion_collection;
    }

    /**
     * Accepts in instance of a class or primitive ($object) and returns
     * its equivalent in another data type ($to_type).
     *
     * @param mixed $object
     * @param string $to_type
     * @return mixed
     */
    public function convert($object, $to_type)
    {
        $from_type = $this->getType($object);
        $conversion_method = $this->conversionCollection->getConversion($from_type, $to_type);
        $result = $conversion_method($object);
        return $result;
    }

    /**
     * Returns the type of a variable.
     *
     * If the variable is a primitive, the primitive type is returned
     * (see gettype() for possible values).
     * If the variable is an object, the fully qualified class name is returned.
     *
     * @param mixed $object
     * @return string
     */
    private function getType($object)
    {
        $type = gettype($object);
        if($type == 'object') {
            $type = get_class($object);
        }
        return $type;
    }
} 