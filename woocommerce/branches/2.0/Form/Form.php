<?php

namespace tiFy\Plugins\Woocommerce\Form;

use tiFy\Kernel\Params\ParamsBag;
use tiFy\Plugins\Woocommerce\Contracts\Form as FormContract;

/**
 * FORMULAIRES
 * @see Woocommerce/includes/wc-template-functions.php
 * @see https://docs.woocommerce.com/document/tutorial-customising-checkout-fields-using-actions-and-filters/
 */
class Form extends ParamsBag implements FormContract
{
    /**
     * Remplacement de la liste de sélection des pays par une liste de type tiFySelectJs.
     * @var bool
     */
    protected $tiFySelectJsCountry = false;

    /**
     * CONSTRUCTEUR.
     *
     * @retun void
     */
    public function __construct($attrs = [])
    {
        parent::__construct($attrs);

        $this->tiFySelectJsCountry = $this->get('tify_select_js_country', $this->tiFySelectJsCountry);

        $this->addAddressFields($this->get('add_address_fields', []));
        $this->setAddressFormFields('billing', $this->get('billing', []));
        $this->setAddressFormFields('shipping', $this->get('shipping', []));
        $this->setCheckoutFormFields($this->get('checkout', []));

        $this->setFormFieldArgs();


        // Court-circuitage des attributs de champ de formulaire
        //add_filter('woocommerce_form_field_tify_dropdown_country', [$this, 'woocommerce_form_field_tify_dropdown_country'], 10, 4);
    }

    /**
     * Ajout de champs personnalisés aux formulaires d'adresse de facturation/livraison.
     *
     * @param array $additionalFields Liste des champs personnalisés.
     *
     * @see woocommerce_form_field()
     *
     * @return void
     */
    protected function addAddressFields($additionalFields)
    {
        if ($additionalFields) :
            add_filter(
                'woocommerce_default_address_fields',
                function ($fields) use ($additionalFields) {
                    foreach ($additionalFields as $field => $attrs) :
                        if (!isset($fields[$field])) :
                            $fields[$field] = $attrs;
                        endif;
                    endforeach;

                    return $fields;
                }
            );
            add_filter(
                'woocommerce_customer_meta_fields',
                function ($fields) use ($additionalFields) {
                    foreach ($additionalFields as $slug => $attrs) :
                        if (!empty($attrs['admin'])) :
                            foreach ($attrs['admin'] as $addressType => $adminAttrs) :
                                $_slug = "{$addressType}_{$slug}";
                                if (!$addressType || isset($fields[$addressType]['fields'][$_slug]) || !$adminAttrs) :
                                    continue;
                                endif;
                                if (!empty($adminAttrs['before'])) :
                                    $pos = array_search("{$addressType}_{$adminAttrs['before']}", array_keys($fields[$addressType]['fields']));
                                    $fields[$addressType]['fields'] = array_merge(
                                        array_slice($fields[$addressType]['fields'], 0, $pos),
                                        [$_slug => $adminAttrs],
                                        array_slice($fields[$addressType]['fields'], $pos)
                                    );
                                else :
                                    $fields[$addressType]['fields'][$_slug] = $adminAttrs;
                                endif;
                            endforeach;
                        endif;
                    endforeach;

                    return $fields;
                }
            );
        endif;
    }

    /**
     * Court-circuitage des attributs de champ de formulaire.
     *
     * @return void
     */
    protected function setFormFieldArgs()
    {
        add_filter(
            'woocommerce_form_field_args',
            function ($args, $key, $value) {
                if (in_array($key, ['billing_country', 'shipping_country']) && $this->tiFySelectJsCountry) :
                    $args['type'] = 'tify_select_js_country';
                endif;

                if (method_exists($this, 'form_field_args_' . $key)) :
                    return call_user_func([$this, 'form_field_args_' . $key], $args, $value);
                else :
                    return call_user_func([$this, 'form_fields_args'], $args, $key, $value);
                endif;
            },
            10,
            3
        );
    }

    /**
     * Surcharge des champs de formulaire d'adresse de facturation et de livraison.
     *
     * @param string $formId Identifiant du formulaire (billing|shipping)
     * @param array $fields Champs de formulaire écrasants.
     *
     * @see woocommerce_form_field()
     *
     * @return void
     */
    protected function setAddressFormFields($formId, $fields)
    {
        if ($fields) :
            add_filter(
                "woocommerce_{$formId}_fields",
                function ($currentFields) use ($formId, $fields) {
                    return $this->overwriteFormFields($formId, $currentFields, $fields);
                }
            );
        endif;
    }

    /**
     * Surcharge des champs des différents formulaires au niveau du processus commande.
     *
     * @param array $forms Formulaires à surcharger.
     *
     * @return void
     */
    protected function setCheckoutFormFields($forms)
    {
        if ($forms) :
            add_filter(
                'woocommerce_checkout_fields',
                function ($currentForms) use ($forms) {
                    foreach ($forms as $form => $fields) :
                        if (!isset($currentForms[$form])) :
                            continue;
                        endif;
                        $currentForms[$form] = $this->overwriteFormFields($form, $currentForms[$form], $fields);
                    endforeach;

                    return $currentForms;
                }
            );
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function overwriteFormFields($formId, $currentFields, $newFields)
    {
        foreach ($newFields as $slug => $attrs) :
            if (isset($currentFields["{$formId}_{$slug}"])) :
                if ($attrs === false) :
                    unset($currentFields["{$formId}_{$slug}"]);
                else :
                    $currentFields["{$formId}_{$slug}"] = array_merge($currentFields["{$formId}_{$slug}"], $attrs);
                endif;
            endif;
        endforeach;

        return $currentFields;
    }

    /**
     *
     * @see wp-content/plugins/woocommerce/includes/wc-template-functions.php
     */
    /*final public function woocommerce_form_field_tify_dropdown_country($field, $key, $args, $value)
    {
        $field = "";

        if ($args['required']) :
            $args['class'][] = 'validate-required';
            $required = ' <abbr class="required" title="' . esc_attr__('required', 'woocommerce') . '">*</abbr>';
        else :
            $required = '';
        endif;

        if ($args['label']) :
            $field .= '<label for="' . esc_attr($args['id']) . '" class="' . esc_attr(implode(' ', $args['label_class'])) . '">' . $args['label'] . $required . '</label>';
        endif;

        $sort = $args['priority'] ? $args['priority'] : '';
        $field_container = '<div class="form-row %1$s" id="%2$s" data-priority="' . esc_attr($sort) . '">%3$s</div>';
        $countries = 'shipping_country' === $key ? WC()->countries->get_shipping_countries() : WC()->countries->get_allowed_countries();

        if (1 === sizeof($countries)) :
            $field .= '<strong>' . current(array_values($countries)) . '</strong>';
            $field .= '<input type="hidden" name="' . esc_attr($key) . '" id="' . esc_attr($args['id']) . '" value="' . current(array_keys($countries)) . '" ' . implode(' ', array_filter((array)$args['custom_attributes'])) . ' class="country_to_state" />';
        else :
            $field .= tify_control_dropdown(
                [
                    'name'              => esc_attr($key),
                    // 'id'                => esc_attr( $args['id'] ), // BUG avec le checkoutjs
                    'class'             => 'country_to_state country_select ' . esc_attr(implode(' ', $args['input_class'])),
                    'attrs'             => array_filter((array)$args['custom_attributes']),
                    'show_option_none'  => esc_html__('Select a country&hellip;', 'woocommerce'),
                    'option_none_value' => '',
                    'choices'           => $countries,
                    'selected'          => $value,
                    'picker'            => [
                        'class' => 'country_to_state country_select_picker ' . (isset($args['picker_class']) ? esc_attr(implode(' ', $args['picker_class'])) : null)
                    ]
                ],
                false
            );
            $field .= '<noscript><input type="submit" name="woocommerce_checkout_update_totals" value="' . esc_attr__('Update country', 'woocommerce') . '" /></noscript>';
        endif;

        if ($args['description']) :
            $field .= '<span class="description">' . esc_html($args['description']) . '</span>';
        endif;

        $container_class = esc_attr(implode(' ', $args['class']));
        $container_id = esc_attr($args['id']) . '_field';
        $field = sprintf($field_container, $container_class, $container_id, $field);

        return $field;
    }*/

    /**
     * {@inheritdoc}
     */
    public function istiFySelectJsCountry()
    {
        return $this->tiFySelectJsCountry;
    }

    /**
     * Traitement des arguments par défaut de formulaire
     *
     * @param array $args
     * 'type'              => 'text',
     * 'label'             => '',
     * 'description'       => '',
     * 'placeholder'       => '',
     * 'maxlength'         => false,
     * 'required'          => false,
     * 'autocomplete'      => false,
     * 'id'                => $key,
     * 'class'             => array(),
     * 'label_class'       => array(),
     * 'input_class'       => array(),
     * 'return'            => false,
     * 'options'           => array(),
     * 'custom_attributes' => array(),
     * 'validate'          => array(),
     * 'default'           => ''
     * @param string $key
     * @param string $value
     *
     * @see woocommerce_form_field()
     */
    public function form_fields_args($args, $key, $value)
    {
        return $args;
    }

    /**
     * Exemple de traitement de champ -> prénom de l'adresse de facturation
     *
     * @param array $args
     * @param string $value
     */
    public function form_field_args_billing_first_name($args, $value)
    {
        return $this->form_fields_args($args, 'billing_first_name', $value);
    }
}