/**
 * Zanvarsity Carousel Initialization
 * This script handles the initialization and configuration of the homepage carousel
 */

(function($) {
    'use strict';

    // Wait for window and all assets to load
    $(window).on('load', function() {
        console.log('Window and all assets loaded, initializing carousel...');
        
        var $carousel = $(".image-carousel");
        
        if ($carousel.length) {
            console.log('Carousel element found, initializing...');
            
            // Ensure the carousel has the owl-carousel class
            $carousel.addClass('owl-carousel');
            
            // Initialize Owl Carousel with debug options
            try {
                $carousel.owlCarousel({
                    items: 1,
                    loop: true,
                    margin: 0,
                    nav: true,
                    dots: true,
                    autoplay: true,
                    autoplayTimeout: 5000,
                    autoplayHoverPause: true,
                    smartSpeed: 800,
                    lazyLoad: true,
                    navText: [
                        '<i class="fa fa-angle-left" aria-hidden="true"></i>',
                        '<i class="fa fa-angle-right" aria-hidden="true"></i>'
                    ],
                    responsive: {
                        0: { items: 1 },
                        600: { items: 1 },
                        1000: { items: 1 }
                    },
                    onInitialize: function() {
                        console.log('Carousel initialized successfully');
                        // Force show the carousel
                        $carousel.css('opacity', '1');
                    },
                    onInitialized: function() {
                        console.log('Carousel fully initialized');
                        // Hide loading spinner if exists
                        $('.owl-carousel-loading').remove();
                    },
                    onTranslated: function() {
                        console.log('Slide changed');
                    },
                    onResize: function() {
                        console.log('Carousel resized');
                        $carousel.trigger('refresh.owl.carousel');
                    }
                });
                
                // Force refresh after a short delay
                setTimeout(function() {
                    console.log('Refreshing carousel...');
                    $carousel.trigger('refresh.owl.carousel');
                }, 1000);
                
            } catch (e) {
                console.error('Error initializing carousel:', e);
            }
            
        } else {
            console.error('Carousel element not found. Check your HTML structure.');
        }
    });

    // Fallback in case window.onload doesn't fire
    setTimeout(function() {
        if (typeof $('.image-carousel').data('owl.carousel') === 'undefined') {
            console.warn('Carousel not initialized after 3 seconds, attempting to initialize...');
            $('.image-carousel').owlCarousel();
        }
    }, 3000);

})(jQuery);
