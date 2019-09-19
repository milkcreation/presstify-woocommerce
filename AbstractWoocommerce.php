<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce;

use tiFy\Plugins\Woocommerce\Contracts\Woocommerce;

abstract class AbstractWoocommerce
{
    /**
     * Instance du gestionnaire du plugin woocommerce.
     * @var Woocommerce
     */
    protected $manager;

    /**
     * CONSTRUCTEUR.
     *
     * @param Woocommerce $manager Conteneur Instance du gestionnaire du plugin woocommerce.
     *
     * @return void
     */
    public function __construct(Woocommerce $manager)
    {
        $this->manager = $manager;
    }
}
