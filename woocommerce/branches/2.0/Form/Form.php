<?php

namespace tiFy\Plugins\Woocommerce\Form;

use tiFy\Kernel\Params\ParamsBag;
use tiFy\Plugins\Woocommerce\Contracts\Form as FormContract;
use tiFy\Plugins\Woocommerce\WoocommerceResolverTrait;
use WooCommerce;

/**
 * FORMULAIRES
 * @see Woocommerce/includes/wc-template-functions.php
 * @see https://docs.woocommerce.com/document/tutorial-customising-checkout-fields-using-actions-and-filters/
 */
class Form extends ParamsBag implements FormContract
{
    use WoocommerceResolverTrait;

    /**
     * CONSTRUCTEUR.
     *
     * @retun void
     */
    public function __construct($attrs = [])
    {
        parent::__construct($attrs);

        $this->selectJs();
        $this->selectJsCountry();
        $this->selectJsState();

        $this->addAddressFields($this->get('add_address_fields', []));
        $this->setAddressFormFields('billing', $this->get('billing', []));
        $this->setAddressFormFields('shipping', $this->get('shipping', []));
        $this->setCheckoutFormFields($this->get('checkout', []));

        $this->setFormFieldArgs();

        $this->saveAddress();
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
            add_filter('woocommerce_default_address_fields', function ($fields) use ($additionalFields) {
                foreach ($additionalFields as $field => $attrs) :
                    if (!isset($fields[$field])) :
                        $fields[$field] = $attrs;
                    endif;
                endforeach;

                return $fields;
            });

            add_filter('woocommerce_customer_meta_fields', function ($fields) use ($additionalFields) {
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
                                    [$_slug => array_merge(
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
                            else :
                                $fields[$addressType]['fields'][$_slug] = $adminAttrs;
                            endif;
                        endforeach;
                    endif;
                endforeach;

                return $fields;
            });
        endif;
    }

    /**
     * Sauvegarde des adresses de facturation et livraison.
     *
     * @return void
     */
    protected function saveAddress()
    {
        add_action('woocommerce_after_save_address_validation', function ($user_id, $load_address, $address) {
            foreach ($address as $key => $field) :
                if (!isset($field['type'])) :
                    $field['type'] = 'text';
                endif;
                switch ($field['type']) :
                    case 'checkbox' :
                        $_POST[$key] = (int)isset($_POST[$key]);
                        break;
                    default :
                        $_POST[$key] = isset($_POST[$key]) ? wc_clean(wp_unslash($_POST[$key])) : '';
                        break;
                endswitch;
            endforeach;
        }, 10, 3);
    }

    /**
     * Court-circuitage des attributs de champ de formulaire.
     *
     * @return void
     */
    protected function setFormFieldArgs()
    {
        add_filter('woocommerce_form_field_args', function ($args, $key, $value) {
            if (method_exists($this, 'form_field_args_' . $key)) :
                return call_user_func([$this, 'form_field_args_' . $key], $args, $value);
            else :
                return call_user_func([$this, 'form_fields_args'], $args, $key, $value);
            endif;
        }, 10, 3);
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
            add_filter("woocommerce_{$formId}_fields", function ($currentFields) use ($formId, $fields) {
                return $this->overwriteFormFields($formId, $currentFields, $fields);
            });
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
            add_filter('woocommerce_checkout_fields', function ($currentForms) use ($forms) {
                foreach ($forms as $form => $fields) :
                    if (!isset($currentForms[$form])) :
                        continue;
                    endif;
                    $currentForms[$form] = $this->overwriteFormFields($form, $currentForms[$form], $fields);
                endforeach;

                return $currentForms;
            });
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
     * Récupération des attributs HTML personnalisés d'un tag au format string.
     *
     * @param array $customAttrs Liste des attributs HTML personnalisés (clé => valeur)
     *
     * @return string
     */
    public function getCustomHtmlAttrs($customAttrs)
    {
        $custom_attributes = [];

        if (!empty($customAttrs) && is_array($customAttrs)) :
            foreach ($customAttrs as $attribute => $attribute_value) :
                $custom_attributes[] = esc_attr($attribute) . '="' . esc_attr($attribute_value) . '"';
            endforeach;
        endif;

        return implode(' ', $custom_attributes);
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

    /**
     * {@inheritdoc}
     */
    public function hasSelectJsField($haystack)
    {
        foreach ($haystack as $slug => $attrs) :
            if (is_array($attrs) && $this->hasSelectJsField($attrs)) :
                return true;
            elseif (($slug === 'type') && in_array($attrs, ['select_js', 'select_js_country'])) :
                return true;
            endif;
        endforeach;

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isSelectJsEnabled()
    {
        foreach (['add_address_fields', 'billing', 'shipping', 'checkout'] as $form) :
            if ($this->hasSelectJsField($this->get($form, []))) :
                return true;
            endif;
        endforeach;

        return false;
    }

    /**
     * Définition du type de champ "Sélecteur JS".
     *
     * @see wp-content/plugins/woocommerce/includes/wc-template-functions.php
     *
     * @return void
     */
    protected function selectJs()
    {
        add_filter('woocommerce_form_field_select_js', function ($field, $key, $args, $value) {
            $field_type = 'select-js';

            $args['attrs'] = array_merge(
                [
                    'class' => esc_attr(implode(' ', $args['input_class']))
                ],
                isset($args['attrs']) ? $args['attrs'] : []
            );

            $args['picker'] = array_merge(
                [
                    'class' => isset($args['picker_class']) ? esc_attr(implode(' ', $args['picker_class'])) : null
                ],
                isset($args['picker']) ? $args['picker'] : []
            );

            $args['choices'] = isset($args['options']) ? $args['options'] : [];

            return (string)$this->viewer('field/field', compact('args', 'key', 'value', 'field_type'));
        }, 10, 4);
    }

    /**
     * Définition du type de champ "Sélecteur JS de pays".
     *
     * @see wp-content/plugins/woocommerce/includes/wc-template-functions.php
     *
     * @return void
     */
    protected function selectJsCountry()
    {
        add_filter('woocommerce_form_field_select_js_country', function ($field, $key, $args, $value) {
            $countries = 'shipping_country' === $key ? WC()->countries->get_shipping_countries() : WC()->countries->get_allowed_countries();

            $singleCountry = (1 === sizeof($countries));

            $field_type = $singleCountry ? 'single-country' : 'select-js-country';

            if (!$singleCountry) :
                $args['attrs'] = array_merge(
                    [
                        'class' => 'country_to_state country_select ' . esc_attr(implode(' ', $args['input_class']))
                    ],
                    isset($args['attrs']) ? $args['attrs'] : []
                );

                $args['picker'] = array_merge(
                    [
                        'class' => 'country_to_state country_select_picker ' . (isset($args['picker_class']) ? esc_attr(implode(' ', $args['picker_class'])) : null)
                    ],
                    isset($args['picker']) ? $args['picker'] : []
                );
                $countries = ['' => esc_html__('Select a country&hellip;', 'woocommerce')] + $countries;
                $args['attrs'] = array_merge($args['attrs'], $args['custom_attributes']);
            else :
                $args['custom_attributes'] = $this->getCustomHtmlAttrs($args['custom_attributes']);
            endif;

            $args['choices'] = $countries;

            return (string)$this->viewer('field/field', compact('args', 'key', 'value', 'field_type'));
        }, 10, 4);
    }

    /**
     * Définition du type de champ "Sélecteur JS d'état/comté".
     *
     * @see wp-content/plugins/woocommerce/includes/wc-template-functions.php
     *
     * @return void
     */
    protected function selectJsState()
    {
        add_filter('woocommerce_form_field_select_js_state', function ($field, $key, $args, $value) {
            $for_country = isset($args['country']) ? $args['country'] : WC()->checkout->get_value('billing_state' === $key ? 'billing_country' : 'shipping_country');
            $states = WC()->countries->get_states($for_country);

            if (is_array($states) && empty($states)) :
                $field_type = 'empty-state';
                $args['custom_attributes'] = $this->getCustomHtmlAttrs($args['custom_attributes']);
                $args['inline_style'] = 'style="display:none;"';
            elseif (!is_null($for_country) && is_array($states)) :
                $field_type = 'select-js';
                $args['attrs'] = array_merge(
                    [
                        'class' => 'state_select ' . esc_attr(implode(' ', $args['input_class']))
                    ],
                    isset($args['attrs']) ? $args['attrs'] : []
                );

                $args['picker'] = array_merge(
                    [
                        'class' => 'state_select_picker ' . (isset($args['picker_class']) ? esc_attr(implode(' ', $args['picker_class'])) : null)
                    ],
                    isset($args['picker']) ? $args['picker'] : []
                );
                $args['choices'] = ['' => esc_html__('Select a state&hellip;', 'woocommerce')] + $states;
                $args['attrs'] = array_merge($args['attrs'], $args['custom_attributes']);
            else :
                $field_type = 'text-state';
                $args['custom_attributes'] = $this->getCustomHtmlAttrs($args['custom_attributes']);
            endif;

            return (string)$this->viewer('field/field', compact('args', 'key', 'value', 'field_type'));
        }, 10, 4);
    }
}