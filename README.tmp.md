[![Build Status](https://travis-ci.org/endel/slim-lightncandy-view.svg?branch=master)](https://travis-ci.org/endel/slim-lightncandy-view)

This is a Slim Framework view helper built on top of the
[Lightncandy](https://github.com/zordius/lightncandy) templating component,
which is an extremely fast PHP implementation of
[handlebars](http://handlebarsjs.com/) and
[mustache](http://mustache.github.io/).

You can use this component to create and render templates in your Slim Framework application.

## Install

Via [Composer](https://getcomposer.org/)

```bash
$ composer require endel/slim-lightncandy-view
```

Requires PHP 5.3.0 or newer.

## Usage

```php
// Create Slim app
$app = new \Slim\App();

// Register Lightncandy View helper
$app->register(new \Slim\Views\Lightncandy('path/to/templates', [
    'cache' => 'path/to/cache'
]));

// Define named route
$app->get('/hello/{name}', function ($request, $response, $args) {
    $this['view']->render('profile.html', [
        'name' => $args['name']
    ]);
})->setName('profile');

// Run app
$app->run();
```

## Custom template functions

This component exposes a custom `url_for()` function to your Lightncandy templates. You can use this function to generate complete URLs to any Slim application named route. This is an example Lightncandy template:

    {% extends "layout.html" %}

    {% block body %}
    <h1>User List</h1>
    <ul>
        <li><a href="{{ url_for('profile', { 'name': 'josh' }) }}">Josh</a></li>
    </ul>
    {% endblock %}

## Testing

```bash
phpunit
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email security@slimframework.com instead of using the issue tracker.

## Credits

- [Josh Lockhart](https://github.com/codeguy)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

