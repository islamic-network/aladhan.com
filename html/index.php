<?php

if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/../vendor/autoload.php';

session_start();

// Instantiate the app
$container = new \DI\Container();
\Slim\Factory\AppFactory::setContainer($container);
$app = \Slim\Factory\AppFactory::create();
$app->addRoutingMiddleware();
$container = $app->getContainer();


// Set up dependencies
require __DIR__ . '/../src/dependencies.php';

// Register middleware
require __DIR__ . '/../src/middleware.php';

/** Load routes **/
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(realpath(__DIR__) . '/../routes'));
$routes = array_keys(array_filter(iterator_to_array($iterator), function($file) {
    return $file->isFile();
}));
foreach ($routes as $route) {
    if (strpos($route, '.php') !== false) {
        require_once(realpath($route));
    }
}

// Run app
$app->run();
