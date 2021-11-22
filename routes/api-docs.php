<?php
$app->get(
    '/asma-al-husna-api', function ($request, $response, $args) {

    // $this->logger->info("aladhan.com '/' asma-al-husna-api");
    $args['apidocs'] = json_decode(file_get_contents('../html/apidocs/asmaAlHusna/api_data.json'));
    $args['title'] = 'Asma Al Husna  API';
    $args['view'] = 'api';
    $args['holydayFloater'] = $this->get('holyDay');
    $args['noticeFloater'] = $this->get('noticeFloater');

    return $this->get('renderer')->render($response, 'asma-al-husna-api.phtml', $args);
}
);

$app->get(
    '/prayer-times-api', function ($request, $response, $args) {

    // $this->logger->info("aladhan.com '/' prayer-times-api");
    $args['apidocs'] = json_decode(file_get_contents('../html/apidocs/timings/api_data.json'));
    $args['title'] = 'Prayer Times API';
    $args['view'] = 'api';
    $args['holydayFloater'] = $this->get('holyDay');
    $args['noticeFloater'] = $this->get('noticeFloater');

    return $this->get('renderer')->render($response, 'prayer-times-api.phtml', $args);
}
);

$app->get(
    '/rest-api', function ($request, $response, $args) {
    return $response->withStatus(301)->withHeader('Location', '/prayer-times-api');
}
);

$app->get(
    '/qibla-api', function ($request, $response, $args) {
    $args['apidocs'] = json_decode(file_get_contents('../html/apidocs/qibla/api_data.json'));
    $args['title'] = 'Qibla Direction API';
    $args['view'] = 'api';
    $args['holydayFloater'] = $this->get('holyDay');
    $args['noticeFloater'] = $this->get('noticeFloater');

    return $this->get('renderer')->render($response, 'qibla-api.phtml', $args);
}
);

$app->get(
    '/islamic-calendar-api', function ($request, $response, $args) {
    $args['apidocs'] = json_decode(file_get_contents('../html/apidocs/hijri/api_data.json'));
    $args['title'] = 'Islamic / Hijri Calendar API';
    $args['view'] = 'api';
    $args['holydayFloater'] = $this->get('holyDay');
    $args['noticeFloater'] = $this->get('noticeFloater');

    return $this->get('renderer')->render($response, 'islamic-calendar-api.phtml', $args);
}
);