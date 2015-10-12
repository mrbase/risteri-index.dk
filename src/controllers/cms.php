<?php
/**
 * This file is part of the risteri-index.dk package.
 *
 * (c) Ulrik Nielsen <me@ulrik.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpFoundation\Response;

/** @var \Silex\Application $app */

/**
 * Google maps page
 */
$app->get('/map', function() use ($app) {
    $dm = $app['doctrine.odm.mongodb.dm'];

    /** @var \Doctrine\ODM\MongoDB\Query\Builder $qb */
    $rep = $dm->getRepository('Model\Roaster');


    $response = new Response($app['twig']->render('google-maps.html.twig', [
        'roasters' => $rep->findAll(),
    ]));
    $response->setSharedMaxAge(60*60*24);
    $response->setTtl(60*60*24);

    return $response;
})->bind('map');


/**
 * About us page
 */
$app->get('/om-os', function() use ($app) {
    $dm = $app['doctrine.odm.mongodb.dm'];

    /** @var \Doctrine\ODM\MongoDB\Query\Builder $qb */
    $rep = $dm->getRepository('Model\Roaster');

    $response = new Response($app['twig']->render('about.html.twig', [
        'roasters' => $rep->findAll(),
    ]));
    $response->setSharedMaxAge(60*60*24);
    $response->setTtl(60*60*24);

    return $response;
})->bind('about');
