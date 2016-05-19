<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

// 3. party
use Doctrine\ODM\MongoDB\Tools\Console\Helper\DocumentManagerHelper;
use GuzzleHttp\Client as GuzzleClient;
use Geocoder\Provider\GoogleMaps;
use Ivory\HttpAdapter\Guzzle6HttpAdapter;

$console = new Application('Roaser CLI', '0/1');
$console->getDefinition()->addOption(new InputOption('--env', '-e', InputOption::VALUE_REQUIRED, 'The Environment name.', 'dev'));
$console->setDispatcher($app['dispatcher']);

$console
    ->register('nff')
    ->setDescription('Create new roaster from json file.')
    ->setDefinition([
        new InputArgument('file', InputArgument::REQUIRED, 'File path')
    ])
    ->setCode(function(InputInterface $input, OutputInterface $output) use ($app) {
        $data = file_get_contents($input->getArgument('file'));
        $data = json_decode($data, true);

        $guzzle   = new GuzzleClient(['verify' => true]);
        $geocoder = new GoogleMaps(new Guzzle6HttpAdapter($guzzle));
        //$geocoder = new GoogleMaps(new Guzzle6HttpAdapter($guzzle), null, null, true, 'AIzaSyDU1YKd8OwCpJYWaD_LyUd7UYefFzn9Sjg');

        /** @var \Doctrine\ODM\MongoDB\DocumentManager $dm */
        $dm = $app['doctrine.odm.mongodb.dm'];
        $repo = $dm->getRepository('Model\Roaster');

        // /** @var \Doctrine\ODM\MongoDB\Query\Builder $qb */
        // $qb = $dm->createQueryBuilder('Model\Roaster');
        //
        // $count = $qb->field('url')->equals($data['url'])
        //     ->hydrate(false)
        //     ->getQuery()
        //     ->count();
        //
        // if ($count) {
        //     $output->writeln('Roaster already in DB!');
        //     exit;
        // }

        $r = new Model\Roaster();
        $r->setName($data['name']);
        $r->setUrl($data['url']);

        $address = $data['address'].', '.$data['zip'].' '.$data['city'];
        $geo = $geocoder->geocode($address.', denmark');
        $geo = $geo->first();

        $a = new Model\Address();
        $a->setAddressLine1($geo->getStreetName().' '.$geo->getStreetNumber());
        $a->setPostalCode($geo->getPostalCode());
        $a->setLocality($geo->getLocality());
        $a->setCountryCode($geo->getCountryCode());
        $a->setLocale('da');

        $c = new Model\Coordinates();
        $c->setLat($geo->getLatitude());
        $c->setLon($geo->getLongitude());

        $a->setCoordinates($c);
        $r->setAddress($a);

        if (isset($data['cvr'])) {
            $r->setRegistrationNumber($data['cvr']);

            if (isset($data['startdate'])) {
                $time = DateTime::createFromFormat('m/d - Y', $data['startdate'], new DateTimeZone('Europe/Copenhagen'));
                $r->setEstablishedAt($time);
            }
        }

        if (isset($data['facebook'])) {
            $r->addFeed('facebook', $data['facebook']);
        }
        if (isset($data['twitter'])) {
            $r->addFeed('twitter', $data['twitter']);
        }
        if (isset($data['instagram'])) {
            $r->addFeed('instagram', $data['instagram']);
        }
        if (isset($data['blog'])) {
            $r->addFeed('blog', $data['blog']);
        }

//print_r($r);exit;
        $dm->persist($r);
        $dm->flush();

        $output->writeln('"'.$data['name'].'" now created.');
    })
;

$console
    ->register('my-command')
    ->setDefinition(array(
        // new InputOption('some-option', null, InputOption::VALUE_NONE, 'Some help'),
    ))
    ->setDescription('My command description')
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
        // do something

        /** @var \Doctrine\ODM\MongoDB\DocumentManager $dm */
        $dm = $app['doctrine.odm.mongodb.dm'];
        $repo = $dm->getRepository('Model\Roaster');

        /** @var \Model\Roaster $roaster */

        // Copenhagen Roaster
        $roaster = $repo->find('55f3d116dd3fdf57eb0041a7');
        $roaster->setUrl('http://chokocom.com/kaffe-2');
        $dm->persist($roaster);


        $dm->flush();

return;


        $guzzle   = new GuzzleClient(['verify' => true]);
        $geocoder = new GoogleMaps(new Guzzle6HttpAdapter($guzzle), null, null, true, 'AIzaSyDU1YKd8OwCpJYWaD_LyUd7UYefFzn9Sjg');

        $dm = $app['doctrine.odm.mongodb.dm'];

        /** @var \Doctrine\ODM\MongoDB\Query\Builder $qb */
        $qb = $dm->createQueryBuilder('Model\Roaster');

        foreach ($shops as $shop) {
            $count = $qb->field('url')->equals($shop['url'])
                ->hydrate(false)
                ->getQuery()
                ->count();

            if ($count) {
//                continue;
            }

            $r = new Model\Roaster();
            $r->setName($shop['name']);
            $r->setUrl($shop['url']);
            if (isset($shop['cvr'])) {
                $r->setRegistrationNumber($shop['cvr']);

                try {
                    $response = $guzzle->get('http://cvrapi.dk/api?vat='.$shop['cvr'].'&country=dk');

                    $data = json_decode($response->getBody()->getContents(), true);
                    $time = DateTime::createFromFormat('m/d - Y', $data['startdate'], new DateTimeZone('Europe/Copenhagen'));

                    $r->setEstablishedAt($time);

                    if (!is_null($data['enddate'])) {
                        $time = DateTime::createFromFormat('m/d - Y', $data['enddate'], new DateTimeZone('Europe/Copenhagen'));
                        $r->setInvalidatedAt($time);
                    }
                } catch (\Exception $e) {
                    $output->writeln('cvrapi error: '.$shop['cvr']);
                }
            }

#            try {
                $geo = $geocoder->geocode($shop['address'].', denmark');
                $geo = $geo->first();
#            } catch (\Exception $e) {
#                $output->writeln('out of lookups, bailing... try again tomorrow');
#                break;
#            }
print_r($geo);exit;
            $a = new Model\Address();
            $a->setAddressLine1($geo->getStreetName().' '.$geo->getStreetNumber());
            $a->setPostalCode($geo->getPostalCode());
            $a->setLocality($geo->getLocality());
            $a->setCountryCode($geo->getCountryCode());
            $a->setLocale('da');

            $c = new Model\Coordinates();
            $c->setLat($geo->getLatitude());
            $c->setLon($geo->getLongitude());

            $a->setCoordinates($c);
            $r->setAddress($a);

            if (isset($shop['thanks'])) {
                foreach (explode(',', $shop['thanks']) as $key) {
                    $key = trim($key);

                    $c = new Model\Credit();
                    $c->setName($thanks[$key]['name']);
                    $c->setSource($thanks[$key]['url']);
                    $r->addCredit($c);
                }
            }
            $r->setTags($shop['tags']);

            if (isset($shop['facebook'])) {
                $r->addFeed('facebook', $shop['facebook']);
            }
            if (isset($shop['twitter'])) {
                $r->addFeed('twitter', $shop['twitter']);
            }
            if (isset($shop['instagram'])) {
                $r->addFeed('instagram', $shop['instagram']);
            }
            if (isset($shop['blog'])) {
                $r->addFeed('blog', $shop['blog']);
            }

            $dm->persist($r);
        }

        $dm->flush();
    })
;

// MongoDB Stuff..
$dm = new DocumentManagerHelper($app['doctrine.odm.mongodb.dm']);
$console->getHelperSet()->set($dm, 'dm');

// Add Doctrine ODM commands
$console->addCommands(array(
    new Doctrine\ODM\MongoDB\Tools\Console\Command\GenerateDocumentsCommand(),
    new Doctrine\ODM\MongoDB\Tools\Console\Command\GenerateHydratorsCommand(),
    new Doctrine\ODM\MongoDB\Tools\Console\Command\GenerateProxiesCommand(),
    new Doctrine\ODM\MongoDB\Tools\Console\Command\GenerateRepositoriesCommand(),
    new Doctrine\ODM\MongoDB\Tools\Console\Command\QueryCommand(),
    new Doctrine\ODM\MongoDB\Tools\Console\Command\ClearCache\MetadataCommand(),

    new Doctrine\ODM\MongoDB\Tools\Console\Command\Schema\CreateCommand(),
    new Doctrine\ODM\MongoDB\Tools\Console\Command\Schema\DropCommand(),
    new Doctrine\ODM\MongoDB\Tools\Console\Command\Schema\UpdateCommand(),
));

return $console;
