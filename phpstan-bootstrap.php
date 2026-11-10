<?php

use Symfony\Component\Dotenv\Dotenv;

require_once __DIR__.'/vendor/autoload.php';

if (class_exists(Dotenv::class)) {
    (new Dotenv())->usePutenv()->bootEnv(__DIR__.'/.env');
}
