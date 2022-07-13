<?php
// Application middleware
use Psr7Middlewares\Middleware;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;

$errorMiddleware = $app->addErrorMiddleware(false, true, true);

$errorMiddleware->setErrorHandler(
    Slim\Exception\HttpNotFoundException::class,
    function (ServerRequestInterface $request, Throwable $exception, bool $displayErrorDetails) use ($container) {
        $response = new Response();
        $response->getBody()->write('Sorry, we could not find the URL you are after.');
        return $response->withStatus(404);
    });

$errorMiddleware->setErrorHandler(
    HttpMethodNotAllowedException::class,
    function (ServerRequestInterface $request, Throwable $exception, bool $displayErrorDetails) use ($container) {
        $logger = $containter->get('logger)');
        $logger->error('Slim Error Handler Triggered', ['code' => $exception->getCode(), 'message' => $exception->getMessage(), 'trace' => $exception->getTraceAsString()]);
        $response = new Response();
        $response->getBody()->write($exception->getMessage());
        return $response->withStatus($exception->getCode());
    });
