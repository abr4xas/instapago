<?php
/**
 * Configuración de Instapago.
 *
 * @package WC_Gateway_Instapago_Commerce
 * @category Admin
 * @author Angel Cruz
 * @copyright Copyright (C) Angel Cruz <me@abr4xas.org> and WooCommerce
 */

if (!defined('ABSPATH'))
{
    exit;
}

/**
 * Settings for Instapago Gateway.
 */
return array(
    'enabled' => array(
        'title' => __('Enable/Disable', 'woocommerce') ,
        'type' => 'checkbox',
        'label' => __('Habilitar Instapago', 'woocommerce') ,
        'default' => 'no'
    ) ,
    'title' => array(
        'title' => __('Título', 'woocommerce') ,
        'type' => 'text',
        'description' => __('Esto controla el título que el usuario ve durante la compra.', 'woocommerce') ,
        'default' => __('Instapago', 'woocommerce'),
        'desc_tip' => true,
    ) ,
    'description' => array(
        'title' => __('Descripción', 'woocommerce') ,
        'type' => 'text',
        'desc_tip' => true,
        'description' => __('Esto controla la descripción que el usuario ve durante la compra.', 'woocommerce') ,
        'default' => __('Puedes pagar con tu tarjeta de crédito.', 'woocommerce')
    ) ,
    'api_details' => array(
        'title' => __('Credenciales de la API de Instapago', 'woocommerce') ,
        'type' => 'title',
        'description' => sprintf(__('Ingrese su <strong>keyId</strong> y <strong>publicKeyId</strong>  puede obtenerlas haciendo clic %saquí%s.', 'woocommerce') , '<a href="https://banesco.instapago.com/Account/API" target="_blank">', '</a>') ,
    ) ,
    'api_keyId' => array(
        'title' => __('keyId', 'woocommerce') ,
        'type' => 'text',
        'description' => __('Se encuentra en su panel de usuario en instapago.com', 'woocommerce') ,
        'default' => '',
        'desc_tip' => true,
        'placeholder' => __('Requerido', 'woocommerce')
    ) ,
    'api_publicKeyId' => array(
        'title' => __('publicKeyId', 'woocommerce') ,
        'type' => 'text',
        'description' => __('Se encuentra en su buzón de correo.', 'woocommerce') ,
        'default' => '',
        'desc_tip' => true,
        'placeholder' => __('Requerido', 'woocommerce')
    ) ,	
    'paymentaction' => array(
        'title'       => __( 'Payment Action', 'woocommerce' ),
        'type'        => 'select',
        'class'       => 'wc-enhanced-select',
        'description' => __( 'Debe indicar si desea retener o autorizar el pago.', 'woocommerce' ),
        'default'     => '2',
        'desc_tip'    => true,
        'options'     => array(
            '1'          => __( 'Retener (pre-autorización)', 'woocommerce' ),
            '2' => __( 'Pagar (autorización).', 'woocommerce' )
        )
    ),
    'api_debug' => array(
        'title' => __('Modo de depuración', 'woocommerce') ,
        'type' => 'title',
        'description' => sprintf(__('Desactivar cuando terminen las pruebas de integración', 'woocommerce')) ,
    ) ,
    'debug' => array(
        'title' => __('Debug Log', 'woocommerce') ,
        'type' => 'checkbox',
        'label' => __('Enable logging', 'woocommerce') ,
        'default' => 'yes',
        'description' => sprintf(__('Save Instapago events inside <code>%s</code>', 'woocommerce') , wc_get_log_file_path('instapago'))
    ) ,
    'mail_details' => array(
        'title' => __('Mensajes personalizados en el correo de confirmación de compras', 'woocommerce') ,
        'type' => 'title',
        'description' => sprintf(__('En este apartado puede configurar los mensajes que van aparecer en el correo de confirmación de compra que se le envía al cliente junto al voucher.<br/> Todos estos mensajes son necesarios.', 'woocommerce') ) ,
    ),
    'mail_header' => array(
        'title' => __('Mensaje principal del correo', 'woocommerce') ,
        'type' => 'text',
        'description' => __('Mensaje principal del correo', 'woocommerce') ,
        'default' => 'Confirmación de Compra',
        'desc_tip' => true,
        'placeholder' => __('Requerido', 'woocommerce')
    ),
    'mail_subheader' => array(
        'title' => __('Mensaje secundario del correo', 'woocommerce') ,
        'type' => 'text',
        'description' => __('Mensaje secundario del correo', 'woocommerce') ,
        'default' => 'Acontinuación el Voucher de su compra. Guarde para sus archivos',
        'desc_tip' => true,
        'placeholder' => __('Requerido', 'woocommerce')
    )
);
