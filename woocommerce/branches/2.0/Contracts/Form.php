<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Contracts;

use tiFy\Contracts\Support\ParamsBag;

interface Form extends ParamsBag, WoocommerceAwareTrait
{
    /**
     * Initialisation de la classe.
     *
     * @return void
     */
    public function boot(): void;

    /**
     * Traitement des arguments par défaut de formulaire
     *
     * @param array $args {
     *      'type'              => 'text',
     *      'label'             => '',
     *      'description'       => '',
     *      'placeholder'       => '',
     *      'maxlength'         => false,
     *      'required'          => false,
     *      'autocomplete'      => false,
     *      'id'                => $key,
     *      'class'             => array(),
     *      'label_class'       => array(),
     *      'input_class'       => array(),
     *      'return'            => false,
     *      'options'           => array(),
     *      'custom_attributes' => array(),
     *      'validate'          => array(),
     *      'default'           => ''
     * }
     * @param string $key
     * @param mixed $value
     *
     * @see woocommerce_form_field()
     */
    public function form_fields_args(array $args, string $key, $value): array;

    /**
     * Exemple de traitement de champ -> prénom de l'adresse de facturation
     *
     * @param array $args
     * @param mixed $value
     *
     * @return array
     */
    public function form_field_args_billing_first_name(array $args, $value): array;

    /**
     * Récupération des attributs HTML personnalisés d'un tag au format string.
     *
     * @param array $customAttrs Liste des attributs HTML personnalisés (clé => valeur)
     *
     * @return string
     */
    public function getCustomHtmlAttrs(array $customAttrs): string;

    /**
     * Vérification de l'existence d'un champ utilisant le support SelectJs.
     *
     * @param array $haystack Champs de formulaire.
     *
     * @return boolean
     */
    public function hasSelectJsField(array $haystack): bool;

    /**
     * Vérification de l'activation du support SelectJs.
     *
     * @return boolean
     */
    public function isSelectJsEnabled(): bool;

    /**
     * Surcharge des champs d'un formulaire.
     *
     * @param string $formId Identifiant du formulaire. billing|shipping|account|order.
     * @param array $currentFields Champs de formulaire à écraser.
     * @param array $newFields Champs de formulaire écrasants.
     *
     * @return array
     */
    public function overwriteFormFields(string $formId, array $currentFields, array $newFields): array;
}