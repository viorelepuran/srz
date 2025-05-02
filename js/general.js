(function( $ ){
    $(document).on("ready", function() { $(".wp-block-navigation-submenu__toggle").on("click", function() { var li = $(this).parent(); if ( li.hasClass("ddl-active") || li.find(".ddl-active").length !== 0 || li.find(".wp-block-navigation-submenu").is(":visible") ) { li.removeClass("ddl-active"); li.children().find(".ddl-active").removeClass("ddl-active"); li.children(".wp-block-navigation-submenu").slideUp(); } else { li.addClass("ddl-active"); li.children(".wp-block-navigation-submenu").slideDown(); } }); });
})( jQuery );
let lastScrollTop = 0;
let hasScrolledPast100 = false;

window.addEventListener("scroll", function () {
    let element = document.querySelector(".mainHeader");
    let currentScroll = window.scrollY;
    
    console.log(currentScroll);

    // Check if user has scrolled past 100px once
    if (currentScroll > 100) {
        hasScrolledPast100 = true;
    } else {
        hasScrolledPast100 = false; // Reset when back at the top
        element.classList.remove("fixedMh"); // Ensure class is removed
    }

    // If scrolled more than 100px and scrolling down, remove class
    if (hasScrolledPast100 && currentScroll > lastScrollTop) {
        element.classList.remove("fixedMh");
    }

    // If scrolling up by at least 10px and already past 100px, add class
    if (hasScrolledPast100 && lastScrollTop - currentScroll > 10) {
        element.classList.add("fixedMh");
    }

    lastScrollTop = currentScroll; // Update last scroll position
});



document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.carousel-slider').forEach(slider => {
        const slidesToShow = parseInt(slider.getAttribute('data-slides')) || 3;
        const showNav = slider.getAttribute('data-nav') === 'true';
        const showPag = slider.getAttribute('data-pag') === 'true';

        new Swiper(slider, {
            loop: true,
            slidesPerView: slidesToShow,
            spaceBetween: 10,
            navigation: showNav ? {
                nextEl: slider.querySelector('.swiper-button-next'),
                prevEl: slider.querySelector('.swiper-button-prev'),
            } : false,
            pagination: showPag ? {
                el: slider.querySelector('.swiper-pagination'),
                clickable: true,
            } : false,
            breakpoints: {
                320: { // mobile
                    slidesPerView: 1,
                    spaceBetween: 10,
                },
                640: { // small tablets
                    slidesPerView: Math.max(1, slidesToShow / 2),
                    spaceBetween: 10,
                },
                1024: { // desktop
                    slidesPerView: slidesToShow,
                    spaceBetween: 10,
                }
            }
        });
    });
});