/* globals tify */

"use strict";

import collect from 'collect.js';
import Mustache from 'mustache';

jQuery(document).ready(function ($) {
    let product = tify.wc && tify.wc.product ? tify.wc.product : undefined;

    if (product !== undefined) {
        // Vérification d'une variation sélectionnée.
        let hasSelectedVariation = function () {
                if (product.type !== 'variable') {
                    return false;
                } else {
                    let has = true;
                    $('[name^="attribute_pa_"]').each(function () {
                        if (!$(this).val()) {
                            has = false;
                            return false;
                        }
                    });
                    return has;
                }
            },

            // Récupération des infos produit.
            getInfos = function () {
                let exists = product.infos || [],
                    attributes = {},
                    infos = {exists:!!exists};

                if (product.type === 'variable') {
                    if (hasSelectedVariation()) {
                        $('[name^="attribute_pa_"]').each(function () {
                            attributes[$(this).attr('name')] = $(this).val();
                        });

                        for (let el of exists) {
                            let collection = collect(el.attributes),
                                diff = collection.diffAssoc(attributes);

                            if (!diff.count()) {
                                $.extend(infos, el, {unavailable: false});
                                break;
                            } else {
                                infos.unavailable = true;
                            }
                        }
                    } else {
                        infos.exists = false;
                    }
                } else if (product.type === 'simple') {
                    $.extend(infos, exists);
                }

                return infos;
            },

            // Récupération de la quantité.
            getQuantity = function () {
                return parseInt($('[name="quantity"]').val());
            },

            // Traitement de la liste des arguments.
            parsePriceArgs = function (args) {
                if (args.reset || !args.exists) {
                    args.show_total = false;
                } else if (args.unavailable) {
                    args.show_total = false;
                } else {
                    if (args.pricettc) {
                        args.totalttc = parseFloat((args.qty * args.pricettc).toFixed(2));
                        args.show_total = true;
                    }

                    if (args.priceht) {
                        args.totalht = parseFloat((args.qty * args.priceht).toFixed(2));
                        args.show_total = true;
                    }
                }

                return args;
            },

            // Affichage des tarifs associé au produit.
            displayPrices = function (extend) {
                let template = $('#template').html(),
                    args = parsePriceArgs($.extend(extend || {}, getInfos()));

                $('#default').hide();

                Mustache.parse(template);
                let rendered = Mustache.render(template, args);

                $('#target').html(rendered);
            },

            //
            make = function (args) {
                displayPrices(args);
            };

        // Selection d'un attribut de déclinaison.
        $('select[name^="attribute_pa_"]').change(function () {
            make({qty: getQuantity()});
        });

        // Reinitialisation des variations et de la zone des prix (bouton effacer).
        $('.reset_variations').on('click', function (e) {
            e.preventDefault();

            $('select[name^="attribute_pa_"]').prop('selectedIndex', '');

            make({reset: true});
        });

        // Recalcul du total lors du changement de quantité.
        $('.qty.tiFyField-NumberJs').spinner({
            stop: function () {
                let args = {};

                if ($(this).spinner('isValid')) {
                    args.qty = getQuantity();
                } else {
                    args.reset = true;
                }
                make(args);
            }
        });

        switch (product.type) {
            case 'variable' :
                make({qty: getQuantity()});
                break;
            case 'simple' :
                break;
        }
    }
});