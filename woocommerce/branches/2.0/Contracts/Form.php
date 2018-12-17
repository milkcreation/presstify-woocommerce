<?php

namespace tiFy\Plugins\Woocommerce\Contracts;

use tiFy\Contracts\Kernel\ParamsBag;

interface Form extends ParamsBag
{
    /**
     * Vérification de l'existence d'un champ utilisant le support SelectJs.
     *
     * @param array $haystack Champs de formulaire.
     *
     * @return bool
     */
    public function hasSelectJsField($haystack);

    /**
     * Vérification de l'activation du support SelectJs.
     *
     * @return bool
     */
    public function isSelectJsEnabled();

    /**
     * Surcharge des champs d'un formulaire.
     *
     * @param int|string $formId Identifiant du formulaire (billing|shipping|account|order)
     * @param array $currentFields Champs de formulaire à écraser.
     * @param array $newFields Champs de formulaire écrasants.
     *
     * @return array
     */
    public function overwriteFormFields($formId, $currentFields, $newFields);
}