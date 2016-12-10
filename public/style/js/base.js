$(function() {
	
//	var h=$(window).height();
	//	$("#menu").height(h);
	
	$("#s_menu").click(function(event){
			$("body").toggleClass("m_open");
			$(".bMask").toggle();
			event.stopPropagation();
		})
		
		$("body,.bMask").click(function(event){
			$("body").removeClass("m_open");
			$(".bMask").fadeOut();
			event.stopPropagation();
		})
		
	$(window).resize(function(){
	//	$("#menu").height($(window).height());
	})
	
	$(".li_toggle").click(function(){
		$(this).toggleClass("li_toggle_o");
		$(this).next(".toggle_con").toggle();
	})
	$(".dl_toggle .dt_tit").click(function(){
		$(this).toggleClass("dt_tit_c");
		$(this).parent(".dl_toggle").find(".toggle_con").toggle();
	})
	var w=$(".p_jindu").css("width");	
	var pw=$(".stepInfo").css("width");	
	$(".p_jindu").removeClass("p_jindu_0");
	if(w=="0px"){
		$(".p_jindu").addClass("p_jindu_0");
	}
	if(w==pw){
		$(".step_c").show();
	}


});


var initial_fontsize    = 14;
function setFontsize(type){
    var $whichEl = $(".zfont");
    if ($whichEl!=null) {
        if (type==1){
            if(initial_fontsize<44){
				$whichEl.css("font-size",(++initial_fontsize))
            }
        }else {
            if(initial_fontsize>8){
				$whichEl.css("font-size",(--initial_fontsize))
            }
        }
    }
}

function tb(obj,cntSelect){
	var _this=$(obj);
	var $container=$("#"+cntSelect);
	_this.addClass("current").siblings().removeClass("current");
//	_this.parent("ul").removeClass().addClass(pclass);
//	$container.css( "display", "block").siblings(".subbox").css( "display","none" );
	$container.fadeIn(1000).siblings(".subbox").css( "display","none" );
}

function loadCity(){
	$('body').append('<div class="tips-mask" style=""></div>');
	$(".change_city").show();
	$("body").addClass("body_mask");
	$(".tips-mask").click(function(event){
		$('.tips-mask').remove();
		$(".change_city").hide();
		$("body").removeClass("body_mask");
		event.stopPropagation();
	});
}
