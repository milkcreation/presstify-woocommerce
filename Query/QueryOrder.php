<?php declare(strict_types=1);

namespace tiFy\Plugins\Woocommerce\Query;

use Exception;
use BadMethodCallException;
use tiFy\Plugins\Woocommerce\Contracts\QueryProduct as QueryProductContract;
use tiFy\Wordpress\Contracts\Query\QueryPost as QueryPostContract;
use tiFy\Wordpress\Query\QueryPost;
use tiFy\Plugins\Woocommerce\Contracts\QueryOrder as QueryOrderContract;
use WC_Order, WP_Post;

class QueryOrder extends QueryPost implements QueryOrderContract
{
    /**
     * Nom de qualification du type de post ou liste de types de post associés.
     * @var string|string[]|null
     */
    protected static $postType = 'shop_order';

    /**
     * Objet Product Woocommerce.
     * @var WC_Order|null
     */
    protected $wcOrder;

    /**
     * CONSTRUCTEUR.
     *
     * @param WC_Order|null $wc_order Objet Product Woocommerce.
     *
     * @return void
     */
    public function __construct(?WC_Order $wc_order = null)
    {
        if ($this->wcOrder = $wc_order instanceof WC_Order ? $wc_order : null) {
            parent::__construct(get_post($this->wcOrder->get_id()));
        }
    }

    /**
     * @inheritDoc
     */
    public function __call(string $method, $parameters = [])
    {
        if ($order = $this->getWcOrder()) {
            try {
                return $order->$method(...$parameters);
            } catch (Exception $e) {
                throw new BadMethodCallException(
                    sprintf(
                        __('La méthode [%s] de l\'objet Woocommerce n\'est pas disponible.', 'tify'),
                        $method
                    )
                );
            }
        } else {
            throw new BadMethodCallException(
                sprintf(
                    __('La méthode [%s] n\'est pas disponible.', 'tify'),
                    $method
                )
            );
        }
    }

    /**
     * @inheritDoc
     */
    public static function build(object $wc_order): ?QueryPostContract
    {
        if (!$wc_order instanceof WC_Order) {
            return null;
        }

        $classes = self::$builtInClasses;
        $post_type = $wc_order->get_type();

        $class = $classes[$post_type] ?? (self::$fallbackClass ?: static::class);

        return class_exists($class) ? new $class($wc_order) : new static($wc_order);
    }

    /**
     * @inheritDoc
     */
    public static function create($id = null, ...$args): ?QueryPostContract
    {
        if (is_numeric($id)) {
            return static::createFromId((int)$id);
        } elseif (is_string($id)) {
            return static::createFromName($id);
        } elseif ($id instanceof WC_Order) {
            return static::build($id);
        } elseif ($id instanceof WP_Post) {
            return static::createFromId($id->ID);
        } elseif ($id instanceof QueryPostContract) {
            return static::createFromId($id->getId());
        } elseif (is_null($id)) {
            return static::createFromGlobal();
        } else {
            return null;
        }
    }

    /**
     * {@inheritDoc}
     *
     * @return QueryProductContract|null
     */
    public static function createFromId(int $order_id): ?QueryPostContract
    {
        if ($order_id && ($order = WC()->order_factory->get_order($order_id)) && ($order instanceof WC_Order)) {
            return static::is($instance = static::build($order)) ? $instance : null;
        } else {
            return null;
        }
    }

    /**
     * @inheritDoc
     */
    public static function parseQueryArgs(array $args = []): array
    {
        if (!isset($args['post_type'])) {
            $args['post_type'] = static::$postType ?: 'shop_order';
        }

        if (!isset($args['post_status'])) {
            $args['post_status'] = array_keys(wc_get_order_statuses());
        }

        return array_merge(static::$defaultArgs, $args);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerId(): int
    {
        return (int) $this->getMetaSingle('_customer_user', 0);
    }

    /**
     * @inheritDoc
     */
    public function getShortStatus(): string
    {
        return preg_replace('/^wc-/', '', $this->getStatus()->getName());
    }

    /**
     * @inheritDoc
     */
    public function getWcOrder(): ?WC_Order
    {
        return $this->wcOrder;
    }

    /**
     * @inheritDoc
     */
    public function isCustomer(): bool
    {
        return is_user_logged_in() && ($this->getCustomerId() === get_current_user_id());
    }

    /**
     * @inheritDoc
     */
    public function hasShortStatus($statuses): bool
    {
        if (is_string($statuses)) {
            $statuses = [$statuses];
        }

        return in_array($this->getShortStatus(), $statuses);
    }
}