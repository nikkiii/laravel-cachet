Laravel Cachet
====================

Laravel Cachet is a Cachet API wrapper for Laravel 5. It utilises Graham Campbell's [Laravel Manager](https://github.com/GrahamCampbell/Laravel-Manager) package.


## Installation

[PHP](https://php.net) 5.5+ or [HHVM](http://hhvm.com) 3.6+, and [Composer](https://getcomposer.org) are required.

To get the latest version of Laravel Cachet, simply add the following line to the require block of your `composer.json` file:

```
"nikkiii/laravel-cachet": "~1.0.1"
```

Once Laravel Cachet is installed, you need to register the service provider. Open up `config/app.php` and add the following to the `providers` key.

* `Nikkiii\Cachet\CachetServiceProvider::class`

You can register the DigitalOcean facade in the `aliases` key of your `config/app.php` file if you like.

* `'Cachet' => Nikkiii\Cachet\Facades\Cachet::class`


## Configuration

Laravel Cachet requires connection configuration.

To get started, you'll need to publish all vendor assets:

```bash
$ php artisan vendor:publish
```

This will create a `config/cachet.php` file in your app that you can modify to set your configuration. Also, make sure you check for changes to the original config file in this package between releases.

There are two config options:

##### Default Connection Name

This option (`'default'`) is where you may specify which of the connections below you wish to use as your default connection for all work. Of course, you may use many connections at once using the manager class. The default value for this setting is `'main'`.

##### Cachet Connections

This option (`'connections'`) is where each of the connections are setup for your application. Example configuration has been included, but you may add as many connections as you would like.


## Usage

##### CachetManager

This is the class of most interest. It is bound to the ioc container as `'cachet'` and can be accessed using the `Facades\Cachet` facade. This class implements the `ManagerInterface` by extending `AbstractManager`. The interface and abstract class are both part of Graham Campbell's [Laravel Manager](https://github.com/GrahamCampbell/Laravel-Manager) package, so you may want to go and checkout the docs for how to use the manager class over at [that repo](https://github.com/GrahamCampbell/Laravel-Manager#usage). Note that the connection class returned will always be an instance of `\Nikkiii\Cachet\CachetConnection`.

##### Facades\Cachet

This facade will dynamically pass static method calls to the `'cachet'` object in the ioc container which by default is the `CachetManager` class.

##### CachetServiceProvider

This class contains no public methods of interest. This class should be added to the providers array in `config/app.php`. This class will setup ioc bindings.

##### Real Examples

Here you can see an example of just how simple this package is to use. Out of the box, the default adapter is `main`. After you enter your authentication details in the config file, it will just work:

```php
use Nikkiii\Cachet\Facades\DigitalOcean;
// you can alias this in config/app.php if you like

// all calls will return either an array if it's a list, or stdClass object if it's data.
// however, ping simply returns a boolean.

// this'll return a list of components registered in cachet!
Cachet::components();

// this'll return the component data for component 1
Cachet::component(1);

// this'll return a list of incidents
Cachet::incidents();
```

The cachet manager will behave like it is a `\Nikkiii\Cachet\CachetConnection` class. If you want to call specific connections, you can do with the `connection` method:

```php
use Nikkiii\Cachet\Facades\DigitalOcean;

// the alternative connection is the other example provided in the default config
Cachet::connection('alternative')->components();
```

With that in mind, note that:

```php
use Nikkiii\Cachet\Facades\DigitalOcean;

// writing this:
Cachet::connection('main')->components();

// is identical to writing this:
Cachet::components();

// and is also identical to writing this:
Cachet::connection()->components();

// this is because the main connection is configured to be the default
Cachet::getDefaultConnection(); // this will return main

// we can change the default connection
Cachet::setDefaultConnection('alternative'); // the default is now alternative
```

If you prefer to use dependency injection over facades, then you can easily inject the manager like so:

```php
use Nikkiii\Cachet\CachetManager;
use Illuminate\Support\Facades\App; // you probably have this aliased already

class Foo {
    protected $cachet;

    public function __construct(CachetManager $cachet) {
        $this->cachet = $cachet;
    }

    public function bar() {
        $this->cachet->components();
    }
}

App::make('Foo')->bar();
```

For more information on how to the manager class check out https://github.com/GrahamCampbell/Laravel-Manager#usage.

##### Further Information

This doesn't support the Metrics API currently, however it will. This was done very quickly and may be a bit messy.

This library may move to Laravel's Collections in the future to better support array operations, as it often returns arrays of data.

## License

Laravel Cachet is licensed under [The ISC License (ISC)](LICENSE).
