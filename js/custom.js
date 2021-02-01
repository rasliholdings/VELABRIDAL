jQuery(document).ready(function($){    
    
    var rtl, mrtl, slider_auto;
    
    if( blossom_mommy_blog_data.rtl == '1' ){
        rtl = true;
        mrtl = false;
    }else{
        rtl = false;
        mrtl = true;
    }

    if( blossom_mommy_blog_data.auto == '1' ){
        slider_auto = true;
    }else{
        slider_auto = false;
    }

    //banner layout two
    $('.slider-layout-two').owlCarousel({
        loop       : true,
        margin     : 30,
        nav        : true,
        items      : 1,
        dots       : false,
        autoplay   : slider_auto,
        animateOut : blossom_mommy_blog_data.animation,
        rtl        : rtl
    });

});
