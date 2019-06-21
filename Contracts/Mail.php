<?php

namespace tiFy\Plugins\Woocommerce\Contracts;

interface Mail
{
    /**
     * Formatage du nom du type de mail à débugger.
     *
     * @param string $name Nom du mail en snake_case.
     *
     * @return string
     */
    public function formattingName($name);

    /**
     * Debug de mail Woocommerce.
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
     *
     * @return void
     */
    public function debugMail($mailName);

    /**
     * Récupération de la classe de rappel de l'email.
     *
     * @return \WC_Email
     */
    public function getEmail();

    /**
     * Récupération du contenu du message de l'email.
     *
     * @return mixed|string|null
     */
    public function getMessage();
}