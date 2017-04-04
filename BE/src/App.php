<?php
/** Application class with initial data**/
use Silex\Application;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\HttpFragmentServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\SwiftmailerServiceProvider;
use JDesrosiers\Silex\Provider\CorsServiceProvider;


$app = new Silex\Application();

$app->register(new Silex\Provider\RoutingServiceProvider());
$app->register(new ServiceControllerServiceProvider());
$app->register(new HttpFragmentServiceProvider());
//$app->register(new TranslationServiceProvider());
//////////////////
/////////////////
//include('dbcnf.php');                           ///to add instead Silex\Provider\DoctrineServiceProvider() input array
$app->register(new Silex\Provider\DoctrineServiceProvider(), /*$inp*/ array(
    'dbs.options' => array (
        'adminUser' => array(
            'driver'    => 'pdo_mysql',
            'host'      => 'localhost',
            'dbname'    => 'rcadmin',
            'user'      => 'root',
            'password'  => null,
            'charset'   => 'utf8mb4',
        ),
        'anyUser' => array(
            'driver'    => 'pdo_mysql',
            'host'      => 'localhost',
            'dbname'    => 'rcadmin',
            'user'      => 'root',
            'password'  => null,
            'charset'   => 'utf8mb4',
        ),
    ),
));
//////////////////
$app->register(new Silex\Provider\HttpCacheServiceProvider(), array(
    'http_cache.cache_dir' => __DIR__.'/cache/',
));
///////////////////
$app->register(new Silex\Provider\SessionServiceProvider());
//////////////////
$app->register(new JDesrosiers\Silex\Provider\CorsServiceProvider(), [
    "cors.allowOrigin" => "*",
])
;

//////////////////
//$app->register(new Silex\Provider\TranslationServiceProvider(), array(
//    'translator.domains' => array(),
//));
return $app;
