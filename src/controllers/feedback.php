<?php
/**
 * This file is part of the risteri-index.dk package.
 *
 * (c) Ulrik Nielsen <me@ulrik.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/** @var \Silex\Application $app */

$app->post('/feedback', function(Request $request) use ($app) {
    $guzzle = new Client();
    $response = $guzzle->post('https://www.google.com/recaptcha/api/siteverify', ['form_params' => [
        'secret'   => $app['r.recaptcha.secret'],
        'response' => $request->request->get('g-recaptcha-response'),
        'remoteip' => $request->getClientIp()
    ]]);

    $status = true;

    $response = json_decode($response->getBody()->getContents(), true);
    if (true === $response['success']) {
        $body = $request->request->get('email')." har sendt en besked:\n\n".$request->request->get('message')."\n\n-- \nmvh robottos";

        $message = \Swift_Message::newInstance()
            ->setSubject('[dk roasters] feedback')
            ->setFrom($app['r.feedback.mail.from'])
            ->setTo($app['r.feedback.mail.to'])
            ->setBody($body);

        try {
            $app['mailer']->send($message);
        } catch (\Swift_TransportException $e) {
            error_log($e->getTraceAsString());
            $status = false;
        }
    }

    return new JsonResponse(['status' => $status]);
});
