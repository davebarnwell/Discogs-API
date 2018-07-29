var APP = (function ($) {

    function handleFilterByAlbumFormat(e) {
        e.preventDefault();
        $('.album-formats-nav .album-formats-nav__item').removeClass('album-formats-nav__item_selected');
        $(this).addClass('album-formats-nav__item_selected');
        var format = $(this).data('format');
        if (format == '*') {
            $('.album-list-instance').show();
        } else {
            $('.album-list-instance[data-format=' + format + ']').show();
            $('.album-list-instance:not([data-format=' + format + '])').hide();
        }
    }

    function handleFilterByText(e) {
        if ($('.album-formats-nav__item_selected').data('format') != '*') {
            // for text filtering show all to start
            $('.album-formats-nav .album-formats-nav__item').removeClass('album-formats-nav__item_selected');
            $('.album-list-instance:not([data-format=' * '])').addClass('album-formats-nav__item_selected');
            $('.album-list-instance').show();
        }
        var text = $(this).val();
        if (text == '') {
            $('.album-list-instance').show();
        } else {
            $('.album-list-instance').each(function(i,e){
                if (($(this).data('title')+'').indexOf(text) != -1 || ($(this).data('artist')+'').indexOf(text) != -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
            // $('.album-list-instance[data-title*=' + text.toLowerCase() + '], .album-list-instance[data-artist*=' + text.toLowerCase() + ']').show();
            // $('.album-list-instance:not([data-title*=' + text.toLowerCase() + '])').hide();
        }
    }

    function debounce(func, wait, immediate) {
        var timeout;
        return function () {
            var context = this, args = arguments;
            var later = function () {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            var callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    }

    return {
        init: function () {
            $('.album-formats-nav').on('click', '.album-formats-nav__item', handleFilterByAlbumFormat);
            $('.album-search__input').keyup(debounce(handleFilterByText, 100));
        }
    }
})(jQuery);

$(document).ready(function () {
    APP.init();
});