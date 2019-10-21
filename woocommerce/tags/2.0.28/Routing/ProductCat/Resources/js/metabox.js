"use strict";

jQuery(document).ready(function ($) {
    let changePostPermalink = function (termId, $container) {
        let $newPostSlug = $('#new-post-slug');
        $.post(
            tify.ajax_url,
            {
                _ajax_nonce: $container.data('ajax_nonce'),
                action: 'theme_metabox_item_change_permalink',
                postTitle: $newPostSlug.length ? $newPostSlug.val() : $('#editable-post-name').text(),
                routeName: $container.data('route_name'),
                taxName: $container.data('tax_name'),
                termId: termId
            },
            function (data) {
                $('.PostTerms-url', $container).val(data);
            }
        );
    };

    $(document).on('change', '.PostTerms-items--all input[type="checkbox"]', function () {
        let $container = $(this).closest('.PostTerms');
        if (!$(this).is(':checked') && $('.PostTerms-items--main input[type="radio"][value="' + $(this).val() + '"]:checked', $container).length) {
            $('.PostTerms-item--none input[type="radio"]', $container).prop('checked', true);
            changePostPermalink(0, $container);
        }
    });

    $(document).on('change', '.PostTerms-items--main input[type="radio"]', function () {
        let $container = $(this).closest('.PostTerms'),
            $checkbox = $('.PostTerms-items--all input[type="checkbox"][value="' + $(this).val() + '"]', $container);

        if (!$checkbox.is(':checked')) {
            $checkbox.prop('checked', true);
        }

        changePostPermalink($(this).val(), $container);
    });
});