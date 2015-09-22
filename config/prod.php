<?php

// configure your app for the production environment

$app['twig.path']    = [__DIR__.'/../templates'];
$app['twig.options'] = ['cache' => __DIR__.'/../var/cache/twig'];

$app['r.twitter.api_key']        = '';
$app['r.twitter.api_secret']     = '';
$app['r.instagram.api_key']      = '';
$app['r.instagram.api_secret']   = '';
$app['r.instagram.api_callback'] = '';
$app['r.instagram.api_token']    = '';
$app['r.facebook.app_id']        = '';
$app['r.facebook.app_secret']    = '';
$app['r.mongodb.host']           = '';
$app['r.mongodb.database']       = '';
