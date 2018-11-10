<?php

// DIC configuration. Remove this dependency on an API class. Use the API instead.
use AlAdhanApi\HijriGregorianCalendar;

$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new \Monolog\Handler\ErrorLogHandler());
    return $logger;
};

// Hijri Cal Service
$container['HijriCalendarService'] = function($c) {
    return new HijriGregorianCalendar();
};

$container['holyDay'] = function($c) {
    $cs = new HijriGregorianCalendar();
    return $cs->nextHijriHoliday(1)['data'];
};

$container['errorHandler'] = function ($c) {
    return function ($request, $response, $exception) use ($c) {
        return $c['response']->withStatus(500)
                            ->withHeader('Content-Type', 'text/html')
                            ->write('Sorry, we could not find the location or URL you are after. ');
    };
};

$container['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        return $c['response']
            ->withStatus(404)
            ->withHeader('Content-Type', 'text/html')
            ->write('Sorry, we could not find the URL you are after');
    };
};
