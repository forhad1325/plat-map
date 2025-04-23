jQuery(function($) {
    $(document).on('mouseenter', '.plat-map-pin', function(e) {
        const title = $(this).data('title');
        const $tooltip = $('<div class="plat-map-tooltip"></div>').text(title);
        $('body').append($tooltip);
        $tooltip.css({
            top: e.pageY + 10,
            left: e.pageX + 10
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
});