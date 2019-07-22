<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Form;

use tiFy\Plugins\Woocommerce\{Contracts\Form as FormContract, WoocommerceAwareTrait};
use tiFy\Support\ParamsBag;
use WooCommerce;
use WC_Customer;

/**
 * @see Woocommerce/includes/wc-template-functions.php
 * @see https://docs.woocommerce.com/document/tutorial-customising-checkout-fields-using-actions-and-filters/
 */
class Form extends ParamsBag implements FormContract
{
    use WoocommerceAwareTrait;

    /**
     * CONSTRUCTEUR.
     *
     * @retun void
     */
    public function __construct()
    {
        // Définition du type de champ "Sélecteur JS".
        add_filter('woocommerce_form_field_select_js', function ($field, $key, $args, $value) {
            $field_type = 'select-js';

            $args['attrs'] = array_merge([
                'class' => esc_attr(implode(' ', $args['input_class']))
            ], $args['attrs'] ?? []);

            $args['picker'] = array_merge([
                'class' => isset($args['picker_class']) ? esc_attr(implode(' ', $args['picker_class'])) : null
            ], $args['picker'] ?? []);

            $args['choices'] = $args['options'] ?? [];

            return (string)$this->manager->viewer('field/field', compact('args', 'key', 'value', 'field_type'));
        }, 10, 4);

        // Définition du type de champ "Sélecteur JS de pays".
        add_filter('woocommerce_form_field_select_js_country', function ($field, $key, $args, $value) {
            $countries = 'shipping_country' === $key
                ? WC()->countries->get_shipping_countries() : WC()->countries->get_allowed_countries();

            $singleCountry = (1 === sizeof($countries));

            $field_type = $singleCountry ? 'single-country' : 'select-js-country';

            if (!$singleCountry) {
                $args['attrs'] = array_merge([
                    'class' => 'country_to_state country_select ' . esc_attr(implode(' ', $args['input_class']))
                ], $args['attrs'] ?? []);

                $args['picker'] = array_merge([
                    'class' => 'country_to_state country_select_picker ' . (isset($args['picker_class'])
                            ? esc_attr(implode(' ', $args['picker_class'])) : null)
                ], $args['picker'] ?? []);

                $countries = ['' => esc_html__('Select a country&hellip;', 'woocommerce')] + $countries;

                $args['attrs'] = array_merge($args['attrs'], $args['custom_attributes']);
            } else {
                $args['custom_attributes'] = $this->getCustomHtmlAttrs($args['custom_attributes']);
            }

            $args['choices'] = $countries;

            return (string)$this->manager->viewer('field/field', compact('args', 'key', 'value', 'field_type'));
        }, 10, 4);

        // Définition du type de champ "Sélecteur JS d'état/comté".
        add_filter('woocommerce_form_field_select_js_state', function ($field, $key, $args, $value) {
            $for_country = isset($args['country'])
                ? $args['country']
                : WC()->checkout->get_value('billing_state' === $key ? 'billing_country' : 'shipping_country');

            $states = WC()->countries->get_states($for_country);

            if (is_array($states) && empty($states)) {
                $field_type = 'empty-state';
                $args['custom_attributes'] = $this->getCustomHtmlAttrs($args['custom_attributes']);
                $args['inline_style'] = 'style="display:none;"';
            } elseif (!is_null($for_country) && is_array($states)) {
                $field_type = 'select-js';

                $args['attrs'] = array_merge([
                    'class' => 'state_select ' . esc_attr(implode(' ', $args['input_class']))
                ], $args['attrs'] ?? []);

                $args['picker'] = array_merge([
                    'class' => 'state_select_picker ' .
                        (isset($args['picker_class']) ? esc_attr(implode(' ', $args['picker_class'])) : null)
                ], $args['picker'] ?? []);

                $args['choices'] = ['' => esc_html__('Select a state&hellip;', 'woocommerce')] + $states;
                $args['attrs'] = array_merge($args['attrs'], $args['custom_attributes']);
            } else {
                $field_type = 'text-state';
                $args['custom_attributes'] = $this->getCustomHtmlAttrs($args['custom_attributes']);
            }

            return (string)$this->manager->viewer('field/field', compact('args', 'key', 'value', 'field_type'));
        }, 10, 4);

        // Ajout de champs personnalisés aux formulaires d'adresse de facturation et de livraison.
        // @see woocommerce_form_field()
        add_filter('woocommerce_default_address_fields', function (array $fields) {
            foreach ($this->get('add_address_fields', []) as $field => $attrs) {
                if (!isset($fields[$field])) {
                    $fields[$field] = $attrs;
                }
            }
            return $fields;
        });
        add_filter('woocommerce_customer_meta_fields', function (array $fields) {
            foreach ($this->get('add_address_fields', []) as $slug => $attrs) {
                if (!empty($attrs['admin'])) {
                    foreach ($attrs['admin'] as $addressType => $adminAttrs) {
                        $_slug = "{$addressType}_{$slug}";

                        if (!$addressType || isset($fields[$addressType]['fields'][$_slug]) || !$adminAttrs) {
                            continue;
                        } elseif (!empty($adminAttrs['before'])) {
                            $pos = array_search("{$addressType}_{$adminAttrs['before']}",
                                array_keys($fields[$addressType]['fields']));
                            $fields[$addressType]['fields'] = array_merge(
                                array_slice($fields[$addressType]['fields'], 0, $pos),
                                [
                                    $_slug => array_merge(
                                        [
                                            'label'       => isset($attrs['label']) ? $attrs['label'] : '',
                                            'description' => '',
                                            'class'       => ''
                                        ],
                                        $adminAttrs
                                    )
                                ],
                                array_slice($fields[$addressType]['fields'], $pos)
                            );
                        } else {
                            $fields[$addressType]['fields'][$_slug] = $adminAttrs;
                        }
                    }
                }
            }
            return $fields;
        });

        // Surcharge des champs de formulaire d'adresse de facturation.
        // @see woocommerce_form_field()
        add_filter("woocommerce_billing_fields", function (array $originalFields){
            if ($customfields = $this->get('billing', [])) {
                return $this->overwriteFormFields('billing', $originalFields, $customfields);
            }
            return $originalFields;
        });

        // Surcharge des champs de formulaire d'adresse de livraison.
        // @see woocommerce_form_field()
        add_filter("woocommerce_shipping_fields", function (array $originalFields){
            if ($customfields = $this->get('shipping', [])) {
                return $this->overwriteFormFields('shipping', $originalFields, $customfields);
            }
            return $originalFields;
        });

        // Surcharge des champs des différents formulaires au niveau du processus commande.
        add_filter('woocommerce_checkout_fields', function (array $originalFields) {
            foreach ($this->get('checkout', []) as $type => $customfields) {
                $originalFields[$type] = $this->overwriteFormFields($type, $originalFields[$type], $customfields);
            }
            return $originalFields;
        });

        // Court-circuitage des attributs de champ de formulaire.
        add_filter('woocommerce_form_field_args', function ($args, $key, $value) {
            return method_exists($this, 'form_field_args_' . $key)
                ? call_user_func([$this, 'form_field_args_' . $key], $args, $value)
                : call_user_func([$this, 'form_fields_args'], $args, $key, $value);
        }, 10, 3);

        // Sauvegarde des adresses de facturation et livraison.
        add_action(
            'woocommerce_after_save_address_validation',
            function (int $user_id, string $load_address, array $address, WC_Customer $customer) {
                foreach ($address as $key => $field) {
                    if (!isset($field['type'])) {
                        $field['type'] = 'text';
                    }
                    switch ($field['type']) {
                        case 'checkbox' :
                            $_POST[$key] = (int)isset($_POST[$key]);
                            break;
                        default :
                            $_POST[$key] = isset($_POST[$key]) ? wc_clean(wp_unslash($_POST[$key])) : '';
                            break;
                    }
                }
        }, 10, 3);

        $this->boot();
    }

    /**
     * @inheritDoc
     */
    public function boot(): void {}

    /**
     * @inheritDoc
     */
    public function form_fields_args(array $args, string $key, $value): array
    {
        return $args;
    }

    /**
     * @inheritDoc
     */
    public function form_field_args_billing_first_name(array $args, $value): array
    {
        return $this->form_fields_args($args, 'billing_first_name', $value);
    }

    /**
     * @inheritDoc
     */
    public function getCustomHtmlAttrs(array $customAttrs): string
    {
        $custom_attributes = [];

        if (!empty($customAttrs) && is_array($customAttrs)) {
            foreach ($customAttrs as $attribute => $attribute_value) {
                $custom_attributes[] = esc_attr($attribute) . '="' . esc_attr($attribute_value) . '"';
            }
        }

        return implode(' ', $custom_attributes);
    }

    /**
     * @inheritDoc
     */
    public function hasSelectJsField($haystack): bool
    {
        foreach ($haystack as $slug => $attrs) {
            if (is_array($attrs) && $this->hasSelectJsField($attrs)) {
                return true;
            } elseif (($slug === 'type') && in_array($attrs, ['select_js', 'select_js_country'])) {
                return true;
            }
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function isSelectJsEnabled(): bool
    {
        foreach (['add_address_fields', 'billing', 'shipping', 'checkout'] as $form) {
            if ($this->hasSelectJsField($this->get($form, []))) {
                return true;
            }
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function overwriteFormFields(string $formId, array $originalFields, array $customFields): array
    {
        foreach ($customFields as $slug => $attrs) {
            if (isset($originalFields["{$formId}_{$slug}"])) {
                if ($attrs === false) {
                    unset($originalFields["{$formId}_{$slug}"]);
                } else {
                    $originalFields["{$formId}_{$slug}"] = array_merge($originalFields["{$formId}_{$slug}"], $attrs);
                }
            }
        }

        return $originalFields;
    }
}