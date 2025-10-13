// Global variables
var _days = 'Days';
var _hours = 'Hours';
var _minutes = 'Minutes';
var _seconds = 'Seconds';
var _messageAfterCount = 'The course has Started!';
var _date = '2025-10-01 00:00:00';

// Document ready function
$(document).ready(function($) {
    "use strict";

    if (location.hash) {
        window.scrollTo(0, 0);
        setTimeout(function() {
            window.scrollTo(0, 0);
        }, 1);
    }

//  Homepage Slider (Flex Slider)
    if ($('.flexslider').length > 0) {
        $('.flexslider').flexslider({
            controlNav: false,
            prevText: "",
            nextText: "",
            start: function() {
                $(window).trigger('resize');
            }
        });
    }
    
    // Initialize Owl Carousel
    if ($('.image-carousel.owl-carousel').length > 0) {
        var $carousel = $('.image-carousel.owl-carousel');
        $carousel.owlCarousel({
            items: 1,
            loop: true,
            nav: true,
            dots: true,
            autoplay: true,
            autoplayTimeout: 5000,
            autoplayHoverPause: true,
            navText: [
                '<i class="fa fa-angle-left" aria-hidden="true"></i>',
                '<i class="fa fa-angle-right" aria-hidden="true"></i>'
            ],
            responsive: {
                0: { items: 1 },
                600: { items: 1 },
                1000: { items: 1 }
            },
            onInitialized: function() {
                console.log('Carousel initialized successfully');
                $carousel.trigger('refresh.owl.carousel');
                
                // Add custom navigation classes
                $carousel.find('.owl-nav').removeClass('disabled');
                $carousel.find('.owl-dots').removeClass('disabled');
            },
            onTranslated: function() {
                console.log('Slide changed');
            }
        });
        
        // Force refresh after a short delay
        setTimeout(function() {
            $carousel.trigger('refresh.owl.carousel');
        }, 1000);
    }

//  Open tab from another page

    $('a[data-toggle="tab"]').on('show.bs.tab', function(e) {});

    $('#tabs a[href=' + location.hash +']').tab('show');

    $('.secondary-navigation li a').on('click',function (e) {
        $('#tabs a[href=' + this.hash +']').tab('show');
    });

//  Table Sorter
    if ($('.tablesorter').length > 0) {
        $(".course-list-table").tablesorter();
    }

//  Rating

    if ($('.rating-individual').length > 0) {
        $('.rating-individual').raty({
            path: 'assets/img',
            readOnly: true,
            score: function() {
                return $(this).attr('data-score');
            }
        });
    }

    if ($('.rating-user').length > 0) {
        $('.rating-user .inner').raty({
            path: 'assets/img',
            starOff : 'big-star-off.png',
            starOn  : 'big-star-on.png',
            width: 180,
            target : '#hint',
            targetType : 'number',
            targetFormat : 'Rating: {score}',
            click: function(score, evt) {
                alert("Your Rating: " + score + "\nThank You!");
            }
        });
    }

//  Checkbox styling

    if ($('.checkbox').length > 0) {
        $('input').iCheck();
    }

// Disable input on count down

    $('.knob').prop("disabled", true);


//  Count Down - Landing Page

    if ($('.count-down').length > 0) {
        $(".count-down").ccountdown(2014,12,24,'18:00');
    }

//  Selectize

    $('select').selectize();

//  Center Slide Vertically

    $('.flexslider').each(function () {
        var slideHeight = $(this).height();
        var contentHeight = $('.flexslider .slides li .slide-wrapper').height();
        var padTop = (slideHeight / 2) - (contentHeight / 2);
        $('.flexslider .slides li .slide-wrapper').css('padding-top', padTop);
    });

//  Slider height on small screens
    if (document.documentElement.clientWidth < 991) {
        $('#landing-page-head-image').css('height', $(window).height());
        $('.flexslider').css('height', $(window).height());
    }

//  Homepage Carousel
    $(document).ready(function() {
        var $carousel = $(".image-carousel.owl-carousel");
        
        if ($carousel.length) {
            console.log('Initializing carousel...');
            
            // Ensure all carousel items have the correct class
            $carousel.find('.item').addClass('owl-item');
            
            // Initialize Owl Carousel with single item display
            $carousel.owlCarousel({
                items: 1,
                loop: true,
                margin: 0,
                nav: true,
                dots: false, // Hide dots as per previous request
                autoplay: true,
                autoplayTimeout: 5000,
                autoplayHoverPause: true,
                smartSpeed: 800,
                navText: [
                    '<i class="fa fa-angle-left" aria-hidden="true"></i>',
                    '<i class="fa fa-angle-right" aria-hidden="true"></i>'
                ],
                responsive: {
                    0: { items: 1 },
                    600: { items: 1 },
                    1000: { items: 1 }
                },
                onInitialized: function() {
                    console.log('Carousel initialized successfully');
                    $carousel.trigger('refresh.owl.carousel');
                    
                    // Add custom navigation classes
                    $carousel.find('.owl-nav').removeClass('disabled');
                    $carousel.find('.owl-dots').removeClass('disabled');
                },
                onTranslated: function() {
                    console.log('Slide changed');
                }
            });
            
            // Force refresh after a short delay
            setTimeout(function() {
                $carousel.trigger('refresh.owl.carousel');
            }, 1000);
        } else {
            console.warn('Carousel element not found. Selector used: ".image-carousel.owl-carousel"');
            console.log('Available elements with class "image-carousel":', $('.image-carousel').length);
            console.log('Available elements with class "owl-carousel":', $('.owl-carousel').length);
        }
    });

    //  Smooth Scroll

    $('.navigation-wrapper .nav a[href^="#"], a[href^="#"].roll').on('click',function (e) {
        e.preventDefault();
        var target = this.hash,
            $target = $(target);
        $('html, body').stop().animate({
            'scrollTop': $target.offset().top
        }, 2000, 'swing', function () {
            window.location.hash = target;
        });
    });

//  Fixed Navigation After Scroll

//    if (document.documentElement.clientWidth > 768) {
//        $(window).scroll(function () {
//            if ($(window).scrollTop() > 50) {
//                $('.page-landing-page .primary-navigation-wrapper').addClass('navigation-fixed');
//            } else {
//                $('.page-landing-page .primary-navigation-wrapper').removeClass('navigation-fixed');
//            }
//        });
//    }


//  author Carousel (Owl Carousel)

    $(".author-carousel").owlCarousel({
        items: 1,
        autoPlay: false,
        stopOnHover: true,
        responsiveBaseWidth: ".author"
    });

//  Equal Rows

    if(document.documentElement.clientWidth > 991) {
        $('.row').equalHeights();
    }

    $( document.body ).on( 'click', '.dropdown-menu li', function( event ) {
        var $target = $( event.currentTarget );
        $target.closest( '.btn-group' )
            .find( '[data-bind="label"]' ).text( $target.text() )
            .end()
            .children( '.dropdown-toggle' ).dropdown( 'toggle' );
        return false;
    });

//  Slider Subscription Form

    $("#slider-submit").bind("click", function(event){
        $("#slider-form").validate({
            submitHandler: function() {
                $.post("slider-form.php", $("#slider-form").serialize(),  function(response) {
                    $('#form-status').html(response);
                    $('#submit').attr('disabled','true');
                });
                return false;
            }
        });
    });

//  Contact Form with validation

    $("#submit").bind("click", function(event){
        $("#contactform").validate({
            submitHandler: function() {
                $.post("contact.php", $("#contactform").serialize(),  function(response) {
                    $('#form-status').html(response);
                    $('#submit').attr('disabled','true');
                });
                return false;
            }
        });
    });

//  Landing Page Form

    $("#landing-page-submit").bind("click", function(event){
        $("#form-landing-page").validate({
            submitHandler: function() {
                $.post("landing-page-form.php", $("#form-landing-page").serialize(),  function(response) {
                    $('#form-status').html(response);
                    $('#submit').attr('disabled','true');
                });
                return false;
            }
        });
    });

//  Vanilla Box

    if ($('.image-popup').length > 0) {
        $('a.image-popup').vanillabox({
            animation: 'default',
            type: 'image',
            closeButton: true,
            repositionOnScroll: true
        });
    }

//  Calendar

    if ($('.calendar').length > 0) {
        $('.calendar').fullCalendar({
            firstDay: 1,
            weekMode: 'variable',
            contentHeight: 700,
            header: {
                right: 'month,basicWeek,basicDay prev,next'
            },

            events: "events.php"

        });
    }

    $('.fc-view-month .fc-event-title').each(function(){
        $(this).text($(this).text().substring(0,25));
    });

// Remove button function for "join to course" button after count down is over
function disableJoin() {
    // Find "join to course" button
    var buttonToBeRemoved = document.getElementById("btn-course-join");
    // Find "join to course" button on bottom of course detail
    var buttonToBeRemovedBottom = document.getElementById("btn-course-join-bottom");
    // Remove button if it exists
    if (buttonToBeRemoved) buttonToBeRemoved.remove();
    // Remove button on the bottom if it exists
    if (buttonToBeRemovedBottom) buttonToBeRemovedBottom.remove();
    // Update classes for countdown elements
    var courseCountDown = document.getElementById("course-count-down");
    var courseStart = document.getElementById("course-start");
    if (courseCountDown) courseCountDown.classList.add("disable-join");
    if (courseStart) courseStart.classList.add("disable-join");
};

// Count Down - Course Detail
if (typeof _date != 'undefined' && typeof Countdown !== 'undefined') {
    var countdown = new Countdown({
        dateEnd: new Date(_date),
        msgAfter: _messageAfterCount,
        onEnd: function() {
            disableJoin(); // Run this function after count down is over
        }
    });
}