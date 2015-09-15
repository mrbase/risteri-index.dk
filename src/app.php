<?php

use Silex\Application;
use Silex\Provider\HttpFragmentServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;

// 3. party
use Neutron\Silex\Provider\MongoDBODMServiceProvider;

$app = new Application();

// dirs.
$app['root_dir']  = realpath(__DIR__.'/..');
$app['cache_dir'] = $app['root_dir'].'/var/cache';
$app['log_dir']   = $app['root_dir'].'/var/logs';

$app->register(new UrlGeneratorServiceProvider());
$app->register(new ValidatorServiceProvider());
$app->register(new ServiceControllerServiceProvider());
$app->register(new TwigServiceProvider());
$app['twig'] = $app->share($app->extend('twig', function ($twig, $app) {
    // add custom globals, filters, tags, ...

    return $twig;
}));

$app->register(new HttpFragmentServiceProvider());

// MongoDB
$app->register(new MongoDBODMServiceProvider(), [
    'doctrine.odm.mongodb.connection_options' => [
        'database' => 'roasters',
        'host'     => 'mongodb://localhost:27017',
        'options'  => ['fsync' => false]
    ],
    'doctrine.odm.mongodb.documents' => [
        0 => [
            'type'      => 'annotation',
            'path'      => [
                $app['root_dir'].'/src/Model',
            ],
            'namespace' => 'Model',
            'alias'     => 'Model',
        ]
    ],
    'doctrine.odm.mongodb.proxies_dir'             => $app['cache_dir'].'/doctrine/odm/mongodb/Proxy',
    'doctrine.odm.mongodb.proxies_namespace'       => 'DoctrineMongoDBProxy',
    'doctrine.odm.mongodb.auto_generate_proxies'   => true,
    'doctrine.odm.mongodb.hydrators_dir'           => $app['cache_dir'].'/doctrine/odm/mongodb/Hydrator',
    'doctrine.odm.mongodb.hydrators_namespace'     => 'DoctrineMongoDBHydrator',
    'doctrine.odm.mongodb.auto_generate_hydrators' => true,
    'doctrine.odm.mongodb.metadata_cache'          => new \Doctrine\Common\Cache\ArrayCache(),
    'doctrine.odm.mongodb.logger_callable'         => $app->protect(function($query) use ($app) {
        if (empty($app['monolog'])) {
            return;
        }

        $app['monolog']->debug('MongoDB query.', $query);
    }),
]);


return $app;
