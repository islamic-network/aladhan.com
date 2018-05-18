$(document).ready( function() {
  $("li.dropdown-submenu > a").on("click", function(){
  $("li.dropdown-submenu").removeClass("active");
  $(this).parent().addClass("active");
  return false;
  });

  // Background imager
  var minImg = bgsJson.start;
  var maxImg = bgsJson.end;
  var bgInt = Math.floor(Math.random() * maxImg) + 1;
  setInterval(function() {
    var cdnurl = 'https://cdn.aladhan.com/images/backgrounds/';
    if (bgInt == maxImg) {
        // Reset
        bgInt = minImg - 1;
    }
    if (bgInt < maxImg) {
        // Increase as usual
        bgInt = bgInt + 1;
    }
    var bgImg = bgsJson.files[bgInt];
    $("body").css("background-image", "url(" + cdnurl + bgImg + ")");
  }, 18000);

});
