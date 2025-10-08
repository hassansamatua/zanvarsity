<!-- Scroll to Top Button -->
<a href="#" class="scroll-to-top" style="
    position: fixed;
    bottom: 30px;
    right: 30px;
    background-color: #004225;
    color: white;
    width: 45px;
    height: 45px;
    border-radius: 50%;
    text-align: center;
    line-height: 45px;
    font-size: 20px;
    cursor: pointer;
    display: none;
    z-index: 9999;
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
    opacity: 0.9;
">
    <i class="fa fa-arrow-up"></i>
</a>

<script>
// Enhanced smooth scroll to top with requestAnimationFrame for better performance
document.addEventListener('DOMContentLoaded', function() {
    const scrollToTopBtn = document.querySelector('.scroll-to-top');
    
    // Show/hide button on scroll with throttle for better performance
    let isScrolling;
    window.addEventListener('scroll', function() {
        // Clear our timeout throughout the scroll
        window.clearTimeout(isScrolling);
        
        // Set a timeout to run after scrolling ends
        isScrolling = setTimeout(function() {
            if (window.pageYOffset > 300) {
                scrollToTopBtn.style.display = 'block';
                setTimeout(() => { scrollToTopBtn.style.opacity = '0.9'; }, 10);
            } else {
                scrollToTopBtn.style.opacity = '0';
                setTimeout(() => { scrollToTopBtn.style.display = 'none'; }, 300);
            }
        }, 100);
    }, false);

    // Enhanced smooth scroll to top with slower animation
    scrollToTopBtn.addEventListener('click', function(e) {
        e.preventDefault();
        
        // For modern browsers
        if ('scrollBehavior' in document.documentElement.style) {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
            return;
        }
        
        // For older browsers with slower animation
        const scrollToTop = () => {
            const currentPosition = document.documentElement.scrollTop || document.body.scrollTop;
            if (currentPosition > 0) {
                // Slower scroll by reducing the distance moved each frame
                window.requestAnimationFrame(scrollToTop);
                // Slower easing function (smaller number = slower scroll)
                window.scrollTo(0, currentPosition - currentPosition / 15);
            }
        };
        scrollToTop();
    });
    
    // Initial state
    if (window.pageYOffset > 300) {
        scrollToTopBtn.style.display = 'block';
        scrollToTopBtn.style.opacity = '0.9';
    }
});
</script>
