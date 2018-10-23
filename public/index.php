<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/autoload.php';
require __DIR__ . '/../app/AppKernel.php';

//Set the environment and debugging mode manually, if they are not installed in the environment or need to be redefined
//putenv(sprintf('%s=%s', SYMFONY_ENV, 'dev'));
//putenv(sprintf('%s=%s', SYMFONY_DEBUG, '1'));
$environment = SYMFONY_ENV_DEV === getenv(SYMFONY_ENV) ? SYMFONY_ENV_DEV : SYMFONY_ENV_PROD;
$isDev = SYMFONY_ENV_DEV === $environment;
$debug = $isDev ? true : false;

if ($isDev) {
    Debug::enable();
}

$kernel = new AppKernel($environment, $debug);

if (PHP_VERSION_ID < 70000) {
    $kernel->loadClassCache();
}

//When using the HttpCache, you need to call the method in your front controller instead of relying on the configuration parameter
//$kernel = new AppCache($kernel);
//Request::enableHttpMethodParameterOverride();

$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
