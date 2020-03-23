$(function() {
    $('.settings-icon').on('click', function() {
        $('.timings-settings').show();
        $(this).hide();
        $('.minimise-icon').hide();
        $('.settings-close-icon').show();
        $('.timings').removeClass('fullscreenify');
    });

    $('.minimise-icon').on('click', function() {
        $('.timings').hide();
        $('.nextPrayer').show();
        //$('.timings').removeClass('fullscreenify');
    });

    $('.settings-close-icon').on('click', function() {
        $('.timings-settings').hide();
        $(this).hide();
        $('.settings-icon').show();
        $('.minimise-icon').show();
        $('.timings').addClass('fullscreenify');
    });

    $('#hideNextPrayer').on('click', function() {
        $('.nextPrayer').hide();
        $('.timings').show();
    });

    $('#time').on('click', function() {
        $('.nextPrayer').hide();
        $('.timings').show();
        $('#fullscreen').removeClass('hidden');
        $('#normalscreen').addClass('hidden');
        $('#footer').show();
        $('#navigation').show();
    });

    $('#fullscreen').on('click', function() {
        $(this).addClass('hidden');
        $('#normalscreen').removeClass('hidden');
        $('#footer').hide();
        $('#navigation').hide();
    });

    $('#normalscreen').on('click', function() {
        $(this).addClass('hidden');
        $('#fullscreen').removeClass('hidden');
        $('#footer').show();
        $('#navigation').show();
    });

    $('#different_fajr_adhan').on('click', function() {
        if($(this).is(":checked")) {
            $('#fajrAdhanfile').show(); 
        }
        if($(this).is(":not(:checked)")) {
            $('#fajrAdhanfile').hide(); 
        }
    });
});
