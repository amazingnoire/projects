jQuery(function PageScrolling(){
play=0;

  $(window).scroll(function ScrollStop(){
  if ($(window).scrollTop()==0 || $(document).height()==($(window).scrollTop()+$(window).height())) play=0;
});

if ($(window).scrollTop()>="250") $("#Go_Top").fadeIn("slow")
$(window).scroll(function Top(){
  if ($(window).scrollTop()<="250") $("#Go_Top").fadeOut("slow")
  else $("#Go_Top").fadeIn("slow")
});

if ($(window).scrollTop()<=$(document).height()-"999") $("#Go_Bottom").fadeIn("slow")
$(window).scroll(function Bottom(){
  if ($(window).scrollTop()>=$(document).height()-"999") $("#Go_Bottom").fadeOut("slow")
  else $("#Go_Bottom").fadeIn("slow")
});

$("#Go_Top").click(function TopClick(){
  if (play==0) { play=1; $("html, body").animate({scrollTop:0}, 3000); }
  else { play=0; $("html, body").stop(); }
})

$("#Go_Bottom").click(function BottomClick(){
  if (play==0) { play=1; $("html, body").animate({scrollTop:$(document).height()}, 3000); }
  else { play=0; $("html, body").stop(); }
})
});
