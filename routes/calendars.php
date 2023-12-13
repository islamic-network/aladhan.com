<?php
$app->get(
    '/hijri-gregorian-calendar', function ($request, $response, $args) {
    $cs = $this->get('HijriCalendarService');

    $month = $cs->currentIslamicMonth()['data'];
    $year = $cs->currentIslamicYear()['data'];

    return $response->withHeader('Location', '/hijri-gregorian-calendar/' . $month . '/' . $year)->withStatus(301);
}
);

$app->get(
    '/hijri-gregorian-calendar/{m}/{y}', function ($request, $response, $args) {
    $adjustment = $this->get('hToGAdjustment');

    $cs = $this->get('HijriCalendarService');

    $m = (int)$request->getAttribute('m');
    if ($m > 12) {
        $m = 12;
    }
    if ($m < 1) {
        $m = 1;
    }
    $y = (int)$request->getAttribute('y');
    if ($y < 1) {
        $y = date('Y');
    }

    $days = 30; // Islamic months have 30 or less days - always.

    $cols = 7;
    $rows = $days / $cols;

    $calDays = $cs->hijriToGregorianCalendar($m, $y, $adjustment)['data'];

    $calendar[$y][$m]['days'] = array_combine(range(1, count($calDays)), array_values($calDays));


    if ($m == '12') {
        $nextMonth = '/1/' . ($y + 1);
        $prevMonth = '/' . ($m - 1) . '/1' . $y;
    } else if ($m == '1') {
        $prevMonth = '/12/' . ($y - 1);
        $nextMonth = '/' . ($m + 1) . '/' . $y;
    } else {
        $nextMonth = '/' . ($m + 1) . '/' . $y;
        $prevMonth = '/' . ($m - 1) . '/' . $y;
    }

    $nextMonth = '/hijri-gregorian-calendar' . $nextMonth;
    $prevMonth = '/hijri-gregorian-calendar' . $prevMonth;

    $args['title'] = 'Hijri / Islamic to Gregorian Calendar - ' . $calendar[$y][$m]['days'][1]['gregorian']['month']['en'] . ' ' . $y;
    $args['calendar'] = $calendar;
    $args['days'] = $days;
    $args['prevMonth'] = $prevMonth;
    $args['nextMonth'] = $nextMonth;
    $args['y'] = $y;
    $args['m'] = $m;
    $args['rows'] = $rows;
    $args['cols'] = $cols;
    $args['view'] = 'gToHCalendar';
    $args['holydayFloater'] = $this->get('holyDay');
    $args['noticeFloater'] = $this->get('noticeFloater');

    return $this->get('renderer')->render($response, 'h-g.phtml', $args);
}
);

$app->get(
    '/gregorian-hijri-calendar', function ($request, $response, $args) {

    $year = date('Y');
    $month = date('n');

    return $response->withHeader('Location', '/gregorian-hijri-calendar/' . $month . '/' . $year)->withStatus(301);
}
);

$app->get(
    '/gregorian-hijri-calendar/{m}/{y}', function ($request, $response, $args) {
    $adjustment = $this->get('gToHAdjustment');

    $m = (int)$request->getAttribute('m');
    if ($m > 12) {
        $m = 12;
    }
    if ($m < 1) {
        $m = 1;
    }
    $y = (int)$request->getAttribute('y');
    if ($y < 1) {
        $y = date('Y');
    }

    $days = cal_days_in_month(CAL_GREGORIAN, $m, $y);

    $cs = $this->get('HijriCalendarService');

    $cols = 7;
    $rows = $days / $cols;

    $calendar[$y][$m]['days'] = $cs->gregorianToHijriCalendar($m, $y, $adjustment)['data'];

    if ($m == '12') {
        $nextMonth = '/1/' . ($y + 1);
        $prevMonth = '/' . ($m - 1) . '/1' . $y;
    } else if ($m == '1') {
        $prevMonth = '/12/' . ($y - 1);
        $nextMonth = '/' . ($m + 1) . '/' . $y;
    } else {
        $nextMonth = '/' . ($m + 1) . '/' . $y;
        $prevMonth = '/' . ($m - 1) . '/' . $y;
    }

    $nextMonth = '/gregorian-hijri-calendar' . $nextMonth;
    $prevMonth = '/gregorian-hijri-calendar' . $prevMonth;

    $args['title'] = 'Gregorian to Hijri / Islamic Calendar - ' . $calendar[$y][$m]['days'][1]['gregorian']['month']['en'] . ' ' . $y;
    $args['calendar'] = $calendar;
    $args['days'] = $days;
    $args['prevMonth'] = $prevMonth;
    $args['nextMonth'] = $nextMonth;
    $args['y'] = $y;
    $args['m'] = $m;
    $args['rows'] = $rows;
    $args['cols'] = $cols;
    $args['view'] = 'gToHCalendar';
    $args['holydayFloater'] = $this->get('holyDay');
    $args['noticeFloater'] = $this->get('noticeFloater');

    return $this->get('renderer')->render($response, 'g-h.phtml', $args);
}
);

$app->get(
    '/islamic-holidays/{year}', function ($request, $response, $args) {
    // Add days adjustment here
    $adjustment = $this->get('hToGAdjustment');
    // Add days adjustment above

    $current_year = (int)$request->getAttribute('year');
    $years[$current_year - 1] = $current_year - 1;
    $years[$current_year] = $current_year;
    $years[$current_year + 1] = $current_year + 1;

    $cs = $this->get('HijriCalendarService');
    $days = $cs->specialDays()['data'];
    $months = $cs->islamicMonths()['data'];
    $currentIslamicYear = $cs->islamicYearFromGregorianForRamadan($current_year)['data'];
    $islamicYears[] = $currentIslamicYear - 2;
    $islamicYears[] = $currentIslamicYear - 1;
    $islamicYears[] = $currentIslamicYear;
    $islamicYears[] = $currentIslamicYear + 1;
    $islamicYears[] = $currentIslamicYear + 2;
    foreach ($islamicYears as $y) {
        $hols = $cs->hijriHolidaysByYear($y, $adjustment)['data'];
        foreach ($hols as $dkey => $h) {
            foreach ($years as $year) {
                if ($year == $h['gregorian']['year']) {
                    $days[$dkey][$year] = $h['gregorian'];
                }
            }
        }
    }

    $args['title'] = 'Islamic Holidays and Holy Days';
    $args['years'] = $years;
    $args['current_year'] = $current_year;
    $args['days'] = $days;
    $args['months'] = $months;
    $args['view'] = 'gToHCalendar';
    $args['holydayFloater'] = $this->get('holyDay');
    $args['noticeFloater'] = $this->get('noticeFloater');

    return $this->get('renderer')->render($response, 'islamic-holidays.phtml', $args);
}
);

$app->get(
    '/islamic-holidays', function ($request, $response, $args) {
    $current_year = date('Y');
    return $response->withStatus(301)->withHeader('Location', '/islamic-holidays/' . $current_year);

}
);

$app->get(
    '/calendar', function ($request, $response, $args) {
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

    $cy = date('Y');
    for ($i = $cy-5; $i <= $cy+5; $i++) {
        $years[$i] = $i;
    }
    $args['years'] = $years;
    $args['cmonth'] = date('m');
    $args['cyear'] = date('Y');
    $args['view'] = 'calendar';
    $args['holydayFloater'] = $this->get('holyDay');
    $args['noticeFloater'] = $this->get('noticeFloater');

    return $this->get('renderer')->render($response, 'calendar.phtml', $args);
}
);

$app->get(
    '/calendar/{city}/{country}', function ($request, $response, $args) {
    $city = $request->getAttribute('city');
    $country = $request->getAttribute('country');
    $adjustment = $this->get('gToHAdjustment');
    $calendar = [];
    $month = date('m');
    $year = date('Y');
    if ($city != null && $country != null) {
        $t = new \AlAdhanApi\CalendarByCity($city, $country, $month, $year, null, '', false, 0);
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
    $cy = date('Y');
    for ($i = $cy-5; $i <= $cy+5; $i++) {
        $years[$i] = $i;
    }
    $args['years'] = $years;
    $args['cmonth'] = $month;
    $args['cyear'] = $year;
    $args['currentDate'] = date('d') . ' ' . date('M') . ' ' . $year;
    $args['calendar'] = $calendar;
    $args['view'] = 'calendar';
    $args['holydayFloater'] = $this->get('holyDay');
    $args['noticeFloater'] = $this->get('noticeFloater');

    return $this->get('renderer')->render($response, 'calendar.phtml', $args);
}
);

$app->get(
    '/calendar/', function ($request, $response, $args) {
    return $response->withStatus(301)->withHeader('Location', '/calendar');
}
);
