$(function(){
  // Background div responsive
  function bg_resize(){
    if($(window).width() > 1000){
      $(".background-div").css("left",($(window).width()-2160)/2+"px");
    }
    else {
      $(".background-div").css("left","-580px");
    }
  }
  bg_resize();
  $(window).resize(function(){
    bg_resize();
  });

});