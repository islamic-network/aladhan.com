<?php
// Application middleware
use Psr7Middlewares\Middleware;

$https = Middleware::Https(true)
        ->maxAge(1000000);
