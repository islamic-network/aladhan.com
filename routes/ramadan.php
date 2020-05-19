<?php

$app->get(
    '/ramadan-calendar/', function ($request, $response, $args) {
    return $response->withStatus(301)->withHeader('Location', '/ramadan-calendar/' . date('Y'));
}
);

$app->get(
    '/ramadan-calendar', function ($request, $response, $args) {

    return $response->withStatus(302)->withHeader('Location', '/ramadan-calendar/' . date('Y'));

}
);

$app->get(
    '/ramadan-prayer-times/{year}/{city}/{country}', function ($request, $response, $args) {
    // $this->logger->info("aladhan.com '/' ramadan-prayer-times");

    $m = 9; // For Ramadan
    $gy = (int)$request->getAttribute('year');
    $city = (string)$request->getAttribute('city');
    $country = (string)$request->getAttribute('country');
    $latitudeAdjustmentMethod = $request->getQueryParam('latitudeAdjustmentMethod') == null ? 3 : (int)$request->getQueryParam('latitudeAdjustmentMethod');
    $method = $request->getQueryParam('method') == null ? 2 : (int)$request->getQueryParam('method');

    $y = $this->HijriCalendarService->islamicYearFromGregorianForRamadan($gy)['data'];
    $c = new \AlAdhanApi\CalendarByCity($city, $country, $m, $y, null, $method, true, $this->hToGAdjustment);
    $days = 30; // Islamic months have 30 or less days - always.
    $cols = 7;
    $rows = $days / $cols;

    $args['title'] = 'Ramadan Prayer Times / Timetable for ' . $gy . ' in ' . $city . ', ' . $country;
    $args['calendar'] = $c->get()['data'];
    $args['days'] = $days;
    $args['y'] = $y;
    $args['gy'] = $gy;
    $args['m'] = $m;
    $args['rows'] = $rows;
    $args['cols'] = $cols;
    $args['view'] = 'ramadanCalendar';
    $args['holydayFloater'] = $this->holyDay;
    $args['noticeFloater'] = $this->noticeFloater;
    $args['years'] = array(
        '2015' => '2015',
        '2016' => '2016',
        '2017' => '2017',
        '2018' => '2018',
        '2019' => '2019',
        '2020' => '2020',
        '2021' => '2021',
    );
    $args['method'] = $method;
    $args['lam'] = $latitudeAdjustmentMethod;

    return $this->renderer->render($response, 'ramadan-prayer-times.phtml', $args);

}
);
$app->get(
    '/ramadan-calendar/{year}', function ($request, $response, $args) {

    // $this->logger->info("aladhan.com '/' ramadan-calendar");

    $cs = $this->HijriCalendarService;
    $m = 9;
    $gy = $request->getAttribute('year');

    $y = $cs->islamicYearFromGregorianForRamadan($gy)['data'];
    $c = new \AlAdhanApi\CalendarByCity('London', 'UK', $m, $y, null, \AlAdhanApi\Methods::MWL, true, $this->hToGAdjustment);
    $days = 30; // Islamic months have 30 or less days - always.
    $cols = 7;
    $rows = $days / $cols;
    $nextMonth = '/ramadan-calendar/' . ($gy + 1);
    $prevMonth = '/ramadan-calendar/' . ($gy - 1);

    $args['title'] = 'Ramadan Calendar - ' . $gy;
    $args['calendar'] = $c->get()['data'];
    $args['days'] = $days;
    $args['prevMonth'] = $prevMonth;
    $args['nextMonth'] = $nextMonth;
    $args['y'] = $y;
    $args['gy'] = $gy;
    $args['m'] = $m;
    $args['rows'] = $rows;
    $args['cols'] = $cols;
    $args['view'] = 'ramadanCalendar';
    $args['holydayFloater'] = $this->holyDay;
    $args['noticeFloater'] = $this->noticeFloater;

    return $this->renderer->render($response, 'ramadan-calendar.phtml', $args);
}
);
