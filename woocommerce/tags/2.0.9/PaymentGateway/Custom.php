<?php
/**
 * @Overrideable
 */

namespace tiFy\Plugins\Woocommerce\PaymentGateway;

class Custom extends \WC_Payment_Gateway
{
    /**
     * CONSTRUCTEUR
     */
    public function __construct()
    {
        // Identifiant de la plateforme de paiement
        $this->id = 'tify_plugin_wc_custom_payment';

        // Intitulé de ma méthode de paiement
        $this->method_title = __('Paiement personnalisé', 'tify');

        $this->title      = __('Paiement personnalisé', 'tify');
        $this->has_fields = true;

        // Chargement de la configuration
        $this->init_form_fields();
        $this->init_settings();

        $this->enabled     = $this->get_option('enabled');
        $this->title       = $this->get_option('title');
        $this->description = $this->get_option('description');

        add_action('woocommerce_update_options_payment_gateways_' . $this->id, [$this, 'process_admin_options']);
    }

    /**
     * Initialisation des champs de formulaire de l'interface d'administration
     * {@inheritDoc}
     * @see WC_Settings_API::init_form_fields()
     */
    public function init_form_fields()
    {
        $this->form_fields = [
            'enabled'     => [
                'title'   => __('Activer/Désactiver', 'tify'),
                'type'    => 'checkbox',
                'label'   => __('Activer le paiement personnalisé', 'tify'),
                'default' => 'no'
            ],
            'title'       => [
                'title'       => __('Titre', 'tify'),
                'type'        => 'text',
                'description' => __('Détermine le titre du moyen de paiement que les clients verront sur la page de commande.',
                    'tify'),
                'default'     => __('Paiement personnalisé', 'tify'),
                'desc_tip'    => true,
            ],
            'description' => [
                'title'       => __('Description', 'tify'),
                'type'        => 'textarea',
                'description' => __('Détails sur le moyen de paiement, visible par le client sur la page de commande, lorsqu\'il choisi cette méthode.',
                    'tify'),
                'default'     => '',
                'desc_tip'    => true
            ]
        ];
    }

    /**
     *
     * {@inheritDoc}
     * @see WC_Payment_Gateway::admin_options()
     */
    public function admin_options()
    {
        ?>
        <h3><?php _e('Paiement personnalisé', 'tify'); ?></h3>
        <table class="form-table">
            <?php $this->generate_settings_html(); ?>
        </table>
        <?php
    }

    /**
     * Processus de paiement
     * {@inheritDoc}
     * @see WC_Payment_Gateway::process_payment()
     */
    public function process_payment($order_id)
    {
        global $woocommerce;
        $order = new \WC_Order($order_id);

        // Indique que la commande est en attente de paiement
        $order->update_status('on-hold', __('En attente de paiement', 'tify'));

        // Ajustement du stock
        wc_reduce_stock_levels($order_id);

        // Vide le panier
        $woocommerce->cart->empty_cart();

        // Retour vers la page de remerciement
        return [
            'result'   => 'success',
            'redirect' => $this->get_return_url($order)
        ];
    }

    /**
     *
     * {@inheritDoc}
     * @see WC_Payment_Gateway::payment_fields()
     */
    public function payment_fields()
    {
        ?>
        <p>
            <?php echo esc_attr($this->description); ?>
        </p>
        <?php
    }
}