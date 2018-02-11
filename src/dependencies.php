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
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

// Hijri Cal Service
$container['HijriCalendarService'] = function($c) {
    return new HijriGregorianCalendar();
};

$container['holyDay'] = function($c) {
    $cs = new HijriGregorianCalendar();
    return $cs->nextHijriHoliday()['data'];
};
