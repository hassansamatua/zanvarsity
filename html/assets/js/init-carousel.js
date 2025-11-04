/**
 * Initialize Owl Carousel for the homepage slider
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        // Initialize carousel
        initCarousel();
        
        // Reinitialize on window resize
        var resizeTimer;
        $(window).on('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                $('.image-carousel.owl-carousel').trigger('destroy.owl.carousel');
                initCarousel();
            }, 250);
        });
    });

    function initCarousel() {
        try {
            if (typeof $.fn.owlCarousel === 'undefined') {
                console.error('Owl Carousel is not loaded');
                return;
            }

            $('.image-carousel.owl-carousel').owlCarousel({
                items: 1,
                loop: true,
                margin: 0,
                nav: true,
                dots: true,
                autoplay: true,
                autoplayTimeout: 5000,
                autoplayHoverPause: true,
                smartSpeed: 800,
                navText: [
                    '<i class="fa fa-chevron-left"></i>',
                    '<i class="fa fa-chevron-right"></i>'
                ],
                responsive: {
                    0: {
                        items: 1,
                        nav: false
                    },
                    600: {
                        items: 1,
                        nav: true
                    }
                }
            });
            
            console.log('Carousel initialized successfully');
        } catch (error) {
            console.error('Error initializing carousel:', error);
        }
    }

})(jQuery);
