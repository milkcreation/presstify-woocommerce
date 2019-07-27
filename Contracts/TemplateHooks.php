<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Contracts;

use tiFy\Contracts\Support\ParamsBag;

interface TemplateHooks extends ParamsBag, WoocommerceAwareTrait
{
    /**
     * Initialisation de la classe.
     *
     * @return void
     */
    public function boot(): void;

    /**
     * Ajout d'un élément d'accroche.
     *
     * @param string $tag Identifiant de qualification.
     * @param callable $function Fonction associée.
     * @param int $priority Priorité d'exécution.
     *
     * @return bool
     */
    public function hookAdd(string $tag, callable $function, $priority = 10): bool;

    /**
     * Changement de priorité d'un élément d'accroche.
     *
     * @param string $tag Identifiant de qualification.
     * @param callable $function Fonction associée.
     * @param int $old Ancienne priorité d'exécution.
     * @param int $new Priorité d'exécution.
     *
     * @return bool
     */
    public function hookChange($tag, $function, $old = 10, $new = 10): bool;

    /**
     * Suppression d'un élément d'accroche.
     *
     * @param string $tag Identifiant de qualification.
     * @param callable $function Fonction associée.
     * @param int|null $priority Priorité d'exécution.
     *
     * @return bool
     */
    public function hookRemove(string $tag, callable $function, ?int $priority = null): bool;

    /**
     * {@inheritDoc}
     *
     * @return static
     */
    public function parse(): TemplateHooks;
}