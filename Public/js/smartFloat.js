// JavaScript Document

$.fn.smartFloat = function(e) {
    var position = function(element) {
        var top = element.position().top, pos = element.css("position");
        $(window).scroll(function() {
			
            var scrolls = $(this).scrollTop()-15;
			
			var maxscrolls=$(".fuwu").offset().top;
			
			var top2=maxscrolls-element.height()-25;
			
            if (scrolls > top&&scrolls<top2) {
                if (window.XMLHttpRequest) {
                    element.css({
                        position: "fixed",
                        top: e
                    })
                } else {
                    element.css({
                        top: scrolls
                    })
                }
            } 
			else if(scrolls>top2)
			   {
				  
				  element.css({
                    position: "absolute",
                    top: top2
                })
				
				}
			
			else {
                element.css({
                    position: ""
                })
            }
        })
    };
    return $(this).each(function() {
        position($(this))
    })
};
