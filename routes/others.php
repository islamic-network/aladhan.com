<?php
// Routes
$app->get('/liveness', function ($request, $response) {
    $response->getBody()->write(json_encode('OK'));
    $response->withStatus(200);

    return $response;
});

$app->get(
    '/', function ($request, $response, $args) {
    $args['title'] = 'Home';
    $args['view'] = 'home';
    $args['holydayFloater'] = $this->get('holyDay');
    $args['noticeFloater'] = $this->get('noticeFloater');

    return $this->get('renderer')->render($response, 'index.phtml', $args);
}
);

$app->get(
    '/about', function ($request, $response, $args) {
    $args['title'] = 'About';
    $args['view'] = 'about';
    $args['holydayFloater'] = $this->get('holyDay');
    $args['noticeFloater'] = $this->get('noticeFloater');

    return $this->get('renderer')->render($response, 'about.phtml', $args);
}
);

$app->get(
    '/calculation-methods', function ($request, $response, $args) {
    $args['title'] = 'Prayer Time Calculation Methods';
    $args['view'] = '';
    $args['holydayFloater'] = $this->get('holyDay');
    $args['noticeFloater'] = $this->get('noticeFloater');

    return $this->get('renderer')->render($response, 'calculation-methods.phtml', $args);
}
);

$app->get(
    '/contact', function ($request, $response, $args) {
    $args['title'] = 'Contact';
    $args['view'] = 'contact';
    $args['holydayFloater'] = $this->get('holyDay');
    $args['noticeFloater'] = $this->get('noticeFloater');

    return $this->get('renderer')->render($response, 'contact.phtml', $args);
}
);

$app->get(
    '/play/', function ($request, $response, $args) {
    return $response->withStatus(301)->withHeader('Location', '/play');
}
);

$app->get(
    '/download-adhans', function ($request, $response, $args) {
    $args['title'] = 'Download Adhans';
    $args['view'] = 'api';
    $args['holydayFloater'] = $this->get('holyDay');
    $args['noticeFloater'] = $this->get('noticeFloater');

    return $this->get('renderer')->render($response, 'download-adhans.phtml', $args);
}
);

$app->get(
    '/consumers-api', function ($request, $response, $args) {
    $args['title'] = 'Apps and Websites using the AlAdhan API';
    $args['view'] = 'api';
    $args['holydayFloater'] = $this->get('holyDay');
    $args['holydayFloater'] = $this->get('holyDay');
    $args['noticeFloater'] = $this->get('noticeFloater');

    return $this->get('renderer')->render($response, 'consumers-api.phtml', $args);
}
);

$app->get(
    '/clients-api', function ($request, $response, $args) {
    $args['title'] = 'API Clients';
    $args['view'] = 'api';
    $args['holydayFloater'] = $this->get('holyDay');
    $args['noticeFloater'] = $this->get('noticeFloater');

    return $this->get('renderer')->render($response, 'clients-api.phtml', $args);
}
);

$app->get(
    '/play', function ($request, $response, $args) {

    // $this->logger->info("aladhan.com '/' play");
    $args['title'] = 'Adhan Player and Prayer Times Today';
    $args['city'] = '';
    $args['country'] = '';
    $args['view'] = 'play';
    $args['holydayFloater'] = $this->get('holyDay');
    $args['noticeFloater'] = $this->get('noticeFloater');

    return $this->get('renderer')->render($response, 'play.phtml', $args);
}
);

$app->get(
    '/play/{city}/{country}', function ($request, $response, $args) {
    $city = $request->getAttribute('city');
    $country = $request->getAttribute('country');
    $adjustment = $this->get('gToHAdjustment');
    $times = [];
    if ($city != null && $country != null) {
        // ISNA is default method. Adjustment of +1 day added for Dhul Hijjah 2018.
        $t = new \AlAdhanApi\TimesByCity($city, $country, null, null, '', $adjustment);
        $times = $t->get()['data'];
    }

    $args['title'] = 'Adhan Player and Prayer Times Today | ' . $city . ' ' . $country;
    $args['city'] = $city;
    $args['country'] = $country;
    $args['view'] = 'play';
    $args['holydayFloater'] = $this->get('holyDay');
    $args['noticeFloater'] = $this->get('noticeFloater');
    $args['times'] = $times;

    return $this->get('renderer')->render($response, 'play.phtml', $args);

}
);

$app->get(
    '/credits-and-terms', function ($request, $response, $args) {
    // $this->logger->info("aladhan.com '/' credits-and-terms");
    $args['title'] = 'Credits, Terms and Conditions';
    $args['view'] = 'terms';
    $args['holydayFloater'] = $this->get('holyDay');
    $args['noticeFloater'] = $this->get('noticeFloater');

    return $this->get('renderer')->render($response, 'terms.phtml', $args);
}
);

$app->post(
    '/download/{format}', function (\Slim\Psr7\Request $request, $response, $args) {
    $format = $request->getAttribute('format');
    $data = $request->getParsedBody()['data'];
    $timings = json_decode($data);
    if (is_object($timings)) {
        $ts = [];
        foreach ($timings as $month => $tx) {
            foreach ($tx as $ty) {
                $ts[] = (array)$ty;
            }
        }
        $timings = $ts;
    }
    // An unhealthy hack as flatterner has not been updated for PHP 8
    error_reporting(E_ALL ^ E_DEPRECATED);
    if ($format === 'csv' && is_array($timings)) {
        $flattener = new \NestedJsonFlattener\Flattener\Flattener();
        $flattener->setArrayData($timings);
        $file = '/tmp/csv' . rand();
        $flattener->writeCsv($file);

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file) . '.csv"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        readfile($file . '.csv');
        unlink($file . '.csv');
    }
}
);
