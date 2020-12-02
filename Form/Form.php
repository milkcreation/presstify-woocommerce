<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Form;

use tiFy\Plugins\Woocommerce\Contracts\Form as FormContract;
use tiFy\Plugins\Woocommerce\WoocommerceAwareTrait;
use tiFy\Support\Proxy\Field;
use tiFy\Support\Proxy\Partial;
use tiFy\Support\ParamsBag;
use WC_Customer;

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
        /** Définition du type de champ select_js. */
        add_filter('woocommerce_form_field_select_js', function ($field, $key, $args, $value) {
            $_field = $field;
            $attrs = $this->getFieldWrapperAttrs($args);
            $label = ($labelArgs = $this->getFieldLabelArgs($args)) ? Partial::get('tag', $labelArgs) : null;

            $field = Field::get('select-js', array_merge([
                'after'   => Partial::get('tag', [
                    'attrs'   => [
                        'id'          => esc_attr($args['id']) . '-description',
                        'class'       => 'description',
                        'aria-hidden' => 'true',
                    ],
                    'content' => $args['description'],
                    'tag'     => 'span',
                ]),
                'attrs'   => array_merge([
                    'class' => '%s ' . esc_attr(implode(' ', $args['input_class'])),
                ], $args['custom_attributes'] ?? []),
                'choices' => $args['options'],
                'handler' => [
                    'attrs' => [
                        'id' => $args['id'],
                    ],
                ],
                'name'    => $key,
                'value'   => $value,
            ], $args['extras']['input'] ?? []));

            return $this->manager->viewer(
                'field/index', compact('attrs', 'field', 'label', 'args', 'key', 'value', '_field')
            );
        }, 10, 4);
        /**/

        /** Définition du type de champ select_js_country. */
        add_filter('woocommerce_form_field_select_js_country', function ($field, $key, $args, $value) {
            $_field = $field;
            $attrs = $this->getFieldWrapperAttrs($args);
            $label = ($labelArgs = $this->getFieldLabelArgs($args)) ? Partial::get('tag', $labelArgs) : null;
            $choices = ('shipping_country' === $key)
                ? WC()->countries->get_shipping_countries() : WC()->countries->get_allowed_countries();

            $fieldArgs = [
                'attrs' => array_merge([
                    'class' => '%s ' . esc_attr(implode(' ', $args['input_class'])),
                ], $args['custom_attributes'] ?? []),
                'name'  => $key,
            ];
            if ((1 === sizeof($choices)) || isset($args['custom_attributes']['readonly'])) {
                $fieldArgs['attrs']['id'] = $args['id'];
                $fieldArgs['attrs']['class'] .= ' country_to_state';
                $fieldArgs['attrs']['readonly'] = 'readonly';

                $field = Field::get('hidden', array_merge($fieldArgs, [
                    'before' => Field::get('text', [
                        'attrs' => [
                            'readonly' => 'readonly'
                        ],
                        'value' => $choices[$value] ?? current(array_values($choices)),
                    ])->render(),
                    'value'  => $value ?: current(array_keys($choices)),
                ], $args['extras']['input'] ?? []));
            } else {
                $fieldArgs['attrs']['class'] .= ' country_to_state country_select';

                $fieldArgs = array_merge($fieldArgs, [
                    'after'   => Partial::get('tag', [
                        'attrs'   => [
                            'id'          => esc_attr($args['id']) . '-description',
                            'class'       => 'description',
                            'aria-hidden' => 'true',
                        ],
                        'content' => $args['description'],
                        'tag'     => 'span',
                    ])->render(),
                    'choices' => ['' => esc_html__('Select a country&hellip;', 'woocommerce')] + $choices,
                    'handler' => [
                        'attrs' => [
                            'id' => $args['id'],
                        ],
                    ],
                    'picker'  => [
                        'attrs'  => [
                            'class' => '%s country_to_state country_select_picker',
                        ],
                        'filter' => true,
                    ],
                    'value'   => $value,
                ], $args['extras']['input'] ?? []);

                $field = Field::get('select-js', $fieldArgs);
            }

            return $this->manager->viewer(
                'field/index', compact('attrs', 'field', 'label', 'args', 'key', 'value', '_field')
            );
        }, 10, 4);
        /**/

        /** Définition du type de champ select_js_state. */
        add_filter('woocommerce_form_field_select_js_state', function ($field, $key, $args, $value) {
            $_field = $field;
            $attrs = $this->getFieldWrapperAttrs($args);
            $label = ($labelArgs = $this->getFieldLabelArgs($args)) ? Partial::get('tag', $labelArgs) : null;

            $for_country = isset($args['country'])
                ? $args['country']
                : WC()->checkout->get_value('billing_state' === $key ? 'billing_country' : 'shipping_country');

            $states = WC()->countries->get_states($for_country);

            $fieldArgs = [
                'attrs' => array_merge([
                    'class' => '%s ' . esc_attr(implode(' ', $args['input_class'])),
                ], $args['custom_attributes'] ?? []),
                'name'  => $key,
            ];

            if (is_array($states) && empty($states)) {
                $fieldArgs['attrs']['id'] = $args['id'];
                $fieldArgs['attrs']['class'] .= ' hidden';
                $fieldArgs['attrs']['placeholder'] = $args['placeholder'];
                $fieldArgs['attrs']['readonly'] = 'readonly';
                $fieldArgs['attrs']['style'] = 'display:none;';

                $field = Field::get('hidden', array_merge($fieldArgs, [
                    'value' => '',
                ], $args['extras']['input'] ?? []));
            } elseif (!is_null($for_country) && is_array($states)) {
                $fieldArgs['attrs']['class'] .= ' state_select';

                $fieldArgs = array_merge($fieldArgs, [
                    'after'   => Partial::get('tag', [
                        'attrs'   => [
                            'id'          => esc_attr($args['id']) . '-description',
                            'class'       => 'description',
                            'aria-hidden' => 'true',
                        ],
                        'content' => $args['description'],
                        'tag'     => 'span',
                    ])->render(),
                    'choices' => ['' => esc_html__('Select a state&hellip;', 'woocommerce')] + $states,
                    'handler' => [
                        'attrs' => [
                            'id' => $args['id'],
                        ],
                    ],
                    'picker'  => [
                        'attrs'  => [
                            'class' => '%s country_to_state country_select_picker',
                        ],
                        'filter' => true,
                    ],
                    'value'   => $value,
                ], $args['extras']['input'] ?? []);

                $field = Field::get('select-js', $fieldArgs);
            } else {
                $fieldArgs['attrs']['id'] = $args['id'];
                $fieldArgs['attrs']['class'] .= ' input-text';
                $fieldArgs['attrs']['placeholder'] = $args['placeholder'];

                $field = Field::get('text', array_merge($fieldArgs, [
                    'value' => '',
                ], $args['extras']['input'] ?? []));
            }

            return $this->manager->viewer(
                'field/index', compact('attrs', 'field', 'label', 'args', 'key', 'value', '_field')
            );
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
                                            'class'       => '',
                                        ],
                                        $adminAttrs
                                    ),
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
        add_filter("woocommerce_billing_fields", function (array $originalFields) {
            if ($customfields = $this->get('billing', [])) {
                return $this->overwriteFormFields('billing', $originalFields, $customfields);
            }
            return $originalFields;
        });

        // Surcharge des champs de formulaire d'adresse de livraison.
        // @see woocommerce_form_field()
        add_filter("woocommerce_shipping_fields", function (array $originalFields) {
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
    public function boot(): void { }

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

    /**
     * @inheritDoc
     */
    public function parse(): FormContract
    {
        parent::parse();

        return $this;
    }

    /**
     * Récupération des attributs de l'étiquette selon les paramètres de configuration d'un champ.
     *
     * @param $args
     *
     * @return array|null
     */
    public function getFieldLabelArgs(array $args = []): ?array
    {
        if ($content = $args['label'] ?? '') {
            return array_merge([
                'after'   => $args['required'] ? Partial::get('tag', [
                    'attrs'   => [
                        'class' => '%s required',
                        'title' => __('required', 'woocommerce'),
                    ],
                    'content' => '*',
                    'tag'     => 'abbr',
                ]) : '',
                'attrs'   => [
                    'class' => '%s ' . esc_attr(implode(' ', $args['label_class'])),
                    'for'   => $args['id'],
                ],
                'content' => $content,
                'tag'     => 'label',
            ], $args['extras']['label'] ?? []);
        } else {
            return null;
        }
    }

    /**
     * Récupération des attributs de l'encapsuleur selon les paramètres de configuration d'un champ.
     *
     * @param $args
     *
     * @return array|null
     */
    public function getFieldWrapperAttrs(array $args)
    {
        return array_merge([
            'id'            => esc_attr($args['id']) . '_field',
            'class'         => '%s form-row ' . esc_attr(implode(' ', $args['class'])),
            'data-priority' => $args['priority'] ?: '',
        ], $args['extras']['wrapper_attrs'] ?? []);
    }
}