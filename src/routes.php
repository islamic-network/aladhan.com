<?php

// Routes
$app->get('/', function ($request, $response, $args) {

    // $this->logger->info("aladhan.com '/' home");
    $args['title'] = 'Home';
    $args['view'] = 'home';
    $args['holydayFloater'] = $this->holyDay;

    return $this->renderer->render($response, 'index.phtml', $args);
});

$app->get('/about', function ($request, $response, $args) {

    // $this->logger->info("aladhan.com '/' about");
    $args['title'] = 'About';
    $args['view'] = 'about';
    $args['holydayFloater'] = $this->holyDay;

    return $this->renderer->render($response, 'about.phtml', $args);
});

$app->get('/calculation-methods', function ($request, $response, $args) {

    // $this->logger->info("aladhan.com '/' calculation-methods");
    $args['title'] = 'Prayer Time Calculation Methods';
    $args['view'] = '';
    $args['holydayFloater'] = $this->holyDay;

    return $this->renderer->render($response, 'calculation-methods.phtml', $args);
});

$app->get('/contact', function ($request, $response, $args) {

    // $this->logger->info("aladhan.com '/' contact");
    $args['title'] = 'Contact';
    $args['view'] = 'contact';
    $args['holydayFloater'] = $this->holyDay;

    return $this->renderer->render($response, 'contact.phtml', $args);
});

$app->get('/asma-al-husna-api', function ($request, $response, $args) {

    // $this->logger->info("aladhan.com '/' asma-al-husna-api");
    $args['apidocs'] = json_decode(file_get_contents('../html/apidocs/asmaAlHusna/api_data.json'));
    $args['title'] = 'Asma Al Husna  API';
    $args['view'] = 'api';
    $args['holydayFloater'] = $this->holyDay;

    return $this->renderer->render($response, 'asma-al-husna-api.phtml', $args);
});

$app->get('/prayer-times-api', function ($request, $response, $args) {

    // $this->logger->info("aladhan.com '/' prayer-times-api");
    $args['apidocs'] = json_decode(file_get_contents('../html/apidocs/timings/api_data.json'));
    $args['title'] = 'Prayer Times API';
    $args['view'] = 'api';
    $args['holydayFloater'] = $this->holyDay;

    return $this->renderer->render($response, 'prayer-times-api.phtml', $args);
});

$app->get('/calendar/', function ($request, $response, $args) {
    return $response->withStatus(301)->withHeader('Location', '/calendar');
});

$app->get('/play/', function ($request, $response, $args) {
    return $response->withStatus(301)->withHeader('Location', '/play');
});

$app->get('/rest-api', function ($request, $response, $args) {

    // $this->logger->info("aladhan.com '/' rest-api");
    return $response->withStatus(301)->withHeader('Location', '/prayer-times-api');
});

$app->get('/islamic-calendar-api', function ($request, $response, $args) {

    // $this->logger->info("aladhan.com '/' islamic-calendar-api");
    $args['apidocs'] = json_decode(file_get_contents('../html/apidocs/hijri/api_data.json'));
    $args['title'] = 'Islamic / Hijri Calendar API';
    $args['view'] = 'api';
    $args['holydayFloater'] = $this->holyDay;

    return $this->renderer->render($response, 'islamic-calendar-api.phtml', $args);
});

$app->get('/download-adhans', function ($request, $response, $args) {

    // $this->logger->info("aladhan.com '/' download-adhans");
    $args['title'] = 'Download Adhans';
    $args['view'] = 'api';
    $args['holydayFloater'] = $this->holyDay;

    return $this->renderer->render($response, 'download-adhans.phtml', $args);
});

$app->get('/ramadan-calendar/', function ($request, $response, $args) {
    return $response->withStatus(301)->withHeader('Location', '/ramadan-calendar/' . date('Y'));
});

$app->get('/ramadan-calendar', function ($request, $response, $args) {

    return $response->withStatus(302)->withHeader('Location', '/ramadan-calendar/' . date('Y'));

});

$app->get('/ramadan-prayer-times/{year}/{city}/{country}', function ($request, $response, $args) {
    // $this->logger->info("aladhan.com '/' ramadan-prayer-times");

    $m = 9; // For Ramadan
    $gy = (int) $request->getAttribute('year');
    $city = (string) $request->getAttribute('city');
    $country = (string) $request->getAttribute('country');
    $latitudeAdjustmentMethod =  $request->getQueryParam('latitudeAdjustmentMethod') == null ? 3 : (int) $request->getQueryParam('latitudeAdjustmentMethod') ;
    $method = $request->getQueryParam('method') == null ? 2 : (int) $request->getQueryParam('method');

    $y = $this->HijriCalendarService->islamicYearFromGregorianForRamadan($gy)['data'];
    $c = new \AlAdhanApi\CalendarByCity($city, $country, $m, $y, null, $method, true);
    $days = 30; // Islamic months have 30 or less days - always.
    $cols = 7;
    $rows = $days/$cols;

    $args['title'] = 'Ramadan Prayer Times / Timetable for ' . $gy . ' in ' . $city . ', ' . $country;
    $args['calendar'] = $c->get()['data'];
    $args['days'] = $days;
    $args['y']= $y;
    $args['gy']= $gy;
    $args['m']= $m;
    $args['rows'] = $rows;
    $args['cols'] = $cols;
    $args['view'] = 'ramadanCalendar';
    $args['holydayFloater'] = $this->holyDay;
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

});
$app->get('/ramadan-calendar/{year}', function ($request, $response, $args) {

    // $this->logger->info("aladhan.com '/' ramadan-calendar");

    $cs = $this->HijriCalendarService;
    $m = 9;
    $gy = $request->getAttribute('year');

    $y = $cs->islamicYearFromGregorianForRamadan($gy)['data'];
    $c = new \AlAdhanApi\CalendarByCity('London', 'UK', $m, $y, null, \AlAdhanApi\Methods::MWL, true);
    $days = 30; // Islamic months have 30 or less days - always.
    $cols = 7;
    $rows = $days/$cols;
    $nextMonth = '/ramadan-calendar/' . ($gy+1);
    $prevMonth = '/ramadan-calendar/' . ($gy-1);

    $args['title'] = 'Ramadan Calendar - ' . $gy;
    $args['calendar'] = $c->get()['data'];
    $args['days'] = $days;
    $args['prevMonth'] = $prevMonth;
    $args['nextMonth'] = $nextMonth;
    $args['y']= $y;
    $args['gy']= $gy;
    $args['m']= $m;
    $args['rows'] = $rows;
    $args['cols'] = $cols;
    $args['view'] = 'ramadanCalendar';
    $args['holydayFloater'] = $this->holyDay;

    return $this->renderer->render($response, 'ramadan-calendar.phtml', $args);
});

$app->get('/stats-api', function ($request, $response, $args) {
    // $this->logger->info("aladhan.com '/' stats-api");
    $args['title'] = 'AlAdhan API Statistics';
    $args['view'] = 'api';
    $args['days'] = (int) isset($_GET['days']) ? $_GET['days'] : 1;
    $args['node'] = (int) isset($_GET['node']) ? $_GET['node'] : 1;
    if (!in_array($args['node'], [1, 2])) {
        $args['node'] = 1;
    }
    $args['ipStats'] = isset($_GET['ip']) ? $_GET['ip'] : false;
    $args['origin'] = isset($_GET['origin']) ? $_GET['origin'] : false;
    $args['holydayFloater'] = $this->holyDay;
    if ($args['ipStats'] !== 'true') {
        $args['ipStats'] = false;
    }

    if ($args['origin'] !== 'true') {
        $args['origin'] = false;
    }
    // Read stats
    $args['lines'] = array_reverse(file(realpath($_SERVER['DOCUMENT_ROOT'] . '/../../stats/') . '/statistics' . $args['node']. '.log'));

    return $this->renderer->render($response, 'stats-api.phtml', $args);
});

$app->get('/consumers-api', function ($request, $response, $args) {

    // $this->logger->info("aladhan.com '/' consumers-api");
    $args['title'] = 'Apps and Websites using the AlAdhan API';
    $args['view'] = 'api';
    $args['holydayFloater'] = $this->holyDay;

    return $this->renderer->render($response, 'consumers-api.phtml', $args);
});

$app->get('/clients-api', function ($request, $response, $args) {

    // $this->logger->info("aladhan.com '/' clients-api");
    $args['title'] = 'API Clients';
    $args['view'] = 'api';
    $args['holydayFloater'] = $this->holyDay;

    return $this->renderer->render($response, 'clients-api.phtml', $args);
});

$app->get('/hijri-gregorian-calendar', function ($request, $response, $args) {

    $adjustment = 0;

    $cs = $this->HijriCalendarService;

    $m = isset($_GET['m']) ? (int) $_GET['m'] : $cs->currentIslamicMonth()['data'];
    if ($m > 12) {
        $m = 12;
    }
    if ($m < 1) {
        $m = 1;
    }
    $y = isset($_GET['y']) ? (int) $_GET['y'] : $cs->currentIslamicYear()['data'];
    if ($y < 1) {
        $y = date('Y');
    }

    $days = 30; // Islamic months have 30 or less days - always.

    $cols = 7;
    $rows = $days/$cols;

    $calDays = $cs->hijriToGregorianCalendar($m, $y, $adjustment)['data'];

    $calendar[$y][$m]['days'] = array_combine(range(1, count($calDays)), array_values($calDays));


    if ($m == '12') {
        $nextMonth = '?m=1&y=' . ($y + 1);
        $prevMonth = '?m=' . ($m - 1) . '&y=' . $y;
    } else if ($m == '1') {
        $prevMonth = '?m=12&y=' . ($y - 1);
        $nextMonth = '?m=' . ($m + 1) . '&y=' . $y;
    } else {
        $nextMonth = '?m=' . ($m + 1) . '&y=' . $y;
        $prevMonth = '?m=' . ($m - 1) . '&y=' . $y;
    }

    $args['title'] = 'Hijri / Islamic to Gregorian Calendar - ' . $calendar[$y][$m]['days'][1]['gregorian']['month']['en'] . ' ' . $y;
    $args['calendar'] = $calendar;
    $args['days'] = $days;
    $args['prevMonth'] = $prevMonth;
    $args['nextMonth'] = $nextMonth;
    $args['y']= $y;
    $args['m']= $m;
    //$args['row'] = $row;
    $args['rows'] = $rows;
    //$args['col'] = $col;
    $args['cols'] = $cols;
    $args['view'] = 'gToHCalendar';
    $args['holydayFloater'] = $this->holyDay;

    return $this->renderer->render($response, 'h-g.phtml', $args);
});

$app->get('/gregorian-hijri-calendar', function ($request, $response, $args) {

    $adjustment = 0;

    $m = isset($_GET['m']) ? (int) $_GET['m'] : date('m');
    if ($m > 12) {
        $m = 12;
    }
    if ($m < 1) {
        $m = 1;
    }
    $y = isset($_GET['y']) ? (int) $_GET['y'] : date('Y');
    if ($y < 1) {
        $y = date('Y');
    }

    $days = cal_days_in_month(CAL_GREGORIAN, $m, $y);

    $cs = $this->HijriCalendarService;

    $cols = 7;
    $rows = $days/$cols;

    $calendar[$y][$m]['days'] = $cs->gregorianToHijriCalendar($m, $y, $adjustment)['data'];

    if ($m == '12') {
        $nextMonth = '?m=1&y=' . ($y + 1);
        $prevMonth = '?m=' . ($m - 1) . '&y=' . $y;
    } else if ($m == '1') {
        $prevMonth = '?m=12&y=' . ($y - 1);
        $nextMonth = '?m=' . ($m + 1) . '&y=' . $y;
    } else {
        $nextMonth = '?m=' . ($m + 1) . '&y=' . $y;
        $prevMonth = '?m=' . ($m - 1) . '&y=' . $y;
    }

    $args['title'] = 'Gregorian to Hijri / Islamic Calendar - ' . $calendar[$y][$m]['days'][1]['gregorian']['month']['en'] . ' ' . $y;
    $args['calendar'] = $calendar;
    $args['days'] = $days;
    $args['prevMonth'] = $prevMonth;
    $args['nextMonth'] = $nextMonth;
    $args['y']= $y;
    $args['m']= $m;
    $args['rows'] = $rows;
    $args['cols'] = $cols;
    $args['view'] = 'gToHCalendar';
    $args['holydayFloater'] = $this->holyDay;

    return $this->renderer->render($response, 'g-h.phtml', $args);
});

$app->get('/islamic-holidays', function ($request, $response, $args) {

    // $this->logger->info("aladhan.com '/' islamic-holidays");

    // Add days adjustment here
    $adjustment = 0;
    // Add days adjustment above

    $current_year = date('Y');
    $years[$current_year - 1] = $current_year - 1;
    $years[$current_year] = $current_year;
    $years[$current_year + 1] = $current_year + 1;

    $cs = $this->HijriCalendarService;
    $days = $cs->specialDays()['data'];
    $months = $cs->islamicMonths()['data'];
    $currentIslamicYear = $cs->currentIslamicYear()['data'];
    $islamicYears[] = $currentIslamicYear - 2;
    $islamicYears[] = $currentIslamicYear - 1;
    $islamicYears[] = $currentIslamicYear;
    $islamicYears[] = $currentIslamicYear + 1;
    $islamicYears[] = $currentIslamicYear + 2;

    foreach ($islamicYears as $y) {
        $hols = $cs->hijriHolidaysByYear($y, $adjustment)['data'];
        foreach ($hols as $dkey => $h) {
            foreach($years as $year) {
                if ($year == $h['gregorian']['year']) {
                    $days[$dkey][$year] = $h['gregorian'];
                }
            }
        }
    }

    $args['title'] = 'Islamic Holidays and Holy Days';
    $args['years'] = $years;
    $args['current_year'] = $current_year;
    $args['days']= $days;
    $args['months'] = $months;
    $args['view'] = 'gToHCalendar';
    $args['holydayFloater'] = $this->holyDay;


    return $this->renderer->render($response, 'islamic-holidays.phtml', $args);
});


$app->get('/play', function ($request, $response, $args) {

    // $this->logger->info("aladhan.com '/' play");
    $args['title'] = 'Adhan Player and Prayer Times Today';
    $args['city'] = '';
    $args['country'] = '';
    $args['view'] = 'play';
    $args['holydayFloater'] = $this->holyDay;

    return $this->renderer->render($response, 'play.phtml', $args);
});

$app->get('/play/{city}/{country}', function ($request, $response, $args) {
    $city = $request->getAttribute('city');
    $country = $request->getAttribute('country');
    $adjustment = 0;
    $times = [];
    if ($city != null && $country != null) {
	// ISNA is default method. Adjustment of +1 day added for Dhul Hijjah 2018.
        $t = new \AlAdhanApi\TimesByCity($city, $country, null, null, 2, $adjustment);
        $times = $t->get()['data'];
    }
    // $this->logger->info("aladhan.com '/' play");
    $args['title'] = 'Adhan Player and Prayer Times Today | ' . $city . ' '. $country;
    $args['city'] = $city;
    $args['country'] = $country;
    $args['view'] = 'play';
    $args['holydayFloater'] = $this->holyDay;
    $args['times'] = $times;

    return $this->renderer->render($response, 'play.phtml', $args);

});

$app->get('/play/{city}/{country}/{a}/{b}', function ($request, $response, $args) {
    $city = $request->getAttribute('city');
    $country = $request->getAttribute('country');

    return $response->withStatus(301)->withHeader('Location', '/play/' . $city . '/' . $country);
});

$app->get('/calendar/{city}/{country}/{a}/{b}', function ($request, $response, $args) {
    $city = $request->getAttribute('city');
    $country = $request->getAttribute('country');

    return $response->withStatus(301)->withHeader('Location', '/calendar/' . $city . '/' . $country);
});

$app->get('/credits-and-terms', function ($request, $response, $args) {
    // $this->logger->info("aladhan.com '/' credits-and-terms");
    $args['title'] = 'Credits, Terms and Conditions';
    $args['view'] = 'terms';
    $args['holydayFloater'] = $this->holyDay;

    return $this->renderer->render($response, 'terms.phtml', $args);
});

$app->get('/calendar', function ($request, $response, $args) {

    // $this->logger->info("aladhan.com '/' calendar");
    $args['title'] = 'Prayer Times Calendar';
    $args['city'] = '';
    $args['country'] = '';
    $args['months'] = array(
        '01' => 'January',
        '02' => 'February',
        '03' => 'March',
        '04' => 'April',
        '05' => 'May',
        '06' => 'June',
        '07' => 'July',
        '08' => 'August',
        '09' => 'September',
        '10' => 'October',
        '11' => 'November',
        '12' => 'December'
    );

    $args['years'] = array(
        '2015' => '2015',
        '2016' => '2016',
        '2017' => '2017',
        '2018' => '2018',
        '2019' => '2019',
        '2020' => '2020',
        '2021' => '2021',
    );

    $args['cmonth'] = date('m');
    $args['cyear'] = date('Y');
    $args['view'] = 'calendar';
    $args['holydayFloater'] = $this->holyDay;

    return $this->renderer->render($response, 'calendar.phtml', $args);
});

$app->get('/calendar/{city}/{country}', function ($request, $response, $args) {
    $city = $request->getAttribute('city');
    $country = $request->getAttribute('country');
    $adjustment = 0;
    $calendar = [];
    $month = date('m');
    $year = date('Y');
    if ($city != null && $country != null) {
        $t = new \AlAdhanApi\CalendarByCity($city, $country, $month, $year, null, 2, false, 0);
        $calendar = $t->get()['data'];
    }

    // $this->logger->info("aladhan.com '/' calendar");
    $args['title'] = 'Prayer Times Calendar | ' . $city . ' ' . $country . ' | ' . $calendar[0]['date']['gregorian']['month']['en'] . ', ' . $year;
    $args['city'] = $city;
    $args['country'] = $country;
    $args['months'] = array(
        '01' => 'January',
        '02' => 'February',
        '03' => 'March',
        '04' => 'April',
        '05' => 'May',
        '06' => 'June',
        '07' => 'July',
        '08' => 'August',
        '09' => 'September',
        '10' => 'October',
        '11' => 'November',
        '12' => 'December'
    );

    $args['years'] = array(
        '2015' => '2015',
        '2016' => '2016',
        '2017' => '2017',
        '2018' => '2018',
        '2019' => '2019',
        '2020' => '2020',
        '2021' => '2021',
    );

    $args['cmonth'] = $month;
    $args['cyear'] = $year;
    $args['currentDate'] = date('d') . ' ' . date('M') . ' ' . $year;
    $args['calendar'] = $calendar;
    $args['view'] = 'calendar';
    $args['holydayFloater'] = $this->holyDay;

    return $this->renderer->render($response, 'calendar.phtml', $args);
});


$app->post('/download/{format}', function (\Slim\Http\Request $request, $response, $args) {
    $format = $request->getAttribute('format');
    $data = $request->getParsedBodyParam('data');
    $timings = json_decode($data);
    if (is_object($timings)) {
        $ts = [];
        foreach ($timings as $month => $tx) {
            foreach ($tx as $ty) {
                $ts[] = (array) $ty;
            }
        }
        $timings = $ts;
    }

    if ($format === 'csv' && is_array($timings)) {
        $flattener = new \NestedJsonFlattener\Flattener\Flattener();
        $flattener->setArrayData($timings);
        $file = 'csv'.rand();
        $flattener->writeCsv($file);

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($file).'.csv"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        readfile($file.'.csv');
        unlink($file.'.csv');
    }
});
