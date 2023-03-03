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

    $m = 9; // For Ramadan
    $gy = (int)$request->getAttribute('year');
    $city = (string)$request->getAttribute('city');
    $country = (string)$request->getAttribute('country');
    $latitudeAdjustmentMethod = !isset($request->getQueryParams()['latitudeAdjustmentMethod']) ? 3 : (int )$request->getQueryParams()['latitudeAdjustmentMethod'];
    $method = !isset($request->getQueryParams()['method']) ? '' : (int) $request->getQueryParams()['method'];

    $y = $this->get('HijriCalendarService')->islamicYearFromGregorianForRamadan($gy)['data'];
    $c = new \AlAdhanApi\CalendarByCity($city, $country, $m, $y, null, $method, true, $this->get('hToGAdjustment'));
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
    $args['holydayFloater'] = $this->get('holyDay');
    $args['noticeFloater'] = $this->get('noticeFloater');
    $cy = date('Y');
    for ($i = $cy-5; $i <= $cy+5; $i++) {
        $years[$i] = $i;
    }
    $args['years'] = $years;
    $args['method'] = $args['calendar'][0]['meta']['method']['id'];
    $args['lam'] = $latitudeAdjustmentMethod;

    return $this->get('renderer')->render($response, 'ramadan-prayer-times.phtml', $args);

}
);
$app->get(
    '/ramadan-calendar/{year}', function ($request, $response, $args) {

    // $this->logger->info("aladhan.com '/' ramadan-calendar");

    $cs = $this->get('HijriCalendarService');
    $m = 9;
    $gy = $request->getAttribute('year');

    $y = $cs->islamicYearFromGregorianForRamadan($gy)['data'];
    $c = new \AlAdhanApi\CalendarByCity('London', 'UK', $m, $y, null, \AlAdhanApi\Methods::MWL, true, $this->get('hToGAdjustment'));
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
    $args['holydayFloater'] = $this->get('holyDay');
    $args['noticeFloater'] = $this->get('noticeFloater');

    return $this->get('renderer')->render($response, 'ramadan-calendar.phtml', $args);
}
);
