jQuery(document).ready(function ($) {
    let mediaUploaders = {};

    // Function to initialize an uploader
    function initializeUploader(buttonId, previewContainer, inputField) {
        $(buttonId).click(function (e) {
            e.preventDefault();

            if (mediaUploaders[buttonId]) {
                mediaUploaders[buttonId].open();
                return;
            }

            mediaUploaders[buttonId] = wp.media({
                title: 'Select Images',
                button: { text: 'Use These Images' },
                multiple: true
            });

            mediaUploaders[buttonId].on('select', function () {
                const attachments = mediaUploaders[buttonId].state().get('selection').toJSON();
                attachments.forEach(function (attachment) {
                    addImageToPreview(previewContainer, inputField, attachment.id, attachment.url);
                });
            });

            mediaUploaders[buttonId].open();
        });
    }

    // Add image to preview container
    function addImageToPreview(previewContainer, inputField, id, url) {
        const item = `
            <div class="item" data-id="${id}">
                <img src="${url}"/>
                <button class="remove-image" style="position: absolute; top: 5px; right: 5px; background: red; color: white; border: none; cursor: pointer;">X</button>
            </div>`;
        $(previewContainer).find('.upload-placeholder').before(item); // Add before the placeholder
        updateInputIds(previewContainer, inputField);
    }

    // Update hidden input with IDs
    function updateInputIds(previewContainer, inputField) {
        const ids = [];
        $(previewContainer).find('.item').each(function () {
            ids.push($(this).data('id'));
        });
        $(inputField).val(ids.join(','));
    }

    // Enable remove button functionality
    $(document).on('click', '.remove-image', function () {
        const container = $(this).closest('.grid-container');
        const inputField = container.data('input-field');
        $(this).closest('.item').remove();
        updateInputIds(container, inputField);
    });

    // Enable sortable feature
    $('.grid-container').sortable({
        items: '.item',
        stop: function (event, ui) {
            const container = $(this);
            const inputField = container.data('input-field');
            updateInputIds(container, inputField);
        }
    });

    // Clear preview container
    function clearPreview(previewContainer, inputField) {
        $(previewContainer).find('.item').remove(); // Remove only items, not placeholder
        updateInputIds(previewContainer, inputField);
    }

    // Prepopulate galleries
    function preloadImages(previewContainer, inputField, ids) {
        if (!ids || !Array.isArray(ids)) return;

        ids.forEach(function (id) {
            // Fetch the image URL via AJAX or REST API
            wp.media.attachment(id).fetch().then(function (attachment) {
                addImageToPreview(previewContainer, inputField, id, attachment.url);
            });
        });
    }

    // Initialize functionality for Gallery
    initializeUploader('#upload-gallery-button', '#gallery-preview', '#gallery-ids');
    $('#clear-gallery-button').click(function () {
        clearPreview('#gallery-preview', '#gallery-ids');
    });

    // Prepopulate Gallery images 
    preloadImages('#gallery-preview', '#gallery-ids', typeof initialGalleryImages !== 'undefined' ? initialGalleryImages : []);


    // Initialize functionality for Schita
    initializeUploader('#upload-schita-button', '#schita-preview', '#schita-ids');
    $('#clear-schita-button').click(function () {
        clearPreview('#schita-preview', '#schita-ids');
    });

    // Prepopulate Schita images
    preloadImages('#schita-preview', '#schita-ids', typeof initialSchitaImages !== 'undefined' ? initialSchitaImages : []);



    // Function to extract YouTube Video ID from URL
    function extractYouTubeId(url) {
        const regex = /(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/;
        const match = url.match(regex);
        return match ? match[1] : null;
    }

    // Prepopulate YouTube URL and preview
if (typeof initialVideoUrl !== 'undefined' && initialVideoUrl) {
    $('#youtube-url').val(initialVideoUrl);
    const youtubeId = extractYouTubeId(initialVideoUrl);
    if (youtubeId) {
        const embedUrl = `https://www.youtube.com/embed/${youtubeId}`;
        $('#youtube-iframe').attr('src', embedUrl);
        $('#youtube-preview').show();
    }
}


    // Handle YouTube Preview Button Click
    $('#preview-youtube-button').click(function () {
        const url = $('#youtube-url').val().trim();
        const youtubeId = extractYouTubeId(url);

        if (youtubeId) {
            const embedUrl = `https://www.youtube.com/embed/${youtubeId}`;
            $('#youtube-iframe').attr('src', embedUrl);
            $('#youtube-preview').show();
        } else {
            alert('Introduceți un URL valid YouTube.');
        }
    });
});