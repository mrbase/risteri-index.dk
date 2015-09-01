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
            'added' => '2015-08-26 12:00:00',
            'thanks' => 'Michael',
            'tags' => ['jylland'],
        ],
        [
            'name' => 'KONTRA Coffee',
            'url' => 'http://www.kontracoffee.com/',
            'address' => 'Dag Hammarskjölds Allé 36, 2100 København Ø',
            'added' => '2015-08-26 12:00:00',
            'tags' => ['sjælland'],
        ],
        [
            'name' => 'Kaffe Kaffe',
            'url' => 'http://kaffekaffe.dk/',
            'address' => 'St. Sct. Mikkelsgade 10G, 8800 Viborg',
            'added' => '2015-08-26 12:00:00',
            'tags' => ['jylland'],
        ],
        [
            'name' => 'Delux Kaffe',
            'url' => 'http://www.deluxkaffekompagniet.dk/',
            'address' => 'Nordskovvej 1, 3700 Rønne',
            'added' => '2015-08-26 12:00:00',
            'tags' => ['bornholm'],
        ],
        [
            'name' => 'Mols Kafferisteri',
            'url' => 'http://molskafferisteri.dk/',
            'address' => 'Lyngevej 58, 8420 Knebel',
            'added' => '2015-08-26 12:00:00',
            'tags' => ['jylland'],
        ],
        [
            'name' => 'Risteriet',
            'url' => 'http://www.risteriet.dk/',
            'address' => ' Tørringvej 17, 2610 Rødovre ',
            'added' => '2015-08-26 12:00:00',
            'tags' => ['sjælland'],
        ],
        [
            'name' => 'Holy Bean',
            'url' => 'http://holybean.dk/',
            'address' => 'Cederfeldsgade 18A, 5560 Aarup',
            'added' => '2015-08-26 12:00:00',
            'tags' => ['fyn'],
        ],
        [
            'name' => 'Just Coffee',
            'url' => 'http://justcoffee.dk/',
            'address' => 'Frederiksborgvej 551, 4000 Roskilde',
            'added' => '2015-08-26 12:00:00',
            'tags' => ['sjælland'],
        ],
        [
            'name' => 'Baris&Co',
            'url' => 'http://barisco.dk/',
            'address' => 'Melchiors Plads 3, 2100 København Ø',
            'added' => '2015-08-26 12:00:00',
            'tags' => ['sjælland'],
        ],
        [
            'name' => 'Det lille Risteri',
            'url' => 'http://detlilleristeri.dk/',
            'address' => 'Refshalevej 320, 1432 København K',
            'added' => '2015-08-26 12:00:00',
            'tags' => ['sjælland'],
        ],
        [
            'name' => 'The Coffee Collective',
            'url' => 'http://coffeecollective.dk/',
            'address' => 'Godthåbsvej 34B, 2000 Frederiksberg',
            'added' => '2015-08-26 12:00:00',
            'tags' => ['sjælland'],
        ],
        [
            'name' => 'Øristeriet',
            'url' => 'http://www.øristeriet.dk/',
            'address' => 'Valhøjs Allé 190D, DK-2610 Rødovre',
            'added' => '2015-08-26 12:00:00',
            'tags' => ['sjælland'],
        ],
        [
            'name' => 'Strandvejsristeriet',
            'url' => 'http://www.strandvejsristeriet.dk/',
            'address' => 'Kronborg Slot, Bygn 12A, 3000 Helsingør',
            'added' => '2015-08-26 12:00:00',
            'tags' => ['sjælland'],
        ],
        [
            'name' => 'Kama Group',
            'url' => 'http://www.kamagroup.dk/',
            'address' => 'Kannikegade 9, 8500 Grenaa',
            'added' => '2015-08-26 12:00:00',
            'tags' => ['jylland'],
        ],
        [
            'name' => 'chulumenda',
            'url' => 'http://www.chulumenda.dk/',
            'address' => 'Bjergagervej 35, 8300 Odder',
            'added' => '2015-08-26 12:00:00',
            'tags' => ['jylland'],
        ],
        [
            'name' => 'Kaffe & Co',
            'url' => 'http://www.kaffeogco.dk/',
            'address' => 'Bredskiftevej 18, 8210 Århus V',
            'added' => '2015-08-26 12:00:00',
            'tags' => ['jylland'],
        ],
        [
            'name' => 'Espressos',
            'url' => 'http://www.espressos.dk/',
            'address' => 'Skudehavnsvej 2b, 2150 Nordhavn',
            'added' => '2015-08-26 12:00:00',
            'tags' => ['sjælland'],
        ],
        [
            'name' => 'Davids Coffee',
            'url' => 'http://www.davidscoffee.com/',
            'address' => 'Rugvænget 21A, DK-2630 Taastrup',
            'added' => '2015-08-26 12:00:00',
            'tags' => ['sjælland'],
        ],
        [
            'name' => 'KaffeKaa',
            'url' => 'http://kaffekaa.dk/',
            'address' => 'Jels Nygade 4, 6630 Jels',
            'added' => '2015-08-26 12:00:00',
            'tags' => ['jylland'],
        ],
        [
            'name' => 'Kaffevaerk',
            'url' => 'http://kaffevaerk.dk/',
            'address' => 'Smallegade 22, 2000 Frederiksberg',
            'added' => '2015-08-26 12:00:00',
            'tags' => ['sjælland'],
        ],
        [
            'name' => 'Hipster brew',
            'url' => 'http://hipsterbrew.com/',
            'address' => 'Jernbanegade 44, 1 6000 Kolding',
            'added' => '2015-08-26 12:00:00',
            'tags' => ['jylland'],
        ],
        [
            'name' => 'Farm Mountain Coffee',
            'url' => 'http://farmmountain.dk/',
            'address' => 'Mysundevej 10, 8930 Randers',
            'added' => '2015-08-26 12:00:00',
            'tags' => ['jylland'],
        ],
        [
            'name' => 'KaffeMekka',
            'url' => 'http://www.kaffemekka.dk/',
            'address' => 'Beringvej 21A, 8361 Hasselager',
            'added' => '2015-08-26 12:00:00',
            'tags' => ['jylland'],
        ],
        [
            'name' => 'Nørre Snede Kafferisteri',
            'url' => 'http://www.noerresnedekafferisteri.dk/',
            'address' => 'Strøget 8, 8766 Nørre Snede',
            'thanks' => 'Logan, Marie, Per',
            'added' => '2015-08-27 12:00:00',
            'tags' => ['jylland'],
        ],
        [
            'name' => 'emofabrik',
            'url' => 'http://emofabrik.dk/',
            'address' => 'Søren Frichsvej 22, DK-8000 Århus C',
            'thanks' => 'Logan',
            'added' => '2015-08-27 12:00:00',
            'tags' => ['jylland'],
        ],
        [
            'name' => "Buchwald's Kafferisteri",
            'url' => 'http://buchwalds.dk/',
            'address' => 'Skansevej 8A, 3700 Rønne',
            'thanks' => 'Logan',
            'added' => '2015-08-27 12:00:00',
            'tags' => ['bornholm'],
        ],
        [
            'name' => 'kaffeagenterne',
            'url' => 'http://kaffeagenterne.dk/',
            'address' => 'Mysundevej 10, DK-8930 Randers NØ',
            'thanks' => 'Logan',
            'added' => '2015-08-27 12:00:00',
            'tags' => ['jylland'],
        ],
        [
            'name' => 'Zozozial coffee',
            'url' => 'http://zozozialcoffee.dk/',
            'address' => 'Vestergade 24, 5700 Svendborg',
            'added' => '2015-08-27 12:00:00',
            'tags' => ['fyn'],
        ],
        [
            'name' => 'Soze kaffe & thebar',
            'url' => 'http://www.sozekaffe.dk/',
            'address' => 'Havnegade 18, 5600 Faaborg',
            'thanks' => 'Rita',
            'added' => '2015-08-27 12:00:00',
            'tags' => ['fyn'],
        ],
        [
            'name' => 'Stellini Kaffe',
            'url' => 'http://www.stellini.dk/',
            'address' => 'Cikorievej 80, 5220 Odense SØ',
            'thanks' => 'Jonny',
            'added' => '2015-08-28 12:00:00',
            'tags' => ['fyn'],
        ],
        [
            'name' => 'KaffeLars',
            'url' => 'http://www.kaffelars.dk/',
            'address' => 'Brylle Industrivej 5, 5690 Tommerup',
            'thanks' => 'Jonny',
            'added' => '2015-08-28 12:00:00',
            'tags' => ['fyn'],
        ],
        [
            'name' => 'Ækvatorkaffe',
            'url' => 'http://www.aekvatorkaffe.dk/',
            'address' => 'Skovhusvej 4, 8762 Flemming',
            'thanks' => 'Jonny',
            'added' => '2015-08-28 12:00:00',
            'tags' => ['jylland'],
        ],
        [
            'name' => 'La Cabra',
            'url' => 'http://www.lacabra.dk/',
            'address' => 'Graven 20, 8000 Aarhus C',
            'thanks' => 'Jonny',
            'added' => '2015-08-28 12:00:00',
            'tags' => ['jylland'],
        ],
        [
            'name' => 'Kaffehuset Møn',
            'url' => 'http://www.kaffehusetmoen.dk/',
            'address' => 'Storegade 48, 4780 Stege',
            'thanks' => 'Jonny',
            'added' => '2015-08-28 12:00:00',
            'tags' => ['jylland'],
        ],
        [
            'name' => 'Altura Kaffe',
            'url' => 'http://www.altura.dk/',
            'address' => 'Graven 22, 8000 Aarhus C',
            'thanks' => 'Jonny',
            'added' => '2015-08-28 12:00:00',
            'tags' => ['jylland'],
        ],
        [
            'name' => 'Riccos',
            'url' => 'http://riccos.dk/',
            'address' => 'Frederiksborgade 50, 1360 København K',
            'thanks' => 'Jonny',
            'added' => '2015-08-28 12:00:00',
            'tags' => ['sjælland'],
        ],
        [
            'name' => 'The Factory',
            'url' => 'http://www.thefactorycoffee.eu/',
            'address' => 'Refshalevej 163A, st., 1432 København K',
            'added' => '2015-09-01 11:30:00',
            'tags' => ['sjælland'],
        ],
        [
            'name' => 'Kent Kaffe Laboratorium',
            'url' => 'http://www.kentkaffelaboratorium.com/',
            'address' => 'Nørre Farimagsgade 70, 1364 København K',
            'added' => '2015-09.01 11:30:00',
            'tags' => ['sjælland'],
        ],
        [
            'name' => 'Ren Kaffe',
            'url' => 'http://renkaffe.dk/',
            'address' => 'Willemoesgade 82st. th., 8200 Aarhus N',
            'added' => '2015-09-01 11:30:00',
            'tags' => ['jylland'],
        ],
        [
            'name' => 'Amokka',
            'url' => 'http://www.amokka.dk/',
            'address' => 'Vibe Alle 3, 2980 Kokkedal',
            'added' => '2015-09-01 11:30:00',
            'tags' => ['sjælland'],
        ],
        [
            'name' => 'Black Juice',
            'url' => 'http://blackjuice.dk/',
            'address' => 'Sankt Mortens Vej 3, 4000 Roskilde',
            'added' => '2015-09-01 11:30:00',
            'tags' => ['sjælland'],
        ],
        [
            'name' => 'Nygaards Kaffe',
            'url' => 'http://www.nygaardskaffe.dk/',
            'address' => 'Jernbanegade 2D, 9900 Frederikshavn',
            'added' => '2015-09-01 11:30:00',
            'tags' => ['jylland'],
        ],
        [
            'name' => 'Kaffeselskabet',
            'url' => 'http://www.kaffeselskabet.dk/',
            'address' => 'Tostrupvej 142, 9240 Nibe',
            'added' => '2015-09-01 11:30:00',
            'tags' => ['jylland'],
        ],
        [
            'name' => 'Vendia Kaffe',
            'url' => 'http://www.vendiakaffe.dk/',
            'address' => 'Skagensvej 201, 9800 Hjørring',
            'added' => '2015-09-01 11:30:00',
            'tags' => ['jylland'],
        ],
        [
            'name' => 'KaffeClausen',
            'url' => 'http://www.kaffeclausen.dk/',
            'address' => 'Strandbyvej 10, 8240 Risskov',
            'added' => '2015-09-01 11:30:00',
            'tags' => ['jylland'],
        ],
        [
            'name' => 'Q-kaffe',
            'url' => 'http://q-kaffe.dk/',
            'address' => 'Blåbærvej 40, 5260 Odense S',
            'added' => '2015-09-01 11:30:00',
            'tags' => ['fyn'],
        ],
        [
            'name' => 'Kafferisteriet',
            'url' => 'http://kafferisteriet.dk/',
            'address' => 'Korsgade 17, 5700 Svendborg',
            'added' => '2015-09-01 11:30:00',
            'tags' => ['fyn'],
        ],
        [
            'name' => 'Nielsens Kafferisteri',
            'url' => 'http://www.nielsenskafferisteri.dk/',
            'address' => 'Krondrevet 3, 4760 Vordingborg',
            'added' => '2015-09-01 11:30:00',
            'tags' => ['sjælland'],
        ],
        [
            'name' => 'Den Bæredygtige Bønne',
            'url' => 'http://www.ugandakaffe.dk/',
            'address' => 'Ønskevej 14, 3360 Liseleje',
            'added' => '2015-09-01 11:30:00',
            'tags' => ['sjælland'],
        ],
#        [
#            'name' => '',
#            'url' => '',
#            'address' => '',
#            'thanks' => '',
#            'added' => '2015-08-28 12:00:00',
#        ],
    ];

    usort($shops, function($a, $b) {
        return strcasecmp($a['name'], $b['name']);
    });

    $tags = [];
    foreach ($shops as $shop) {
        $tag = $shop['tags'][0]; 
        $tags[$tag] = ucfirst($tag);
    }

    return $app['twig']->render('index.html', [
        'shops' => $shops,
        'tags'  => $tags
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
