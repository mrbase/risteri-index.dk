<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

//Request::setTrustedProxies(array('127.0.0.1'));

$app->get('/', function () use ($app) {
    $shops = [
        [
            'name' => 'Great Coffee',
            'url' => 'http://www.greatcoffee.dk/',
            'address' => 'Klostergade 32, Aarhus C',
        ],
        [
            'name' => 'KONTRA Coffee',
            'url' => 'http://www.kontracoffee.com/',
            'address' => 'Dag Hammarskjölds Allé 36 2100 København Ø',
        ],
        [
            'name' => 'Kaffe Kaffe',
            'url' => 'http://kaffekaffe.dk/',
            'address' => 'St. Sct. Mikkelsgade 10G 8800 Viborg',
        ],
        [
            'name' => 'Delux Kaffe',
            'url' => 'http://www.deluxkaffekompagniet.dk/',
            'address' => 'Nordskovvej 1 3700 Rønne',
        ],
        [
            'name' => 'Mols Kafferisteri',
            'url' => 'http://molskafferisteri.dk/',
            'address' => 'Lyngevej 58 8420 Knebel',
        ],
        [
            'name' => 'Risteriet',
            'url' => 'http://www.risteriet.dk/',
            'address' => ' Tørringvej 17 2610 Rødovre ',
        ],
        [
            'name' => 'Holy Bean',
            'url' => 'http://holybean.dk/',
            'address' => 'Cederfeldsgade 18A, 5560 Aarup',
        ],
        [
            'name' => 'Just Coffee',
            'url' => 'http://justcoffee.dk/',
            'address' => 'Frederiksborgvej 551 4000 Roskilde',
        ],
        [
            'name' => 'Baris&Co',
            'url' => 'http://barisco.dk/',
            'address' => 'Melchiors Plads 3 2100 København Ø',
        ],
        [
            'name' => 'Det lille Risteri',
            'url' => 'http://detlilleristeri.dk/',
            'address' => 'Refshalevej 320 1432 København K',
        ],
        [
            'name' => 'The Coffee Collective',
            'url' => 'http://coffeecollective.dk/',
            'address' => 'Godthåbsvej 34B 2000 Frederiksberg',
        ],
        [
            'name' => 'Øristeriet',
            'url' => 'http://www.øristeriet.dk/',
            'address' => 'Valhøjs Allé 190D DK-2610 Rødovre',
        ],
        [
            'name' => 'Strandvejsristeriet',
            'url' => 'http://www.strandvejsristeriet.dk/',
            'address' => 'Kronborg Slot, Bygn 12A 3000 Helsingør',
        ],
        [
            'name' => 'Kama Group',
            'url' => 'http://www.kamagroup.dk/',
            'address' => 'Kannikegade 9, 8500 Grenaa',
        ],
        [
            'name' => 'chulumenda',
            'url' => 'http://www.chulumenda.dk/',
            'address' => 'Bjergagervej 35 8300 Odder',
        ],
        [
            'name' => 'Kaffe & Co',
            'url' => 'http://www.kaffeogco.dk/',
            'address' => 'Bredskiftevej 18 8210 Århus V',
        ],
        [
            'name' => 'Espressos',
            'url' => 'http://www.espressos.dk/',
            'address' => 'Skudehavnsvej 2b 2150 Nordhavn',
        ],
        [
            'name' => 'Davids Coffee',
            'url' => 'http://www.davidscoffee.com/',
            'address' => 'Rugvænget 21A DK-2630 Taastrup',
        ],
        [
            'name' => 'KaffeKaa',
            'url' => 'http://kaffekaa.dk/',
            'address' => 'Jels Nygade 4, 6630 Jels',
        ],
        [
            'name' => 'Kaffevaerk',
            'url' => 'http://kaffevaerk.dk/',
            'address' => 'Smallegade 22 2000 Frederiksberg',
        ],
        [
            'name' => 'Hipster brew',
            'url' => 'http://hipsterbrew.com/',
            'address' => 'Jernbanegade 44, 1 6000 Kolding',
        ],
        [
            'name' => 'Farm Mountain Coffee',
            'url' => 'http://farmmountain.dk/',
            'address' => 'Mysundevej 10, 8930',
        ],
        [
            'name' => 'KaffeMekka',
            'url' => 'http://www.kaffemekka.dk/',
            'address' => 'Beringvej 21A 8361 Hasselager',
        ],
        [
            'name' => 'Nørre Snede Kafferisteri',
            'url' => 'http://www.noerresnedekafferisteri.dk/',
            'address' => 'Strøget 8, 8766 Nørre Snede',
        ],
        [
            'name' => 'emofabrik',
            'url' => 'http://emofabrik.dk/',
            'address' => 'Søren Frichsvej 22 DK-8000 Århus C ',
        ],
        [
            'name' => "Buchwald's Kafferisteri",
            'url' => 'http://buchwalds.dk/',
            'address' => 'Skansevej 8 A 3700 Rønne',
        ],
        [
            'name' => 'kaffeagenterne',
            'url' => 'http://kaffeagenterne.dk/',
            'address' => 'Mysundevej 10, DK-8930 Randers NØ',
        ],
        [
            'name' => 'Zozozial coffee',
            'url' => 'http://zozozialcoffee.dk/',
            'address' => 'Vestergade 24 5700 Svendborg',
        ],
    ];

    usort($shops, function($a, $b) {
        return strcasecmp($a['name'], $b['name']);
    });

    return $app['twig']->render('index.html', [
        'shops' => $shops
    ]);
})->bind('homepage');

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
