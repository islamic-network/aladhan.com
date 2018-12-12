jQuery( document ).ready( function( $ ) {
    $.nsAlAdhan = {
        _location: '',
        _method: '',
        _school: '',
        _timings: '',
        _device: '',
        _timezonename: 'Europe/London',
        _currentlyPlaying: false,
        _locationFieldId: 'location',
        _methodFieldId: 'method',
        _latitudeAdjustmentFieldId: 'latiudeAdjustment',
        _schoolFieldId: 'juristicSchool',
        _adhanFile: 'https://cdn.aladhan.com/audio/adhans/a1.mp3',
        _latitudeAdjustment: '',
        _juristicSchool: '',
        _apiUrl: 'https://api.aladhan.com/v1/',
        _updated: '',
        _player: '',
        _nextPrayer: '',
        _paused: true,
        _timestamp: '',
        _daysAdjustment: 1,
        init: function() {
            var gc = this;
            gc._location = $('#' + gc._locationFieldId).val();
            gc._method = $('#' + gc._methodFieldId).val();
            gc._latitudeAdjustment = $('#' + gc._latitudeAdjustmentFieldId).val();
            gc._school = $('#' + gc._schoolFieldId).val();
            gc._device = $("input[name=currenttimedevice]:radio:checked").val();
            // We want to fetch prayer times initially, but this won't work as the latitude and longitude take time to come back from the Google API.
            // So instead of calling gc.fetchPrayerTimes(), we will simply let monitorLatLng run every 5 seconds, it won't find the timings so will try itself.
            // See if the adhan needs playing every 2 seconds.
            setInterval(function() {
                gc.monitor();
                gc.playAdhan();
                gc.getNextPrayerTime();
            }, 3000);
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

            if (this._timings != '') {
              if (this._updated == 'y') {
                this.updateTimesDisplay();
              }
            }

            // If all propertes are not empty.
            if (this._location != ''  && this._method != '' && this._school != '' && this._device != '' && this._latitudeAdjustment != '') {
                // Check that the value of the properties has not changed.
                if (this._location != $('#' + this._locationFieldId).val()
                    || this._method != $('#' + this._methodFieldId).val()
                    || this._device != $("input[name=currenttimedevice]:radio:checked").val()
                    || this._latitudeAdjustment !=  $('#' + this._latitudeAdjustmentFieldId).val()
                    || this._school != $('#' + this._schoolFieldId).val()
                  ) {
                    // If it has, update the object properties.
                    this._location = $('#' + this._locationFieldId).val();
                    this._method = $('#' + this._methodFieldId).val();
                    this._school = $('#' + this._schoolFieldId).val();
                    this._latitudeAdjustment = $('#' + this._latitudeAdjustmentFieldId).val();
                    this._device = $("input[name=currenttimedevice]:radio:checked").val();
                    this._timings = '';
                }
                //console.log(this._timings);
            }
            if (this._timings == '') {
              this.getTimeZone();
              this.fetchPrayerTimes();
              this._updated = 'y';
            } else {
              this._updated = 'n';
            }
        },
        fetchPrayerTimes: function() {
            var gc = this;
            if (gc._location != '') {
                var credentials = {
                    address: gc._location,
                    method: gc._method,
                    latitudeAdjustmentMethod: gc._latitudeAdjustment,
                    school: gc._school,
                    adjustment: gc._daysAdjustment
                };

                $('.loader').show();
                // Post to API
                return $.ajax({
                    type: "GET",
                    url: gc._apiUrl + "timingsByAddress/" + gc.calculateCurrentTimestamp(),
                    cache: false,
                    data: credentials,
                    dataType: 'json',
                    timeout: 5000,
                    success: function(data) {
                        // Update timings
                        gc._timings = data.data.timings;
                    },
                    error: function() {
                      console.log('API threw an error!');
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
        playAdhan: function() {
            var gc = this;
            var currentTime = gc.calculateCurrentTime();
            var match = gc.doesPrayerTimeMatch(currentTime);
            if (match) {
                gc.setMEStatus();
                if (gc._currentlyPlaying !== true && gc._paused === true) {
                    gc._player.setSrc(gc._adhanFile);
                    gc._player.play();
                    gc._currentlyPlaying = true;
                }
            } else {
                gc._currentlyPlaying = false;
            }
        },
        calculateCurrentTimestamp: function() {
            var gc = this;
            var theTime;
            if (gc._device == 'client') {
                theTime = Math.round(+new Date()/1000);
            } else {
                //gc._analytics('send', 'event', 'API', 'CurrentTimeStamp', 'Getting current timestamp from server.');
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
                }
                if (prayerTime.hh == currentTime.hh) {
                    // Hours match, check minutes. Minutes need to be rounded because we converted them to strings. That actually may not be needed.
                    if (Math.round(currentTime.mm) == Math.round(prayerTime.mm)) {
                        //console.log('Current mins: ' + Math.round(currentTime.mm) + ' Prayer Mins: ' + Math.round(prayerTime.mm));
                        //console.log(i);
                        // Current time equal to the prayer time? We check every 5 seconds so this should be good enough.
                        // Check that this value is not for sunset or sunrise or imsask or midnight.
                        if (i != 'Sunset' && i != 'Sunrise' && i != 'Imsak' && i != 'Midnight') {
                            //console.log('Prayer time matched');
                            result = true;
                            //return false;
                        }
                    }
                }
            });
            return result;
        },
        getTimeZone: function() {
          var gc = this;
          if (gc._location != '') {
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
                  gc._timezonename = result.timezone;
              }
          });
          }
        },
        adhanFileMonitor: function() {
            var gc = this;
            gc._player = new MediaElementPlayer('#adhanplayer', {
                //features: ['playpause', 'duration', 'volume', 'current'],
                defaultAudioWidth: 250
            });
            gc._player.setSrc(gc._adhanFile);
            $('#adhanfile').on('change', function() {
                gc._adhanFile = $(this).val();
                // Now also update the source of the player with this file.
                gc._player.setSrc(gc._adhanFile);
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
            if (gc._location != '') {
                var credentials = {
                    address: gc._location,
                    method: gc._method,
                    latitudeAdjustmentMethod: gc._latitudeAdjustment,
                    school: gc._school
                };
                // Post to API
                return $.ajax({
                    type: "GET",
                    url: gc._apiUrl + "nextPrayerByAddress/" + gc.calculateCurrentTimestamp(),
                    cache: false,
                    data: credentials,
                    dataType: 'json',
                    timeout: 5000,
                    success: function(data) {
                        // Update timings
                        gc._nextPrayer = data.data.timings;
                        //console.log(gc._nextPrayer);
                        var pName = Object.keys(gc._nextPrayer)[0];
                        var pTime = gc._nextPrayer[pName];
                        // Fix Dhuhr name for display
                        if (pName == 'Dhuhr') {
                            pName = 'Zhuhr';
                        }
                        $('.nextPrayer span#time').html('Next Prayer: ' + pName + ' @ ' + pTime);
                    },
                    error: function() {
                      console.log('API threw an error!');
                    }
                });
            }

        },
        setMEStatus: function() {
            var gc = this;
            $.each(gc._player.$media, function(i,v) {
                gc._paused = v.paused;
            });
        }
    }
});
