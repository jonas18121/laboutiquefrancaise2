<?php

use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

$dotenv = new Dotenv();
// dd(dirname(__DIR__).'/.env');
$dotenv->usePutenv(true)->bootEnv(dirname(__DIR__).'/.env');

// (new Dotenv())->usePutenv(true)->bootEnv(dirname(__DIR__).'/.env');

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
