<?php

// DIC configuration. Remove this dependency on an API class. Use the API instead.
use AlAdhanApi\HijriGregorianCalendar;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Slim\Http\Request;
use Slim\Http\Response;
use Vesica\Slim\Middleware\Headers\Validate as HeaderValidationMiddleware;

$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Logger($settings['name']);
    $logger->pushHandler(new StreamHandler('php://stdout', $settings['name']));
    return $logger;
};

// Hijri Cal Service
$container['HijriCalendarService'] = function($c) {

    return new HijriGregorianCalendar();
};

$container['gToHAdjustment'] = function($c) {
    return 1;
};

$container['hToGAdjustment'] = function($c) {
    return -1;
};

$container['holyDay'] = function($c) {
    try {
        $cs = $c->HijriCalendarService;

        return $cs->nextHijriHoliday(0)['data'];
    } catch (Exception $e) {
        $c->logger->error('Unable to get Holy Day', ['code' => $e->getCode(), 'message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
    }
};

$container['errorHandler'] = function ($c) {
    return function (Request $request, Response $response, Exception $e) use ($c) {
        $c->logger->error('Slim Error Handler Triggered', ['code' => $e->getCode(), 'message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        return $c['response']->withStatus($e->getCode())
                            ->withHeader('Content-Type', 'text/html')
                            ->write($e->getMessage());
    };
};

$container['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        return $c['response']
            ->withStatus(404)
            ->withHeader('Content-Type', 'text/html')
            ->write('Sorry, we could not find the URL you are after.');
    };
};

/** Invoke Middleware for Load Balancer Checks */
$app->add(new HeaderValidationMiddleware(
        (bool) getenv('LOAD_BALANCER_MODE'),
        'X-LOAD-BALANCER',
        getenv('LOAD_BALANCER_KEY'),
        'Invalid Load Balancer Key.'
    )
);
