/**
 * Zanvarsity Carousel Initialization
 * This script handles the initialization and configuration of the homepage carousel
 */

// Wait for the document to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Function to initialize carousel
    function initCarousel() {
        if (typeof jQuery === 'undefined' || typeof jQuery.fn.owlCarousel === 'undefined') {
            console.error('jQuery or Owl Carousel not loaded');
            return false;
        }

        var $carousel = jQuery(".image-carousel.owl-carousel");
        
        if ($carousel.length) {
            console.log('Carousel element found, initializing...');
            
            try {
                // Initialize Owl Carousel
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
                        $carousel.css('opacity', '1');
                    },
                    onInitialized: function() {
                        console.log('Carousel fully initialized');
                        jQuery('.owl-carousel-loading').remove();
                    }
                });
                
                // Fix for carousel not showing
                setTimeout(function() {
                    $carousel.trigger('refresh.owl.carousel');
                }, 100);
                
                return true;
            } catch (e) {
                console.error('Error initializing carousel:', e);
                return false;
            }
        } else {
            console.error('Carousel element not found. Check your HTML structure.');
            return false;
        }
    }

    // Function to load required CSS
    function loadCarouselCSS() {
        if (document.querySelector('link[href*="owl.carousel"]') === null) {
            var css1 = document.createElement('link');
            css1.rel = 'stylesheet';
            css1.href = 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css';
            document.head.appendChild(css1);
            
            var css2 = document.createElement('link');
            css2.rel = 'stylesheet';
            css2.href = 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css';
            document.head.appendChild(css2);
        }
    }

    // Main initialization
    function initialize() {
        // Check if jQuery is loaded
        if (typeof jQuery === 'undefined') {
            console.error('jQuery is not loaded');
            return;
        }

        // Load CSS
        loadCarouselCSS();

        // Check if Owl Carousel is loaded
        if (typeof jQuery.fn.owlCarousel === 'undefined') {
            // Load Owl Carousel
            var script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js';
            script.onload = function() {
                console.log('Owl Carousel loaded successfully');
                initCarousel();
            };
            script.onerror = function() {
                console.error('Failed to load Owl Carousel');
            };
            document.body.appendChild(script);
        } else {
            // Owl Carousel already loaded
            initCarousel();
        }
    }

    // Start initialization
    initialize();

    // Fallback in case of any issues
    var attempts = 0;
    var maxAttempts = 5;
    var checkInterval = setInterval(function() {
        attempts++;
        if (typeof jQuery !== 'undefined' && typeof jQuery.fn.owlCarousel !== 'undefined') {
            if (jQuery('.owl-carousel.image-carousel').length > 0) {
                if (typeof jQuery('.owl-carousel.image-carousel').data('owl.carousel') === 'undefined') {
                    console.log('Fallback: Initializing carousel (attempt ' + attempts + ')');
                    initCarousel();
                } else {
                    clearInterval(checkInterval);
                }
            }
        }
        
        if (attempts >= maxAttempts) {
            console.warn('Maximum initialization attempts reached');
            clearInterval(checkInterval);
        }
    }, 1000);
});
