jQuery(function($) {
    // Upload image
    $('.upload_plat_map_image').on('click', function(e) {
        e.preventDefault();
        const frame = wp.media({
            title: 'Select or Upload plat Map Image',
            button: { text: 'Use this image' },
            multiple: false
        });

        frame.on('select', function() {
            const attachment = frame.state().get('selection').first().toJSON();
            $('#plat_map_image_id').val(attachment.id);
            $('#plat_map_image_preview').html(`<img src="${attachment.url}" style="max-width:100%; height:auto;" />`);
        });

        frame.open();
    });

    // Live preview pins
    $(document).on('input', '.coord-input', function() {
        renderPins();
    });

    function renderPins() {
        const image = $('#plat_map_image_preview img');
        let container = $('#plat_map_image_preview .plat-map-pin-preview-layer');
    
        if (image.length === 0) return;
    
        // Ensure the pin layer is inside the image preview container
        if (container.length === 0) {
            container = $('<div class="plat-map-pin-preview-layer"></div>');
            $('#plat_map_image_preview').append(container);
        } else {
            container.empty();
        }
    
        $('table.widefat tbody tr').each(function() {
            const $row = $(this);
            const x = parseFloat($row.find('input[name*="[x]"]').val());
            const y = parseFloat($row.find('input[name*="[y]"]').val());
            const title = $row.data('title');
            const status = $row.data('status');
    
            if (!isNaN(x) && !isNaN(y)) {
                const statusIcons = {
                    'build-your-dream': 'red.svg',
                    'make-it-yours': 'blue.svg',
                    'coming-soon': 'orange.svg',
                    'move-in-ready': 'green.svg',
                };
    
                // Assuming 'property_status' is stored as the value, we match it to the status icons
                const iconFile = statusIcons[status] || 'default.svg'; // Default fallback
                const iconPath = platMapData.plugin_url + 'assets/images/status-icons/' + iconFile;
    
                const $pin = $('<div class="plat-map-pin" data-title="' + title + '"></div>');
                $pin.css({
                    left: x + '%',
                    top: y + '%'
                }).append('<img src="' + iconPath + '" alt="' + status + '">');
                
                container.append($pin);
            }
        });
    }

    // Property Title on Hover as Tooltip
    $(document).on('mouseenter', '.plat-map-pin', function(e) {
        const title = $(this).data('title');
        const $tooltip = $('<div class="plat-map-tooltip"></div>').text(title);
    
        $('body').append($tooltip);
    
        $tooltip.css({
            position: 'absolute',
            top: e.pageY + 10,
            left: e.pageX + 10,
            display: 'none'
        }).fadeIn(200);
    });
    
    $(document).on('mousemove', '.plat-map-pin', function(e) {
        $('.plat-map-tooltip').css({
            top: e.pageY + 10,
            left: e.pageX + 10
        });
    });
    
    $(document).on('mouseleave', '.plat-map-pin', function() {
        $('.plat-map-tooltip').remove();
    });
    
    // Trigger on load & on input change
    $(document).on('input', '.coord-input', renderPins);
    $(window).on('load', function () {
        setTimeout(renderPins, 300); // delay until image is loaded
    });
    
});
