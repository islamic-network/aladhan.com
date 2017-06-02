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
});
