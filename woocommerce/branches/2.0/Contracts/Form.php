<?php

namespace tiFy\Plugins\Woocommerce\Contracts;

use tiFy\Contracts\Kernel\ParamsBag;

interface Form extends ParamsBag
{
    /**
     * Court-circuitage des attributs de champ de formulaire.
     *
     * @return void
     */
    public function setFormFieldArgs();

    /**
     * Vérification du remplacement de la liste de choix des pays.
     *
     * @return bool
     */
    public function istiFySelectJsCountry();
}