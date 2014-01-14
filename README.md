TypeConverter
=============

TypeConverter is a configurable tool for converting between data types. It allows
you to define a set of conversion algorithms up front and easily apply them
throughout your application by simply providing the object to be converted and
its target type. TypeConverter includes a Silex service provider for integration
with Silex and Symfony projects.

Installation
------------

The easiest way to get TypeConverter is to install it with [Composer](http://getcomposer.org).
You will need to add both the project and its repository to your composer.json file.

    {
        "require": {
            "uci/type-converter": "dev-master"
        },
        "repositories": [
            {
                "type": "vcs",
                "url": "git@github.com:ucirvine/TypeConverter.git"
            }
        ]
    }

TypeConverter is stored in a private GitHub repository. To install it,
you will need access to the ucirvine/TypeConverter repository. When installing
via composer, you can either interactively provide your GitHub credentials when
prompted, or use the -n flag to use your public key if you have set it up
with GitHub.

To install and authenticate interactively call:

    php composer.phar install

To install and authenticate via public key call:

    php composer.phar install -n

Usage
-----

###Tutorial

####Set Up

TypeConverter requires explicitly defined one-way conversions methods to convert
data types from one to another. These methods should be established during the
initialization phase of your app so that they're ready when the application
executes.

For the rest of this tutorial, let's suppose that we have two classes,
Fahrenheit and Celsius.

```PHP
namespace Temperature;

class Fahrenheit
{
    public $temperature;
}

class Celsius
{
    public $temperature;
}
```

####Defining Conversions

We can set up a TypeConverter that will allow us to convert between Fahrenheit
and Celsius by creating a ConversionModule that knows how to translate between
those types.

A ConversionModule implements ConversionModuleInterface and contains
"conversion methods." Conversion methods are methods or functions that
accept an argument of one type and return another type, performing a one-way
translation. A ConversionModule must implement the `register` method,
which passes its conversion methods to a ConversionCollection.

```PHP
namespace Temperature;

use UCI\TypeConverter\ConversionModuleInterface;
use UCI\TypeConverter\ConversionCollection;

class TemperatureConversionModule implements ConversionModuleInterface
{
    public function register(ConversionCollection $conversion_collection) {
        $conversion_collection
            ->addConversion(
                'Temperature\Fahrenheit',
                'Temperature\Celsius',
                [$this, 'fahrenheitToCelsius']
            )
            ->addConversion(
                'Temperature\Celsius',
                'Temperature\Fahrenheit',
                [$this, 'celsiusToFahrenheit']
            );
    }

    public function fahrenheitToCelsius(Fahrenheit $f)
    {
        $c = new Celsius();
        $c->temperature = ($f->temperature - 32) * (5/9);
        return $c;
    }

    public function celsiusToFahrenheit(Celsius $c)
    {
        $f = new Fahrenheit();
        $f->temperature = ($c->temperature * (9/5)) + 32;
        return $f;
    }
}
```

In this case, TemperatureConversionModule defines two conversion methods: one
to convert from Fahrenheit to Celsius and one to convert from Celsius to
Fahrenheit. In the `register` method, `ConversionCollection::addConversion` is
called for each conversion method, providing the "from type", the "to type" and
the callback.

[Learn more about adding conversion methods](#registering-conversion-methods)

####Initialization

The easiest way to integrate TypeConverter is with Silex, which we will use here.
Simply register the TypeConverterServiceProvider with your application.

```PHP
$app->register(new UCI\TypeConverter\TypeConverterServiceProvider());
```

After registration, you can add any ConversionModules that you may need to the
TypeConversionBuilder. This should be done before `$app['type_converter.convert']`
is called upon in your application's execution.

```PHP
// Add a couple of ConversionModules to the builder using chaining
// The second module has a dependency on ThingyFactory
$app['type_converter.builder']
    ->addConversionModule(new Temperature\TemperatureConversionModule())
    ->addConversionModule(new ThingyConversionModule($app['thingy_factory']));
```

####Converting Things

Once everything is set up, converting things is easy.

```PHP
$converter = $app['type_converter.converter'];

$f = new Fahrenheit();
$f->temperature = 72;
$c = $converter->convert($f, 'Temperature\Celsius');
echo $c->temperature; // prints 22.2222
```

###More Details

####Registering Conversion Methods

```PHP
ConversionCollection::addConversion($from_type, $to_type, $callback)
```

The `$from_type` and `$to_type` should each either be a *fully qualified* class name,
or the name of a primitive as it would be returned by `gettype()`.

`$callback` can be any valid callable type. In the tutorial above, we provided
callable arrays, but there are other options. Here are a few examples.

```PHP
// Call a method of this ConversionModule
$conversion_collection->addConversion('FromType', 'ToType', [$this, 'conversionMethod']);

// Call a static class method
$conversion_collection->addConversion('FromType', 'ToType', 'SomeClass::conversionMethod');

// Call an anonymous function
$conversion_func = function(FromType $from) { return new ToType(); };
$conversion_collection->addConversion('FromType', 'ToType', $conversion_func);

// Call an inline anonymous function
$conversion_collection->addConversion(
    'FromType',
    'ToType',
    function(FromType $from) { return new ToType(); }
);
```

While any of the above variations will work just fine, using the first option and
adding methods as object method calls on a ConversionModule has a few advantages:

1.  It keeps related conversion methods bundled together in one ConversionModule
    class.
2.  It allows your conversion methods to utilize dependencies. For example, your
    ConversionModule can require a factory class or helper class at construction
    that its methods can then utilize.
3.  It makes unit testing a snap because you can just instantiate the
    ConversionModule class and test its conversion methods directly.

####Recursive Conversions

There may be cases when dealing with large, compound objects when it will be useful
to call TypeConverter from within a conversion method. For example, suppose
you had the weather forecast objects AmericanForecast and EuropeanForecast.

```PHP
class AmericanForecast {
    /**
     * @var 'Temperature\Fahrenheit'
     */
    public $temperature;
}

class EuropeanForecast {
    /**
     * @var 'Temperature\Celsius'
     */
    public $temperature;
}
```

We've already defined conversion methods for Fahrenheit and Celsius, so it would
be nice if we could use those while converting between AmericanForecast and
EuropeanForecast. Fortunately, we can.

The TypeConverter itself is passed into each conversion method on invocation
as an optional second parameter. You can use the TypeConverter to apply conversions
recursively.

```PHP
function americanForecastToEuropeanForecast(
    AmericanForecast $af,
    TypeConverter $type_converter
) {
    $ef = new EuropeanForecast();
    $ef->temperature =
        $type_converter->convert($af->temperature, 'Temperature\Celsius');
}
```
