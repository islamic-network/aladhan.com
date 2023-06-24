jQuery( document ).ready( function( $ ) {
    $.nsAlAdhan = {
        _location: '',
        _method: '',
        _school: '',
        _timings: '',
        _device: '',
        _timezonename: '',
        _locationFieldId: 'location',
        _methodFieldId: 'method',
        _latitudeAdjustmentFieldId: 'latiudeAdjustment',
        _midnightModeFieldId: 'midnightMode',
        _schoolFieldId: 'juristicSchool',
        _adhanFile: 'https://cdn.aladhan.com/audio/adhans/a1.mp3',
        // Fajr adhan from  http://www.assabile.com/adhan-call-prayer
        _fajrAdhanFile: 'https://cdn.aladhan.com/audio/adhans/fajr/f1.mp3',
        _matchedPrayer: '',
        _latitudeAdjustment: '',
        _juristicSchool: '',
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
        _methodSettings: '',
        _tuneString: '',
        _apiUrl: 'https://api.aladhan.com/v1/',
        _updated: '',
        _player: '',
        _nextPrayer: '',
        _timestamp: '',
        _daysAdjustment: 1,
        _invalidLocation: false,
        init: function() {
            var gc = this;
            gc._location = $('#' + gc._locationFieldId).val();
            gc.hideLocationError();
            $('#' + gc._locationFieldId).on('change', function() {
                gc.hideLocationError();
                gc._invalidLocation = false;
            });
            gc._method = $('#' + gc._methodFieldId).val();
            gc._latitudeAdjustment = $('#' + gc._latitudeAdjustmentFieldId).val();
            gc._school = $('#' + gc._schoolFieldId).val();
            gc._device = $("input[name=currenttimedevice]:radio:checked").val();
            $('#' + gc._methodFieldId).on('change', function() {
                if ($(this).find('option:selected').attr('value') == '99') {
                    $('.customSettingsForm').removeClass('hidden');
                } else {
                    $('.customSettingsForm').addClass('hidden');
                }
            });
            // We want to fetch prayer times initially, but this won't work as the latitude and longitude take time to come back from the Google API.
            // So instead of calling gc.fetchPrayerTimes(), we will simply let monitorLatLng run every 5 seconds, it won't find the timings so will try itself.
            // See if the adhan needs playing every 5 seconds.
            setInterval(function() {
                gc.monitor();
                gc.playAdhan();
                gc.getNextPrayerTime();
            }, 5000);
            setTimeout(function() {
                gc.adhanFileMonitor();
            }, 3000);
            // Update the tmings from the API once every 5 minutes. Setting timings to empty will do it via the monitor method.
            setInterval(function() {
                gc._timings = '';
                gc.getNextPrayerTime();
                gc.getAndDisplayDate();
            }, 300000);
        },
        monitor: function() {
            if (this._location == '') {
                this._location = $('#' + this._locationFieldId).val();
            }
            if (this._method == '') {
                this._method = $('#' + this._methodFieldId).val();
            }
            if (this._school == '') {
                this._school = $('#' + this._schoolFieldId).val();
            }
            if (this._latitudeAdjustment == '') {
                this._latitudeAdjustment = $('#' + this._latitudeAdjustmentFieldId).val();
            }
            if (this._device == '') {
                this._device = $("input[name=currenttimedevice]:radio:checked").val();
            }
            if (this._midnightMode == '') {
                this._midnightMode = $('#' + this._midnightModeFieldId).val();
            }
            if (this._customFajrAngle == '') {
                this._customFajrAngle = $('#customFajrAngle').val();
            }
            if (this._customMaghribAngle == '') {
                this._customMaghribAngle = $('#customMaghribAngle').val();
            }
            if (this._customIshaAngle == '') {
                this._customIshaAngle = $('#customIshaAngle').val();
            }
            if (this._methodSettings == '') {
                this._methodSettings = this._customFajrAngle + ',' + this._customMaghribAngle + ',' + this._customIshaAngle;
            }
            if (this._tuneString == '') {
                this._tuneImsak = $('#tuneImsak').val();
                this._tuneFajr = $('#tuneFajr').val();
                this._tuneSunrise = $('#tuneSunrise').val();
                this._tuneZhuhr = $('#tuneZhuhr').val();
                this._tuneAsr = $('#tuneAsr').val();
                this._tuneMaghrib = $('#tuneMaghrib').val();
                this._tuneSunset = $('#tuneSunset').val();
                this._tuneIsha = $('#tuneIsha').val();
                this._tuneMidnight = $('#tuneMidnight').val();
                this._tuneString = this._tuneImsak + ',' + this._tuneFajr + ',' + this._tuneSunrise + ',' +
                    this._tuneZhuhr + ',' + this._tuneAsr + ',' + this._tuneMaghrib + ',' + this._tuneSunset + ',' +
                    this._tuneIsha + ',' + this._tuneMidnight;
            }

            if (this._timings != '') {
                if (this._updated == 'y') {
                    this.updateTimesDisplay();
                }
            }

            // If all propertes are not empty.
            if (this._location != ''  && this._method != '' &&
                this._school != '' && this._device != '' &&
                this._latitudeAdjustment != '' && this._midnightMode != '' &&
                this._methodSettings != '' && this._tuneString != '') {
                // Check that the value of the properties has not changed.
                if (this._location != $('#' + this._locationFieldId).val()
                    || this._method != $('#' + this._methodFieldId).val()
                    || this._device != $("input[name=currenttimedevice]:radio:checked").val()
                    || this._latitudeAdjustment !=  $('#' + this._latitudeAdjustmentFieldId).val()
                    || this._school != $('#' + this._schoolFieldId).val()
                    || this._midnightMode != $('#' + this._midnightModeFieldId).val()
                    || this._methodSettings != $('#customFajrAngle').val() + ',' + $('#customMaghribAngle').val() + ',' + $('#customIshaAngle').val()
                    || this._tuneString != $('#tuneImsak').val() + ',' + $('#tuneFajr').val() + ',' + $('#tuneSunrise').val() + ',' +
                    $('#tuneZhuhr').val() + ',' + $('#tuneAsr').val() + ',' + $('#tuneMaghrib').val() + ',' + $('#tuneSunset').val() + ',' +
                    $('#tuneIsha').val() + ',' + $('#tuneMidnight').val()
                ) {
                    // If it has, update the object properties.
                    this._location = $('#' + this._locationFieldId).val();
                    this._method = $('#' + this._methodFieldId).val();
                    this._school = $('#' + this._schoolFieldId).val();
                    this._latitudeAdjustment = $('#' + this._latitudeAdjustmentFieldId).val();
                    this._device = $("input[name=currenttimedevice]:radio:checked").val();
                    this._midnightMode = $('#' + this._midnightModeFieldId).val();
                    this._methodSettings = $('#customFajrAngle').val() + ',' + $('#customMaghribAngle').val() + ',' + $('#customIshaAngle').val();
                    this._tuneString = $('#tuneImsak').val() + ',' + $('#tuneFajr').val() + ',' + $('#tuneSunrise').val() + ',' +
                    $('#tuneZhuhr').val() + ',' + $('#tuneAsr').val() + ',' + $('#tuneMaghrib').val() + ',' + $('#tuneSunset').val() + ',' +
                    $('#tuneIsha').val() + ',' + $('#tuneMidnight').val();
                    this._timings = '';
                }
                // console.log('-----------------------------------------');
                // console.log('INFO: Fetched prayer times successfully.');
                // console.log(this._timings);
            }
            if (this._timings == '') {
                // this.getTimeZone(); No longer needed as the API computes this automatically
                this.fetchPrayerTimes();

                this._updated = 'y';
            } else {
                this._updated = 'n';
            }
        },
        showLocationError: function() {
            $('#location-error').removeClass("hide-error");
        },
        hideLocationError: function() {
            $('#location-error').addClass("hide-error");
        },
        hasValidLocation: function() {
            return this._location != '' && this._invalidLocation == false;
        },
        fetchPrayerTimes: function() {
            var gc = this;
            if (gc.hasValidLocation()) {
                var credentials = {
                    address: gc._location,
                    method: gc._method,
                    latitudeAdjustmentMethod: gc._latitudeAdjustment,
                    school: gc._school,
                    adjustment: gc._daysAdjustment,
                    methodSettings: gc._methodSettings,
                    midnightMode: gc._midnightMode,
                    tune: gc._tuneString
                };

                $('.loader').show();
                // Post to API
                return $.ajax(
                    {
                        type: "GET",
                        url: gc._apiUrl + "timingsByAddress/" + gc.calculateCurrentTimestamp(),
                        cache: false,
                        data: credentials,
                        dataType: 'json',
                        timeout: 5000,
                        success: function(data) {
                            // Update timings
                            gc._timings = data.data.timings;
                            gc._invalidLocation = false;  
                        },
                        error: function(data, status, errorThrown) {
                            $('.loader').hide();
                            if (errorThrown == "Bad Request") {
                                gc._invalidLocation = true;
                                gc.showLocationError();
                            }
                        }
                    });
            }
        },
        updateTimesDisplay: function() {
            if (this._timings != '') {
                $.each(this._timings, function(i, v) {
                    // v has our times.
                    $('.time' + i).empty().html(v);
                });
                $('.loader').hide();
                $('.pt-heading span#timingLoc').html('<small> in ' + this._location + '</small>' );
            }
        },
        setAdhanFile: function() {
            var gc = this;
            if (gc._matchedPrayer === 'Fajr' && $("#different_fajr_adhan").is(':checked')) {
                gc._player.attr('src', gc._fajrAdhanFile);
            } else {
                gc._player.attr('src', gc._adhanFile);
            }
        },
        playAdhan: function() {
            var gc = this;
            var currentTime = gc.calculateCurrentTime();
            var match = gc.doesPrayerTimeMatch(currentTime);
            if (match) {
                if (gc._player[0].paused) {
                    gc.setAdhanFile();
                    gc._player[0].play();
                }
            }
        },
        calculateCurrentTimestamp: function() {
            var gc = this;
            var theTime;
            if (gc._device == 'client') {
                var date = new Date();
                theTime = date.getDate() + '-' + (date.getMonth() + 1) + '-' + date.getFullYear();
            } else {
                //gc._analytics('send', 'event', 'API', 'CurrentTimeStamp', 'Getting current timestamp from server.');
                gc.getTimeZone();
                var credentials = {
                    zone: gc._timezonename
                };
                // Get time from server.
                $.ajax({
                    type: "GET",
                    url: gc._apiUrl + "currentTimestamp",
                    cache: false,
                    data: credentials,
                    async: false, // This is required otherwise it proceeds to return without waiting for the response.
                    dataType: 'json',
                    success: function(data) {
                        theTime = data.data;
                    }
                });
            }
            gc._timestamp = theTime;

            return theTime;

        },
        calculateCurrentDate: function() {
            var gc = this;
            var theDate; // DD-MM-YYYY format

            var date = new Date();
            return date.getDate() + '-' + (date.getMonth() + 1) + '-' + date.getFullYear();

            var credentials = {
                zone: gc._timezonename
            };
            $.ajax({
                type: "GET",
                url: gc._apiUrl + "currentDate",
                cache: false,
                data: credentials,
                async: false, // This is required otherwise it proceeds to return without waiting for the response.
                dataType: 'json',
                success: function(data) {
                    theDate = data.data;
                }
            });

            return theDate;
        },
        calculateCurrentTime: function() {
            var gc = this;
            var theTime;
            if (gc._device == 'client') {
                var date = new Date;
                date.setTime(+new Date());

                var hours = date.getHours();
                if (hours.toString().length < 2) {
                    hours = '0' + hours;
                }
                var minutes = date.getMinutes();
                if (minutes.toString().length < 2) {
                    minutes = '0' + minutes;
                }
                var currentTime = hours + ':' + minutes;
                cT = currentTime.split(":");
                theTime = {
                    hh: cT[0],
                    mm: cT[1]
                };
            } else {
                gc.getTimeZone();
                var credentials = {
                    zone: gc._timezonename
                };
                $.ajax({
                    type: "GET",
                    url: gc._apiUrl + "currentTime",
                    cache: false,
                    data: credentials,
                    async: false, // This is required otherwise it proceeds to return without waiting for the response.
                    dataType: 'json',
                    success: function(data) {
                        result = data.data;
                        cT = result.split(":");
                        theTime = {
                            hh: cT[0],
                            mm: cT[1]
                        };
                    }
                });
            }
            return theTime;
        },
        doesPrayerTimeMatch: function(currentTime) {
            var gc = this;
            var result = false;
            $.each(gc._timings, function(i, v) {
                pT = v.split(":");
                var prayerTime = {
                    hh: pT[0],
                    mm: pT[1]
                };
                // console.log('INFO: ' + i + ' :: Current mins: ' + Math.round(currentTime.mm) + ' :: Prayer Mins: ' + Math.round(prayerTime.mm));
                if (prayerTime.hh == currentTime.hh) {
                    // Hours match, check minutes. Minutes need to be rounded because we converted them to strings. That actually may not be needed.
                    if (Math.round(currentTime.mm) == Math.round(prayerTime.mm)) {
                        // Current time equal to the prayer time? We check every 5 seconds so this should be good enough.
                        // Check that this value is not for sunset or sunrise or imsask or midnight.
                        if (i != 'Sunset' && i != 'Sunrise' && i != 'Imsak' && i != 'Midnight' && i != 'Firstthird' && i != 'Lastthird') {
                            console.log('INFO: Prayer times matched for ' + i);
                            result = true;
                            gc._matchedPrayer = i;
                        }
                    }
                }
            });
            if (result == false) {
                // console.log('INFO: Prayer times did not match.');
            }
            return result;
        },
        getTimeZone: function() {
            var gc = this;
            if (gc.hasValidLocation()) {
                credentials = {address: gc._location};
                return $.ajax({
                    type: "GET",
                    url: gc._apiUrl + "addressInfo",
                    cache: false,
                    data: credentials,
                    async: false, // This is required otherwise it proceeds to return without waiting for the response.
                    dataType: 'json',
                    success: function(data) {
                        result = data.data;
                        console.log(result);
                        gc._timezonename = result.timezone;
                    }
                });
            }
        },
        adhanFileMonitor: function() {
            var gc = this;
            gc._player = $('#adhanplayer');
            gc._player.attr('src', gc._adhanFile);
            $('#adhanfile').on('change', function() {
                gc._adhanFile = $(this).val();
                // Now also update the source of the player with this file.
                gc._player.attr('src', gc._adhanFile);
            });
            $('#fajrAdhanfile').on('change', function() {
                gc._fajrAdhanFile = $(this).val();
            });
        },
        getAndDisplayDate: function() {
            var gc = this;
            var theDate = gc.calculateCurrentDate();
            $.ajax({
                type: "GET",
                url: gc._apiUrl + "gToH",
                cache: false,
                data: { date: theDate, adjustment: gc._daysAdjustment },
                dataType: 'json',
                success: function(data) {
                    // Update timings
                    var date = data.data.gregorian.day + ' ' + data.data.gregorian.month.en + ', ' + data.data.gregorian.year + ' AD';
                    var hdate = data.data.hijri.day + ' ' + data.data.hijri.month.en + ', ' + data.data.hijri.year + ' AH';
                    $('.pt-heading').empty().append('Prayer Times <small>for</small><br /><small>' + date + '<br />' + hdate + '</small><br /><span id="timingLoc"></span>');
                    var holidays = '';

                    $(data.data.hijri.holidays).each(function(i, v) {
                        holidays += '<span class="label label-danger">' + v + '</span> ';
                    });

                    if (holidays != '') {
                        $('#holidays').html(holidays);
                        $('#holidays').parent().removeClass('hidden');
                    }
                }
            });
        },
        getNextPrayerTime: function() {
            var gc = this;
            if (gc.hasValidLocation()) {
                var credentials = {
                    address: gc._location,
                    method: gc._method,
                    latitudeAdjustmentMethod: gc._latitudeAdjustment,
                    school: gc._school,
                    adjustment: gc._daysAdjustment,
                    methodSettings: gc._methodSettings,
                    midnightMode: gc._midnightMode,
                    tune: gc._tuneString
                };

                // Post to API
                return $.ajax({
                    type: "GET",
                    url: gc._apiUrl + "nextPrayerByAddress/" + gc.calculateCurrentTimestamp(),
                    cache: false,
                    data: credentials,
                    dataType: 'json',
                    timeout: 5000,
                    success: function(data, status, request) {
                        // Update timings
                        gc._nextPrayer = data.data.timings;
                        gc._invalidLocation = false;
                        var pName = Object.keys(gc._nextPrayer)[0];
                        var pTime = gc._nextPrayer[pName];
                        // Fix Dhuhr name for display
                        if (pName == 'Dhuhr') {
                            pName = 'Zhuhr';
                        }
                        $('.nextPrayer span#time').html('Next Prayer: ' + pName + ' @ ' + pTime);
                    },
                    error: function(data, textStatus, errorThrown) {
                        $('.loader').hide();
                        if (errorThrown == "Bad Request") {
                            gc._invalidLocation = true;
                            gc.showLocationError();
                        }
                    }
                });
            }

        }
    }
});
