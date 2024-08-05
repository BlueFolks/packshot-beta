$(document).ready(function() {
    // Toggle user options menu
    $('#profileBtn').click(function() {
        $('#userOptions').toggle();
    });

    // Show popup for login and register
    $('#registerBtn, #loginBtn').click(function() {
        let targetPopup = $(this).attr('id') === 'registerBtn' ? '#registerPopup' : '#loginPopup';
        $(targetPopup).fadeIn();
    });

    // Close popups
    $('.close-popup').click(function() {
        $(this).closest('.popup').fadeOut();
    });

    // Show preview popup
    $('.preview-link').click(function() {
        let modelId = $(this).closest('.model-item').data('id');
        // AJAX call to get preview data
        $.ajax({
            url: 'fetch_preview.php',
            method: 'GET',
            data: { id: modelId },
            dataType: 'json',
            success: function(response) {
                const previewSlider = $('#previewPopup .preview-slider');
                previewSlider.slick('unslick'); // Remove slick if initialized
                previewSlider.html(''); // Clear existing slides

                response.previews.forEach(function(preview) {
                    previewSlider.append('<div class="preview-item"><img src="' + preview.preview_url + '" alt="Prévia" class="img-fluid"></div>');
                });

                previewSlider.slick({
                    infinite: true,
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    arrows: true
                });

                $('#previewPopup .photos-count').text(response.photos_count);
                $('#previewPopup .videos-count').text(response.videos_count);
                $('#previewPopup').fadeIn();
            },
            error: function() {
                alert('Erro ao carregar as prévias. Tente novamente.');
            }
        });
    });

    // Toggle filter section
    $('#filterBtn').click(function() {
        $('.filter-tags').toggle();
    });

    // Order models
    $('.order-btn').click(function() {
        let order = $(this).data('order');
        $('.order-btn').removeClass('active');
        $(this).addClass('active');
        $('<form>', {
            "html": '<input type="hidden" name="order" value="' + order + '">',
            "action": "index.php",
            "method": "post"
        }).appendTo(document.body).submit();
    });

    // Filter by tag
    $('.filter-tag').click(function() {
        let tag = $(this).data('tag');
        $('<form>', {
            "html": '<input type="hidden" name="tag" value="' + tag + '">',
            "action": "index.php",
            "method": "post"
        }).appendTo(document.body).submit();
    });

    // Edit and delete buttons functionality
    $('.edit-btn').click(function() {
        let modelId = $(this).closest('.model-item').data('id');
        // Implement edit functionality
        window.location.href = `edit_model.php?id=${modelId}`;
    });

    $('.delete-btn').click(function() {
        let modelId = $(this).closest('.model-item').data('id');
        if (confirm('Tem certeza que deseja excluir este modelo?')) {
            // AJAX call to delete model
            $.ajax({
                url: 'delete_model.php',
                method: 'POST',
                data: { id: modelId },
                success: function() {
                    location.reload();
                },
                error: function() {
                    alert('Erro ao excluir o modelo. Tente novamente.');
                }
            });
        }
    });
});
