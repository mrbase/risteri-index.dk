<?php

use Facebook\Facebook;
use MetzWeb\Instagram\Instagram;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use TwitterOAuth\Auth\SingleUserAuth;
use TwitterOAuth\Serializer\ArraySerializer;

//Request::setTrustedProxies(array('127.0.0.1'));

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
        $tag = $shop->getTags()[0];
        $tags[$tag] = ucfirst($tag);
    }

    return $app['twig']->render('index.html.twig', [
        'roasters' => $roasters,
        'tags'  => $tags
    ]);
})->bind('home');


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

    return $app['twig']->render('view.html.twig', ['roaster' => $roaster]);
})->bind('view');


/**
 * Facebook embedded controller
 */
$app->get('/p/facebook/{token}', function($token) use ($app) {
    $feed = new Facebook([
        'app_id'                  => $app['r.facebook.app_id'],
        'app_secret'              => $app['r.facebook.app_secret'],
        'http_client_handler'     => 'curl',
        'persistent_data_handler' => 'memory',
        'default_access_token'    => $app['r.facebook.app_id'].'|'.$app['r.facebook.app_secret'],
    ]);

    try {
        $response = $feed->get('/'.$token.'/posts?limit=10&return_ssl_resources=1&fields=picture,message,id,created_time');
    } catch (\Exception $e) {
        return new Response();
    }

    $response = new Response($app['twig']->render('partials/facebook.html.twig', ['feed' => $response->getDecodedBody()['data']]));
    $response->setSharedMaxAge(60*10);
    $response->setTtl(60*10);

    return $response;
})->bind('facebook-partial');


/**
 * Instagram embedded controller
 */
$app->get('/p/instagram/{token}', function($token) use ($app) {
    $instagram = new Instagram([
        'apiKey'      => $app['r.instagram.api_key'],
        'apiSecret'   => $app['r.instagram.api_secret'],
        'apiCallback' => $app['r.instagram.api_callback'],
    ]);

    $instagram->setAccessToken($app['r.instagram.api_token']);
    $user = $instagram->searchUser($token, 1);

    $images = [];
    foreach ($instagram->getUserMedia($user->data[0]->id, 10)->data as $media) {
        $images[] = [
            'caption'      => isset($media->caption, $media->caption->text) ? $media->caption->text : '',
            'created_time' => $media->created_time,
            'link'         => $media->link,
            'image'        => $media->images->thumbnail,
        ];
    }

    $response = new Response($app['twig']->render('partials/instagram.html.twig', ['feed' => $images]));
    $response->setSharedMaxAge(60*10);
    $response->setTtl(60*10);

    return $response;
})->bind('instagram-partial');


/**
 * Twitter embedded controller
 */
$app->get('/p/twitter/{token}', function($token) use ($app) {
    $credentials = [
        'consumer_key'    => $app['r.twitter.api_key'],
        'consumer_secret' => $app['r.twitter.api_secret'],
    ];

    $auth     = new SingleUserAuth($credentials, new ArraySerializer());
    $response = $auth->get('statuses/user_timeline', [
        'count'           => 10,
        'exclude_replies' => true,
        'screen_name'     => $token,
    ]);

    $tweets = [];
    foreach ($response as $tweet) {
        $tweets[] = [
            'created_time' => strtotime($tweet['created_at']),
            'link' => 'https://twitter.com/'.$tweet['user']['screen_name'].'/status/'.$tweet['id'],
            'text' => $tweet['text'],
        ];
    }

    $response = new Response($app['twig']->render('partials/twitter.html.twig', ['feed' => $tweets]));
    $response->setSharedMaxAge(60*10);
    $response->setTtl(60*10);

    return $response;
})->bind('twitter-partial');


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


/**
 * Google maps page
 */
$app->get('/map', function() use ($app) {
    $dm = $app['doctrine.odm.mongodb.dm'];

    /** @var \Doctrine\ODM\MongoDB\Query\Builder $qb */
    $rep = $dm->getRepository('Model\Roaster');

    return $app['twig']->render('google-maps.html.twig', [
        'roasters' => $rep->findAll(),
    ]);
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
    $response->setSharedMaxAge(60*10);
    $response->setTtl(60*10);

    return $response;
})->bind('about');


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
