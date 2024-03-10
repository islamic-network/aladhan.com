<?php

// DIC configuration. Remove this dependency on an API class. Use the API instead.
use AlAdhanApi\HijriGregorianCalendar;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Slim\Http\Request;
use Slim\Http\Response;
use Psr\Log\LogLevel;

// view renderer
$container->set('renderer', function ($c) {
    return new Slim\Views\PhpRenderer(__DIR__ . '/../templates/');
});

// monolog
$container->set('logger', function ($c) {
    $logger = new Logger('AlAdhanApp');
    $logger->pushHandler(new StreamHandler('php://stdout', LogLevel::INFO));
    return $logger;
});

// Hijri Cal Service
$container->set('HijriCalendarService', function ($c) {
    return new HijriGregorianCalendar();
});

$container->set('gToHAdjustment', function ($c) {
    return 0;
});

$container->set('hToGAdjustment', function ($c) {
    return 0;
});

$container->set('holyDay', function ($c) {
    try {
        $cs = $c->get('HijriCalendarService');

        return $cs->nextHijriHoliday($c->get('gToHAdjustment'))['data'];
    } catch (Exception $e) {
        $logger = $c->get('logger');
        $logger->error('Unable to get Holy Day', ['code' => $e->getCode(), 'message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
    }
});

/**
 * Any text returned here appears at the top of all the pages for any announcements / notices
 * @param $c
 * @return string
 */
$container->set('noticeFloater', function ($c) {
    return 'Saudi Arabia announces Monday, March 11, 2024 as the 1st of Ramadan. Other countries, March 12. <a href="https://community.islamic.network/d/85-ramadan-2024" title="Ramadan 2024 Announcement" target="_blank>Read More</a>."';
});


