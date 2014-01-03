<?php
/**
 * TypeConverterServiceProvider.php
 * 11/13/13
 *
 * @author: Jeremy Thacker <thackerj@uci.edu>
 */

namespace UCI\TypeConverter;

use UCI\TypeConverter\TypeConverterBuilder;
use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * Class TypeConverterServiceProvider
 *
 * Provides the TypeConverter service to the application.
 *
 * A TypeConverter can be used to translate objects from one type to another.
 * Translations are implemented as methods in ConversionModules. Different parts
 * of the application can register ConversionModules with TypeConverter that
 * perform required conversions, then call on TypeConverter to actually perform
 * the conversions when necessary.
 *
 * ConversionModules are created by implementing the ConversionModuleInterface.
 * (See the interface for further documentation.) ConversionModules can be added
 * to the TypeConverterBuilder during application initialization by accessing
 * $app['type_converter_builder'].
 *
 * Conversions can be performed at run time by accessing the TypeConverter
 * at $app['type_converter'].
 *
 * Example:
 *
 *   Initialization:
 *
 *     $app['type_converter_builder']
 *        ->addConversionModule(new MyConversionModule())
 *        ->addConversionModule(new AnotherConversionModule($dependency));
 *
 *   Use:
 *
 *     $typeConverter = $app['type_converter'];
 *
 *     $a = new ClassA();
 *     $b = $typeConverter->convert($a, 'ClassB');
 *
 *
 * @see UCI\TypeConverter\ConversionModuleInterface
 *
 * @package UCI\TypeConverter
 * @author Jeremy Thacker <thackerj@uci.edu>
 */
class TypeConverterServiceProvider implements ServiceProviderInterface
{
    /**
     * Provides a shared TypeConverterBuilder (as type_converter.builder) to the
     * rest of the application for adding ConversionModules.
     *
     * Provides a shared TypeConverter (as type_converter.converter) to the rest of
     * the application for.
     *
     * @param Application $app
     */
    public function register(Application $app)
    {
        $app['type_converter.builder'] = $app->share(function ($c) {
            return TypeConverterBuilder::create();
        });

        $app['type_converter.converter'] = $app->share(function ($c) {
            return $c['type_converter.builder']->build();
        });
    }

    public function boot(Application $app)
    {
        // No need to do anything at boot
    }
} 