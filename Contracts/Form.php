<?php

namespace tiFy\Plugins\Woocommerce\Contracts;

use tiFy\Contracts\Kernel\ParamsBag;

interface Form extends ParamsBag
{
    /**
     * Vérification du remplacement de la liste de choix des pays.
     *
     * @return bool
     */
    public function istiFySelectJsCountry();

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