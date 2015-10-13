<?php
/**
 * This file is part of the risteri-index.dk package.
 *
 * (c) Ulrik Nielsen <me@ulrik.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/** @var \Silex\Application $app */

/**
 * Roaster view
 */
$app->get('/r/{id}', function($id) use ($app) {
    $roaster = $app['doctrine.odm.mongodb.dm']
        ->getRepository('Model\Roaster')
        ->find($id);

    if (!$roaster instanceof \Model\Roaster) {
        throw new NotFoundHttpException('Risteriet findes ikke.');
    }

    if (false !== $slug = $roaster->getSlug()) {
        return $app->redirect($app['url_generator']->generate('roaster', [
            'countryCode' => strtolower($roaster->getAddress()->getCountryCode()),
            'slug'        => $roaster->getSlug(),
        ]), 301);
    }

    $response = new Response($app['twig']->render('view.html.twig', [
        'roaster' => $roaster,
        'googlemaps_apikey' => $app['r.googlemaps.apikey'],
    ]));
    $response->setSharedMaxAge(60*10);
    $response->setTtl(60*10);

    return $response;
})->assert('id', '[a-z0-9]+')
    ->bind('view');


/**
 * Roaster view
 */
$app->get('/{countryCode}/{slug}', function($countryCode, $slug) use ($app) {
    $roaster = $app['doctrine.odm.mongodb.dm']
        ->getRepository('Model\Roaster')
        ->findOneBy([
            'address.countryCode' => strtoupper($countryCode),
            'slug'                => $slug,
        ]);

    if (!$roaster instanceof \Model\Roaster) {
        throw new NotFoundHttpException('Risteriet findes ikke.');
    }

    $response = new Response($app['twig']->render('view.html.twig', [
        'roaster'           => $roaster,
        'googlemaps_apikey' => $app['r.googlemaps.apikey'],
    ]));
    $response->setSharedMaxAge(60*10);
    $response->setTtl(60*10);

    return $response;
})->assert('countryCode', '[a-z]{2}')
    ->assert('slug', '[0-9a-z_-]+')
    ->bind('roaster');


/**
 * Ajax callback to get the roasters sorted by distance.
 */
$app->get('/geo-sort', function(Request $request) use ($app) {
    $dm = $app['doctrine.odm.mongodb.dm'];

    /** @var \Doctrine\ODM\MongoDB\Query\Builder $qb */
    $qb = $dm->createQueryBuilder('Model\Roaster');

    try {
        $roasters = $qb
            ->geoNear([
                (float) $request->query->get('lat'),
                (float) $request->query->get('lon'),
            ])
            ->spherical(true)
            ->distanceMultiplier(6378.137)

            ->getQuery()
            ->execute();
    } catch (\Exception $e) {
        error_log($e->getMessage());
        return new JsonResponse();
    }

    return $app['twig']->render('roaster-list.html.twig', [
        'roasters' => $roasters,
    ]);

})->bind('geofilter');
