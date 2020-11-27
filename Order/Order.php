<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Order;

use tiFy\Plugins\Woocommerce\{Contracts\Order as OrderContract, Contracts\QueryOrder, WoocommerceAwareTrait};
use tiFy\Wordpress\Contracts\Query\PaginationQuery;

class Order implements OrderContract
{
    use WoocommerceAwareTrait;

    /**
     * Instance de la requête de pagination.
     * @var PaginationQuery|null
     */
    protected $pagination;

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        $this->boot();
    }

    /**
     * @inheritDoc
     */
    public function boot(): void {}

    /**
     * @inheritDoc
     */
    public function get($id = null): ?QueryOrder
    {
        /** @var QueryOrder $instance */
        $instance = $this->manager->resolve('query.order');

        return $instance::create($id);
    }

    /**
     * @inheritDoc
     */
    public function fetch($query = null): array
    {
        /** @var QueryOrder $instance */
        $instance = $this->manager->resolve('query.order');

        if ($orders = $instance::fetch($query)) {
            $this->pagination = $instance::pagination();
        }

        return $orders;
    }

    /**
     * @inheritDoc
     */
    public function pagination($renew = false): PaginationQuery
    {
        if ($renew || is_null($this->pagination)) {
            /** @var QueryOrder $instance */
            $instance = $this->manager->resolve('query.order');

            return $this->pagination = $instance::pagination();
        }

        return $this->pagination;
    }
}