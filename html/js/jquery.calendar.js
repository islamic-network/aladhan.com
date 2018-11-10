jQuery( document ).ready( function( $ ) {
    $.nsCalendar = {
        _timezonename: '',
        _method: '',
        _timings: '',
        _device: '',
        _location: '',
        _month: '',
        _year: '',
        _midnightMode: '',
        _customFajrAngle: '',
        _customMaghribAngle: '',
        _customIshaAngle: '',
        _tuneImsak: '',
        _tuneFajr: '',
        _tuneSunrise: '',
        _tuneZhuhr: '',
        _tuneAsr: '',
        _tuneMaghrib: '',
        _tuneSunset: '',
        _tuneIsha: '',
        _tuneMidnight: '',
        _latitudeAdjustment: '',
        _latitudeFieldId: 'latitude',
        _longitudeFieldId: 'longitude',
        _timezoneFieldId: 'timezone',
        _methodFieldId: 'method',
        _monthFieldId: 'month',
        _locationFieldId: 'location',
        _yearFieldId: 'year',
        _calendarButtonId: 'generateCalendar',
        _latitudeAdjustmentFieldId: 'latiudeAdjustment',
        _apiUrl: 'https://api.aladhan.com/',
        init: function() {
            var gc = this;
            // Monitor button to generate calendar
            $('#' + gc._calendarButtonId).on('click', function() {
                gc.fetchCalendar();
            });

            // Show calendar settings when button is clicked.
            $('#showCalendarConfigFormButton').on('click', function() {
                $('#calendarConfigForm').slideDown();
                $('#showCalendarConfigForm').slideUp();
            });

            $('#' + gc._methodFieldId).on('change', function() {
                if ($(this).find('option:selected').attr('value') == '99') {
                    $('.customSettingsForm').removeClass('hidden');
                } else {
                    $('.customSettingsForm').addClass('hidden');
                }
            });
        },
        fetchCalendar: function() {
            var gc = this;
            gc._location = $('#' + gc._locationFieldId).val();
            //gc.getTimeZone(false);
            gc._method = $('#' + gc._methodFieldId).val();
            gc._month = $('#' + gc._monthFieldId).val();
            gc._monthName = $('#' + gc._monthFieldId).find('option:selected').attr('name');
            gc._year = $('#' + gc._yearFieldId).val();
            gc._latitudeAdjustment = $('#' + gc._latitudeAdjustmentFieldId).val();
            var locationName = $('#location').val();
            gc._midnightMode = $('#midnightMode').find('option:selected').attr('name');
            gc._customFajrAngle = $('#customFajrAngle').val();
            gc._customMaghribAngle = $('#customMaghribAngle').val();
            gc._customIshaAngle = $('#customIshaAngle').val();
            gc._tuneImsak = $('#tuneImsak').val();
            gc._tuneFajr = $('#tuneFajr').val();
            gc._tuneSunrise = $('#tuneSunrise').val();
            gc._tuneZhuhr = $('#tuneZhuhr').val();
            gc._tuneAsr = $('#tuneAsr').val();
            gc._tuneMaghrib = $('#tuneMaghrib').val();
            gc._tuneSunset = $('#tuneSunset').val();
            gc._tuneIsha = $('#tuneIsha').val();
            gc._tuneMidnight = $('#tuneMidnight').val();
            var tuneString = gc._tuneImsak + ',' +
            gc._tuneFajr + ',' +
            gc._tuneSunrise + ',' +
            gc._tuneZhuhr + ',' +
            gc._tuneAsr + ',' +
            gc._tuneSunset + ',' +
            gc._tuneMaghrib + ',' +
            gc._tuneIsha + ',' +
            gc._tuneMidnight;
            var methodSettings = gc._customFajrAngle + ',' +
            gc._customMaghribAngle + ',' +
            gc._customIshaAngle;

            if (gc._location != '') {
                $('.loader').show();
                if (gc._method == 99) {
                    var credentials = {
                        address: gc._location,
                        method: gc._method,
                        month: gc._month,
                        year: gc._year,
                        latitudeAdjustmentMethod: gc._latitudeAdjustment,
                        midnightMode: gc._midnightMode,
                        tune: tuneString,
                        methodSettings: methodSettings
                    }
                } else {
                    var credentials = {
                        address: gc._location,
                        method: gc._method,
                        month: gc._month,
                        year: gc._year,
                        latitudeAdjustmentMethod: gc._latitudeAdjustment,
                        midnightMode: gc._midnightMode,
                        tune: tuneString
                    }
                };
                // Post to API
                $.ajax({
                    type: "GET",
                    url: gc._apiUrl + "calendarByAddress",
                    cache: false,
                    data: credentials,
                    dataType: 'json',
                    success: function(data) {
                        // Update timings
                        var dBrowser = new Date();
                        var dateBrowser = dBrowser.toDateString();

                        var html = '';
                        $.each(data.data, function(i, v) {
                            //console.log(v.date.readable);
                            var bgColor = '';
                            var dCalc = new Date(v.date.timestamp * 1000);
                            var dateCalc = dCalc.toDateString();
                            if (dateBrowser == dateCalc) {
                                bgColor = 'danger';
                            }
                            html += '<tr class="show-grid ' +  bgColor + '">';
                            html += '<td>' + v.date.readable + '</td>';
                            $.each(v.timings, function(name, time) {
                                if ( name != 'Sunset' && name !== 'Imsak') {
                                    html += '<td>';
                                    html +=  time;
                                    html += '</td>';
                                }
                            });

                            html += '</tr>';
                        });
                        $('.timesLoader').hide();
                        $('#generatedCalendar').html(html);
                        // Slide up calendar config
                        $('#calendarConfigForm').slideUp();
                        $('#showCalendarConfigForm').slideDown();
                        // Change calendar heading
                        $('#calendarHeading').html(locationName + ' - ' + gc._monthName
                                + ', ' + gc._year);
                    }
                });
                $('.loader').hide();
            } else {
                //alert('Sorry! Unable to access Aladhan API.');
            }
        },
        updateTimesDisplay: function() {
            $.each(this._timings, function(i, v) {
                // v has our times.
                $('.time' + i).empty().html(v);
            });
            if (this._timings != '') {
                $('.loader').hide();
            }
        }
    }
});
