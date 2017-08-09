jQuery( document ).ready( function( $ ) {
    $.nsCalendar = {
        _timezonename: '',
        _method: '',
        _timings: '',
        _device: '',
        _location: '',
        _month: '',
        _year: '',
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
            // Run once anyway on load after 2 seconds, we'll have a caledar on page load if the stuff is filled in via the URL.
            setTimeout(function() {
                gc.fetchCalendar();
            }, 2000);

            // Show calendar settings when button is clicked.
            $('#showCalendarConfigFormButton').on('click', function() {
                $('#calendarConfigForm').slideDown();
                $('#showCalendarConfigForm').slideUp();
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

            if (gc._location != '') {
                $('.loader').show();
                var credentials = {
                    address: gc._location,
                    method: gc._method,
                    month: gc._month,
                    year: gc._year,
                    latitudeAdjustmentMethod: gc._latitudeAdjustment
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
                            html += '<td><span class="lead">' + v.date.readable + '</span></td>';
                            $.each(v.timings, function(name, time) {
                                if (name != 'Sunrise' && name != 'Sunset' && name !== 'Imsak' && name != 'Midnight') {
                                    html += '<td><span class="lead">';
                                    html +=  time;
                                    html += '</span></td>';
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
