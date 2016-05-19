<?php

use Symfony\Component\HttpFoundation\Response;

/** @var \Silex\Application $app */

/**
 * Front page
 */
$app->get('/', function () use ($app) {
    $dm = $app['doctrine.odm.mongodb.dm'];

    /** @var \Doctrine\ODM\MongoDB\Query\Builder $qb */
    $qb = $dm->createQueryBuilder('Model\Roaster');

    $roasters = $qb->sort('name', 1)
        ->getQuery()
        ->execute();

    $tags = [];
    foreach ($roasters as $shop) {
        $tag = $shop->getTags();
        if ($tag) {
            $tag = $tag[0];
        }
        $tags[$tag] = ucfirst($tag);
    }

    $response = new Response($app['twig']->render('index.html.twig', [
        'roasters'          => $roasters,
        'tags'              => $tags,
        'recaptcha_sitekey' => $app['r.recaptcha.sitekey'],
        'googlemaps_apikey' => $app['r.googlemaps.apikey'],
    ]));

    // cache for 24 hours
    $response->setSharedMaxAge(60*60*24);
    $response->setTtl(60*60*24);

    return $response;
})->bind('home');

require __DIR__.'/controllers/cms.php';
require __DIR__.'/controllers/feedback.php';
require __DIR__.'/controllers/partials.php';
require __DIR__.'/controllers/roasters.php';

/**
 * Error handling
 */
$app->error(function (\Exception $e, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    // 404.html, or 40x.html, or 4xx.html, or error.html
    $templates = [
        'errors/'.$code.'.html',
        'errors/'.substr($code, 0, 2).'x.html',
        'errors/'.substr($code, 0, 1).'xx.html',
        'errors/default.html',
    ];

    return new Response($app['twig']->resolveTemplate($templates)->render(['code' => $code]), $code);
});
