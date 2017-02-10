var slideSpeed = 700;
var TimeOut = 3000;
var NeedLinks = true;

$(document).ready(function CSS() {
	$('.slide').css(
		{"position" : "absolute",
		 "top":'0', "left": '0'}).hide().eq(0).show();
	var slideNum = 0;
	var slideTime;
	slideCount = $("#slider .slide").size();
	var animslide = function Animation(arrow){
		clearTimeout(slideTime);
		$('.slide').eq(slideNum).fadeOut(slideSpeed);
		if(arrow == "next"){
			if(slideNum == (slideCount-1)){slideNum=0;}
			else{slideNum++}
			}
		else if(arrow == "prew")
		{
			if(slideNum == 0){slideNum=slideCount-1;}
			else{slideNum-=1}
		}
		else{
			slideNum = arrow;
			}
		$('.slide').eq(slideNum).fadeIn(slideSpeed, rotator);
		$(".control-slide.active").removeClass("active");
		$('.control-slide').eq(slideNum).addClass('active');
		}
if(NeedLinks){
var $linkArrow = $('<a id="prewbutton" href="#"></a><a id="nextbutton" href="#"></a>')
	.prependTo('#slider');		
	$('#nextbutton').click(function NextButton(){
		animslide("next");
		})
	$('#prewbutton').click(function PrewButton(){
		animslide("prew");
		})
}
	var $adderSpan = '';
	$('.slide').each(function Span(index) {
			$adderSpan += '<span class = "control-slide">' + index + '</span>';
		});
	$('<div class ="sli-links">' + $adderSpan +'</div>').appendTo('#slider-wrap');
	$(".control-slide:first").addClass("active");
	$('.control-slide').click(function AnimationNum(){
	var goToNum = parseFloat($(this).text());
	animslide(goToNum);
	});
	var pause = false;
	var rotator = function Rotator(){
			if(!pause){slideTime = setTimeout(function AnimationNext(){animslide('next')}, TimeOut);}
			}
	$('#slider-wrap').hover(	
		function MouseOver(){clearTimeout(slideTime); pause = true;},
		function MouseOut(){pause = false; rotator();
		});
});
