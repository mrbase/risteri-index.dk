<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

//Request::setTrustedProxies(array('127.0.0.1'));

/** @var \Silex\Application $app */
$app->get('/', function (Request $request) use ($app) {
    $dm = $app['doctrine.odm.mongodb.dm'];

    /** @var \Doctrine\ODM\MongoDB\Query\Builder $qb */
    $qb = $dm->createQueryBuilder('Model\Roaster');

    $roasters = $qb->sort('name', 1)
        ->getQuery()
        ->execute();

    $tags = [];
    foreach ($roasters as $shop) {
        $tag = $shop->getTags()[0];
        $tags[$tag] = ucfirst($tag);
    }

    return $app['twig']->render('index.html.twig', [
        'roasters' => $roasters,
        'tags'  => $tags
    ]);
})->bind('home');

$app->get('/r/{id}', function($id) use ($app) {
    $roaster = $app['doctrine.odm.mongodb.dm']
        ->getRepository('Model\Roaster')
        ->find($id);

    if (!$roaster instanceof \Model\Roaster) {
        throw new NotFoundHttpException('Risteriet findes ikke.');
    }

    return $app['twig']->render('view.html.twig', ['roaster' => $roaster]);
})->bind('view');


$app->get('/p/facebook/{token}', function($token) use ($app) {
    $feed = new \Facebook\Facebook([
        'app_id'                  => '1614698512145872',
        'app_secret'              => '082bfc447b9c31ded9afc36c6da81136',
        'http_client_handler'     => 'curl',
        'persistent_data_handler' => 'memory',
        'default_access_token'    => '1614698512145872|082bfc447b9c31ded9afc36c6da81136',
    ]);

    try {
        $response = $feed->get('/'.$token.'/posts?limit=10&return_ssl_resources=1&fields=picture,message,id,created_time');
    } catch (\Exception $e) {
        return new Response();
    }

    return $app['twig']->render('partials/facebook.html.twig', ['feed' => $response->getDecodedBody()['data']]);
})->bind('facebook-partial');

$app->get('/p/instagram/{token}', function($token) use ($app) {
    return new Response();
})->bind('instagram-partial');

$app->get('/p/twitter/{token}', function($token) use ($app) {
    return new Response();
})->bind('twitter-partial');

$app->get('/p/blog/{token}', function($token) use ($app) {
    return new Response();
})->bind('blog-partial');


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


$app->get('/map', function() use ($app) {
    $dm = $app['doctrine.odm.mongodb.dm'];

    /** @var \Doctrine\ODM\MongoDB\Query\Builder $qb */
    $rep = $dm->getRepository('Model\Roaster');

    return $app['twig']->render('google-maps.html.twig', [
        'roasters' => $rep->findAll(),
    ]);
})->bind('map');

$app->get('/om-os', function() use ($app) {
    return $app['twig']->render('about.html.twig');
})->bind('about');


$app->error(function (\Exception $e, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    // 404.html, or 40x.html, or 4xx.html, or error.html
    $templates = array(
        'errors/'.$code.'.html',
        'errors/'.substr($code, 0, 2).'x.html',
        'errors/'.substr($code, 0, 1).'xx.html',
        'errors/default.html',
    );

    return new Response($app['twig']->resolveTemplate($templates)->render(array('code' => $code)), $code);
});
