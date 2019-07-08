<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Contracts;

use tiFy\Contracts\Support\ParamsBag;
use tiFy\Wordpress\Contracts\{QueryPost, QueryTerm};

interface StoreFactory extends ParamsBag
{
    /**
     * Initialisation du magasin.
     *
     * @return void
     */
    public function boot(): void;

    /**
     * Récupération du nom de qualification du magasin.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Récupération de la page d'accroche associée.
     *
     * @return QueryPost|null
     */
    public function getPage(): ?QueryPost;

    /**
     * Récupération de la page d'accroche associée.
     *
     * @return int
     */
    public function getPageId(): int;

    /**
     * Récupération de l'identifiant catégorie d'affichage.
     *
     * @return QueryTerm|null
     */
    public function getProductCat(): ?QueryTerm;

    /**
     * Récupération de l'identifiant catégorie d'affichage.
     *
     * @return int
     */
    public function getProductCatId(): int;

    /**
     * Vérifie si la page courante fait partie de la boutique.
     *
     * @return boolean
     */
    public function isCurrent(): bool;

    /**
     * Préparation du magasin.
     *
     * @param string $name Nom de qualification du magasin.
     * @param Multistore $stores Instance du gestionnaire de magasins.
     *
     * @return static
     */
    public function prepare(string $name, Multistore $stores): StoreFactory;

    /**
     * Définition de metaboxe de réglage des options.
     *
     * @param string $name Nome de qualification.
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return StoreFactory
     */
    public function setOptionsMetabox(string $name, array $attrs = []): StoreFactory;

    /**
     * Récupération de l'instance des boutiques associées.
     *
     * @return Multistore
     */
    public function stores(): Multistore;
}