# Slim Framework Lightncandy View

[![Build Status](https://travis-ci.org/endel/slim-lightncandy-view.svg?branch=master)](https://travis-ci.org/endel/slim-lightncandy-view)

This is a Slim Framework view helper built on top of the
[Lightncandy](https://github.com/zordius/lightncandy) templating component,
which is an extremely fast PHP implementation of
[handlebars](http://handlebarsjs.com/) and
[mustache](http://mustache.github.io/).

You can use this component to create and render templates in your Slim Framework application.

Requires Slim 3.x.x

## Install

Via [Composer](https://getcomposer.org/)

```bash
$ composer require endel/slim-lightncandy-view
```

## Usage

```php
// Create app
$app = new \Slim\App();

// Get container
$container = $app->getContainer();

// Register component on container
$container['view'] = function( $container ) {
	return new \Slim\Views\LightnCandy('path/to/templates/');
};

// Define named route
$app->get('/hello/{name}', function ($request, $response, $args) {
    return $this['view']->render('profile', [
        'name' => $args['name']
    ]);
})->setName('profile');

// Run app
$app->run();
```

## Examples

Take a look at the [example](example) directory for usage examples.

## Testing

```bash
phpunit
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.


## Credits

- [Endel Dreyer](https://github.com/endel)
- [Zordius](https://github.com/zordius)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
