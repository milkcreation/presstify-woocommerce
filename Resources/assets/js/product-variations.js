"use strict";

import collect from 'collect.js';
import Mustache from 'mustache';

jQuery(document).ready(function ($) {
    let getVariation = function () {
            let pv = tify.wc_product_variations || [],
                attributes = {},
                variation = {};

            $('[name^="attribute_pa_"]').each(function () {
                attributes[$(this).attr('name')] = ($(this).val());
            });

            for (let el of pv) {
                let collection = collect(el.attributes),
                    diff = collection.diffAssoc(attributes);
                if (!diff.count()) {
                    variation = el;
                    break;
                }
            }

            return variation;
        },
        getQuantity = function () {
            return parseInt($('[name="quantity"]').val());
        },
        parseVariables = function (v) {
            if (v.qty >= 2) {
                if (v.price2ttc) {
                    v.totalttc = parseFloat((v.qty * v.price2ttc).toFixed(2));
                    v.show_total = true;
                }

                if (v.price2ht) {
                    v.totalht = parseFloat((v.qty * v.price2ht).toFixed(2));
                    v.show_total = true;
                }

                v.multi = true;
            } else {
                if (v.pricettc) {
                    v.totalttc = parseFloat((v.qty * v.pricettc).toFixed(2));
                    v.show_total = true;
                }

                if (v.priceht) {
                    v.totalht = parseFloat((v.qty * v.priceht).toFixed(2));
                    v.show_total = true;
                }

                v.multi = false;
            }

            return v;
        },
        displayVariables = function (extend) {
            let variations = getVariation(),
                template = $('#template').html(),
                variables = parseVariables($.extend(extend || {}, variations));

            Mustache.parse(template);
            var rendered = Mustache.render(template, variables);
            $('#target').html(rendered);
        };

    $('select[name^="attribute_pa_"]').change(function () {
        let extend = {};

        extend.qty = getQuantity();

        displayVariables(extend);
    });

    $(document).on('click', 'a#cart-refresh', function (e) {
        e.preventDefault();
        let extend = {};

        extend.qty = getQuantity();

        displayVariables(extend);
    });

    $('[name^="attribute_pa_"]').each(function () {
        if ($(this).val()) {
            let extend = {};

            extend.qty = getQuantity();

            displayVariables(extend);
            return false;
        }
    });

    //Reset des variations et de la zone des prix
    $('.reset_variations').click(function(e){
        e.preventDefault();
        $('select[name^="attribute_pa_"]').prop('selectedIndex','');

        let extend = {};
        extend.qty = getQuantity();
        displayVariables(extend);
    });
});