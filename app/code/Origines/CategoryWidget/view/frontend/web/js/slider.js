define([
    'jquery',
    'slickSlider'
], function ($) {
    'use strict';

    var $screen_1280 = 1280,
        $screen_960 = 960,
        $screen_768 = 768;

    var customJS = {

        logoSlider: function(){

            var logoBannerSlider = $('.category-list-slider');
            if (logoBannerSlider.length) {
                logoBannerSlider.slick({
                    lazyLoad: 'ondemand',
                    dots: true,
                    slidesToShow: 6,
                    slidesToScroll: 1,
                    swipe: true,
                    autoplaySpeed: 3000,
                    autoplay: false,
                    responsive: [
                        {
                            breakpoint: $screen_1280,
                            settings: {
                                slidesToShow: 4,
                                slidesToScroll: 1
                            }
                        },
                        {
                            breakpoint: $screen_960,
                            settings: {
                                slidesToShow: 3,
                                slidesToScroll: 1,
                                swipe: true
                            }
                        },
                        {
                            breakpoint: $screen_768,
                            settings: {
                                slidesToShow: 2,
                                slidesToScroll: 1,
                                swipe: true
                            }
                        }
                    ]
                });
            }
        }
    };

    $(document).ready(function () {
        customJS.logoSlider();
    });

     /* Window resize function */
    var width = $(window).width(),
        resize = 0;
    $(window).resize(function () {
        var _self = $(this);
        resize++;
        setTimeout(function () {
            resize--;
            if (resize === 0) {
                // Done resize ...
                if (_self.width() !== width) {
                    width = _self.width();
                    // Done resize width ...
                    
                    // move banner to sidebar on mobile
                    customJS.logoSlider();
                }
            }
        }, 100);
    });
    return;
});
