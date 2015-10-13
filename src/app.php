<?php

use Silex\Application;
use Silex\Provider\HttpFragmentServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\WebProfilerServiceProvider;

// 3. party
use Neutron\Silex\Provider\MongoDBODMServiceProvider;

// dirs.
$app['root_dir']  = realpath(__DIR__.'/..');
$app['cache_dir'] = $app['root_dir'].'/var/cache';
$app['log_dir']   = $app['root_dir'].'/var/logs';

$app->register(new UrlGeneratorServiceProvider());
$app->register(new ValidatorServiceProvider());
$app->register(new ServiceControllerServiceProvider());
$app->register(new TwigServiceProvider(), [
    'twig.path'    => [__DIR__.'/../templates'],
    'twig.options' => $app['debug'] ? [] : ['cache' => __DIR__.'/../var/cache/twig'],
]);

$app['twig'] = $app->share($app->extend('twig', function (Twig_Environment $twig, $app) {
    // add custom globals, filters, tags, ...
    $twig->addGlobal('debug', $app['debug']);

    $filter = new Twig_SimpleFilter('roasterUrl', function(\Model\Roaster $roaster) use ($app) {
        if (false === $slug = $roaster->getSlug()) {
            return $app['url_generator']->generate('view', ['id' => $roaster->getId()]);
        }

        return $app['url_generator']->generate('roaster', [
            'countryCode' => strtolower($roaster->getAddress()->getCountryCode()),
            'slug'        => $roaster->getSlug(),
        ]);
    });

    $twig->addFilter($filter);

    $filter = new Twig_SimpleFilter('toUrl', function($url, $type) {
        switch ($type) {
            case 'facebook':
                return 'https://www.facebook.com/'.$url;
            case 'twitter':
                return 'https://twitter.com/'.$url;
            case 'instagram':
                return 'https://instagram.com/'.$url;
        }

        return $url;
    });

    $twig->addFilter($filter);

    $filter = new Twig_SimpleFilter('linkify', function($text, $type = 'generic', $context = []) {
        $text = preg_replace(
            '/(https?:\/\/\S+)/',
            '<a href="\1" target="_blank">\1</a>',
            $text
        );

        $tagReplaceUrl = '';
        $userReplaceUrl = '';

        if ('facebook' === $type) {
            $tagReplaceUrl = 'https://www.facebook.com/hashtag/\2?source=feed_text&story_id=:id:';
        }

        if ('instagram' === $type) {
            $tagReplaceUrl = 'https://instagram.com/explore/tags/\2';
            $userReplaceUrl = 'https://instagram.com/\2';
        }

        if ('twitter' === $type) {
            $tagReplaceUrl = 'https://twitter.com/search?q=%23\2';
            $userReplaceUrl = 'https://twitter.com/\2';
        }

        if ($tagReplaceUrl) {
            // linkify tags
            $text = preg_replace(
                '/(^|\s)#(\w+)/u',
                '\1<a href="'.$tagReplaceUrl.'" target="_blank">#\2</a>',
                $text
            );

            foreach ($context as $key => $value) {
                $context[':'.$key.':'] = $value;
                unset($context[$key]);
            }

            $text = strtr($text, $context);
        }

        if ($userReplaceUrl) {
            // linkify tags
            $text = preg_replace(
                '/(^|\s)@(\w+)/u',
                '\1<a href="'.$userReplaceUrl.'" target="_blank">@\2</a>',
                $text
            );
        }


        return $text;
    }, ['is_safe' => ['html']]);

    $twig->addFilter($filter);

    return $twig;
}));

$app->register(new HttpFragmentServiceProvider());

// MongoDB
$app->register(new MongoDBODMServiceProvider(), [
    'doctrine.odm.mongodb.connection_options' => [
        'database' => $app['r.mongodb.database'],
        'host'     => $app['r.mongodb.host'],
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

$app->register(new Silex\Provider\SwiftmailerServiceProvider(), [
    'swiftmailer.options' => [
        'host'       => $app['r.swiftmailer.host'],
        'port'       => $app['r.swiftmailer.port'],
        'username'   => $app['r.swiftmailer.username'],
        'password'   => $app['r.swiftmailer.password'],
        'encryption' => $app['r.swiftmailer.encryption'],
        'auth_mode'  => $app['r.swiftmailer.auth_mode'],
    ],
]);

if (false === $app['debug']) {
    // use cache service in prod mode.
    $app->register(new Silex\Provider\HttpCacheServiceProvider(), [
        'http_cache.cache_dir' => $app['cache_dir'].'/http',
        'http_cache.esi'       => null,
    ]);
} else {
    $app->register(new MonologServiceProvider(), array(
        'monolog.logfile' => __DIR__.'/../var/logs/silex_dev.log',
    ));

    $app->register(new WebProfilerServiceProvider(), array(
        'profiler.cache_dir' => __DIR__.'/../var/cache/profiler',
    ));
}

return $app;
