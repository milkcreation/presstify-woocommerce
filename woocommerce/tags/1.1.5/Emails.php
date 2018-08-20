<?php
/**
 * @Overrideable
 */
namespace tiFy\Plugins\WooCommerce;

use tiFy\Lib\StdClass;

class Emails extends \tiFy\App\Factory
{
    /**
     * Classe de rappel de l'email
     * @var \WC_Email
     */
    private $Email    = null;

    /**
     * Initialisation
     */
    final public function tFyAppOnInit()
    {
        self::tFyAppActionAdd('template_redirect');
    }

    /**
     * DECLENCHEURS
     */
    /**
     * Redirection de template
     *
     * @see \WC_Emails
     *
     * @see \WC_Email
     * @see \WC_Email_New_Order
     * @see \WC_Email_Cancelled_Order
     * @see \WC_Email_Failed_Order
     * @see \WC_Email_Customer_On_Hold_Order
     * @see \WC_Email_Customer_Processing_Order
     * @see \WC_Email_Customer_Completed_Order
     * @see \WC_Email_Customer_Refunded_Order
     * @see \WC_Email_Customer_Invoice
     * @see \WC_Email_Customer_Note
     * @see \WC_Email_Customer_Reset_Password
     * @see \WC_Email_Customer_New_Account
     */
    final public function template_redirect()
    {
        /**
         * @var string $_REQUEST['tfywc_email']
         */
        if(!isset($_REQUEST['tfywc_email'])) :
            return;
        endif;
        if (!is_user_logged_in()) :
            return;
        endif;
        if (!array_intersect(wp_get_current_user()->roles, ['administrator'])) :
            return;
        endif;

        $emails = WC()->mailer()->get_emails();
        $types[] = $_REQUEST['tfywc_email'];
        $sanitized = $types[] = StdClass::sanitizeName($_REQUEST['tfywc_email']);
        $types[] = 'WC_Email_' . $sanitized;

        foreach ($emails as $name => $inst) :
            foreach ($types as $type) :
                if (isset($emails[$type])) :
                    $this->Email = $emails[$type];
                    break 2;
                endif;
            endforeach;
        endforeach;

        if (!$email = $this->getEmail()) :
            return;
        endif;

        if (
            in_array(
                $email->id,
                [
                    'cancelled_order',
                    'customer_completed_order',
                    'customer_invoice',
                    'customer_note',
                    'customer_on_hold_order',
                    'customer_processing_order',
                    'customer_refunded_order',
                    'failed_order',
                    'new_order'
                ]
            )
        ) :
            $orders = wc_get_orders([
                'limit'   => 1,
                'orderby' => 'date',
                'order'   => 'DESC',
            ]);
            $order = current($orders);

            if (is_a($order, 'WC_Order')) :
                $email->object = $order;
                $email->recipient = $email->object->get_billing_email();

                $email->find['order-date'] = '{order_date}';
                $email->find['order-number'] = '{order_number}';

                $email->replace['order-date'] = wc_format_datetime($email->object->get_date_created());
                $email->replace['order-number'] = $email->object->get_order_number();
            endif;

        elseif(
            in_array(
                $email->id,
                [
                    'customer_new_account'
                ]
            )
        ) :
            $user_id = get_current_user_id();
            $user_pass = '';
            $password_generated = false;

            $email->object = new \WP_User($user_id);
            $email->user_pass = $user_pass;
            $email->user_login = stripslashes($email->object->user_login);
            $email->user_email = stripslashes($email->object->user_email);
            $email->recipient = $email->user_email;
            $email->password_generated = $password_generated;

        elseif(
            in_array(
                $email->id,
                [
                    'customer_reset_password'
                ]
            )
        ) :
            $user_login = \wp_get_current_user()->user_login;
            $reset_key = \wp_generate_password(32);

            $email->object = get_user_by('login', $user_login);
            $email->user_login = $user_login;
            $email->reset_key = $reset_key;
            $email->user_email = stripslashes($email->object->user_email);
            $email->recipient = $email->user_email;
        endif;

        echo $this->getMessage();

        exit;
    }

    /**
     * Récupération de la classe de rappel de l'email
     *
     * @return \WC_Email
     */
    final public function getEmail()
    {
        return $this->Email;
    }

    /**
     * Récupération du contenu du message de l'email
     *
     * @return mixed|string|void
     */
    public function getMessage()
    {
        if (!$email = $this->getEmail()) :
            return;
        endif;

        $email->setup_locale();
        $message = $email->get_content();
        $message = apply_filters('woocommerce_mail_content', $email->style_inline($message));
        $email->restore_locale();

        return $message;
    }

}