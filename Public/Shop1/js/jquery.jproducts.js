// JavaScript Document

$(document).ready(function() { 
	$('.jqzoom').jqzoom({ 
		zoomType: 'standard',
		lens:true,
		preloadImages: false,
		alwaysOn:false,
		zoomWidth:360,
		zoomHeight:360
	});
	 jQuery("#mycarousel").jcarousel({
        scroll: 1,
		auto: 2,
        initCallback: mycarousel_initCallback,
        // This tells jCarousel NOT to autobuild prev/next buttons
        buttonNextHTML: null,
        buttonPrevHTML: null
    }); 
	 
});

 
function mycarousel_initCallback(carousel) {
	 jQuery('#mycarousel-next').bind('click', function() {
        carousel.next();
        return false;
    });

    jQuery('#mycarousel-prev').bind('click', function() {
        carousel.prev();
        return false;
    });
}