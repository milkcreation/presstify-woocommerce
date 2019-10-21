<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Contracts;

use tiFy\Contracts\Support\ParamsBag;
use tiFy\Wordpress\Contracts\{Query\QueryPost, Query\QueryTerm};

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
     * Récupération du mode d'affichage de la page de boutique.
     *
     * @param string $default Mode d'affichage par défaut.
     *
     * @return string products|subcategories|both
     */
    public function getDisplayMode(string $default = 'products'): string;

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
     * Vérifie si la page courante est associé au magasin.
     *
     * @return boolean
     */
    public function isCurrent(): bool;

    /**
     * Préparation du magasin.
     *
     * @param string $name Nom de qualification du magasin.
     * @param Stores $stores Instance du gestionnaire de magasins.
     *
     * @return static
     */
    public function prepare(string $name, Stores $stores): StoreFactory;

    /**
     * Définition de l'association du magasin à la page d'affichage courante.
     *
     * @param boolean $current
     *
     * @return static
     */
    public function setCurrent(bool $current = true): StoreFactory;

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
     * @return Stores
     */
    public function stores(): Stores;
}