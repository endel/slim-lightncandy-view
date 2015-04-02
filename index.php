<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require('./vendor/autoload.php');

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

