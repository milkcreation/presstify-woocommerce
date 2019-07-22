<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Contracts;

use tiFy\Contracts\Support\ParamsBag;

interface ScriptLoader extends ParamsBag, WoocommerceAwareTrait
{
    /**
     * Déclaration d'un script associé à un contexte.
     *
     * @param string $tag Nom du contexte.
     * @param string $hook Nom du style.
     *
     * @return void
     */
    public function addScript(string $tag, string $hook): void;

    /**
     * Déclaration d'un style associé à un contexte.
     *
     * @param string $tag Nom du contexte.
     * @param string $hook Nom du style.
     *
     * @return void
     */
    public function addStyle(string $tag, string $hook): void;

    /**
     * Mise en file des scripts après les déclarations.
     *
     * @param string $tag
     *
     * @return void
     */
    public function enqueue_scripts_after(string $tag): void;

    /**
     * is_account_page() - Mise en file des scripts après les déclarations.
     *
     * @return void
     */
    public function enqueue_scripts_after_account_page(): void;

    /**
     * is_product_cart() - Mise en file des scripts après les déclarations.
     *
     * @return void
     */
    public function enqueue_scripts_after_cart(): void;

    /**
     * is_product_checkout() - Mise en file des scripts après les déclarations.
     *
     * @return void
     */
    public function enqueue_scripts_after_checkout(): void;

    /**
     * is_product() - Mise en file des scripts après les déclarations.
     *
     * @return void
     */
    public function enqueue_scripts_after_product(): void;

    /**
     * is_product_category() - Mise en file des scripts après les déclarations.
     *
     * @return void
     */
    public function enqueue_scripts_after_product_category(): void;

    /**
     * is_product_tag() - Mise en file des scripts après les déclarations.
     *
     * @return void
     */
    public function enqueue_scripts_after_product_tag(): void;

    /**
     * is_shop() - Mise en file des scripts après les déclarations.
     *
     * @return void
     */
    public function enqueue_scripts_after_shop(): void;

    /**
     * is_woocommerce() - Mise en file des scripts après les déclarations.
     *
     * @return void
     */
    public function enqueue_scripts_after_woocommerce(): void;

    /**
     * Mise en file des scripts avant les déclarations.
     *
     * @param string $tag
     *
     * @return void
     */
    public function enqueue_scripts_before(string $tag): void;

    /**
     * is_account_page() - Mise en file des scripts avant les déclarations.
     *
     * @return void
     */
    public function enqueue_scripts_before_account_page(): void;

    /**
     * is_product_cart() - Mise en file des scripts avant les déclarations.
     *
     * @return void
     */
    public function enqueue_scripts_before_cart(): void;

    /**
     * is_product_checkout() - Mise en file des scripts avant les déclarations.
     *
     * @return void
     */
    public function enqueue_scripts_before_checkout(): void;

    /**
     * is_product() - Mise en file des scripts avant les déclarations.
     *
     * @return void
     */
    public function enqueue_scripts_before_product(): void;

    /**
     * is_product_category() - Mise en file des scripts avant les déclarations.
     *
     * @return void
     */
    public function enqueue_scripts_before_product_category(): void;
    /**
     * is_product_tag() - Mise en file des scripts avant les déclarations.
     *
     * @return void
     */
    public function enqueue_scripts_before_product_tag(): void;

    /**
     * is_shop() - Mise en file des scripts avant les déclarations.
     *
     * @return void
     */
    public function enqueue_scripts_before_shop(): void;

    /**
     * is_woocommerce() - Mise en file des scripts avant les déclarations.
     *
     * @return void
     */
    public function enqueue_scripts_before_woocommerce(): void;

    /**
     * Vérification d'existence d'un script associé à un contexte.
     *
     * @param string $tag Nom du contexte.
     *
     * @return boolean
     */
    public function hasScript($tag): bool;

    /**
     * Vérification d'existence d'un style associé à un contexte.
     *
     * @param string $tag Nom du contexte.
     *
     * @return boolean
     */
    public function hasStyle(string $tag): bool;

    /**
     * Récupération des scripts associé à contexte.
     *
     * @param string $tag Nom du contexte.
     *
     * @return array|false
     */
    public function getScripts(string $tag): array;

    /**
     * Récupération des styles associé à un contexte.
     *
     * @param string $tag Nom du contexte.
     *
     * @return array
     */
    public function getStyles(string $tag): array;
}