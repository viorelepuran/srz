document.addEventListener('DOMContentLoaded', function () {
    // Thumbnail slider
    const thumbnailSlider = new Swiper('.thumbnail-slider', {
        loop: false,
        spaceBetween: 10,
        slidesPerView: 'auto', // Automatically adjusts visible slides based on size
        freeMode: true,        // Allow free scrolling of thumbnails
        watchSlidesProgress: true, // Ensure active thumbnail is highlighted
    });

    // Main slider
    const mainSlider = new Swiper('.main-slider', {
        loop: true,
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        thumbs: {
            swiper: thumbnailSlider, // Link the thumbnail slider
        },
    });

    // Initialize Lightbox for full-screen slideshow
    lightbox.option({
        'resizeDuration': 100,
        'wrapAround': true,
        'disableScrolling': true,
        'alwaysShowNavOnTouchDevices': true
    });
});
