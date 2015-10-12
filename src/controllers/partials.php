<?php
/**
 * This file is part of the risteri-index.dk package.
 *
 * (c) Ulrik Nielsen <me@ulrik.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Facebook\Facebook;
use MetzWeb\Instagram\Instagram;
use Symfony\Component\HttpFoundation\Response;
use TwitterOAuth\Auth\SingleUserAuth;
use TwitterOAuth\Serializer\ArraySerializer;

/** @var \Silex\Application $app */

/**
 * Facebook embedded controller
 */
$app->get('/partial/facebook/{token}', function($token) use ($app) {
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

    return $app['twig']->render('partials/facebook.html.twig', ['feed' => $response->getDecodedBody()['data']]);
})->bind('facebook-partial');


/**
 * Instagram embedded controller
 */
$app->get('/partial/instagram/{token}', function($token) use ($app) {
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
            'large_image'  => $media->images->standard_resolution,
        ];
    }

    return $app['twig']->render('partials/instagram.html.twig', ['feed' => $images]);
})->bind('instagram-partial');


/**
 * Twitter embedded controller
 */
$app->get('/partial/twitter/{token}', function($token) use ($app) {
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

    return $app['twig']->render('partials/twitter.html.twig', ['feed' => $tweets]);
})->bind('twitter-partial');

