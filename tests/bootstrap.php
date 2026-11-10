<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

// Força o ambiente de teste ANTES de carregar o .env
$_SERVER['APP_ENV'] = 'test';
$_ENV['APP_ENV'] = 'test';
putenv('APP_ENV=test');

// Carrega o .env (sem verificação desnecessária de method_exists)
(new Dotenv())->bootEnv(dirname(__DIR__).'/.env');

// Garante que APP_ENV continue como test após o bootEnv
$_SERVER['APP_ENV'] = 'test';
$_ENV['APP_ENV'] = 'test';

if ($_SERVER['APP_DEBUG']) {
    umask(0000);
}
