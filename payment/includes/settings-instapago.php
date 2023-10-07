<?php

/**
 * Configuración de Instapago.
 *
 * @category Admin
 *
 * @author     Angel Cruz <hello@tepuilabs.dev>
 * @copyright Copyright (C) Angel Cruz <hello@tepuilabs.dev> and WooCommerce
 */
if (!defined('ABSPATH')) {
	exit;
}

/*
 * Settings for Instapago Gateway.
 */
return [
	'enabled' => [
		'title'   => __('Enable/Disable', 'instapago'),
		'type'    => 'checkbox',
		'label'   => __('Habilitar Instapago', 'instapago'),
		'default' => 'no',
	],
	'title' => [
		'title'       => __('Título', 'instapago'),
		'type'        => 'text',
		'description' => __('Esto controla el título que el usuario ve durante la compra.', 'instapago'),
		'default'     => __('Instapago', 'instapago'),
		'desc_tip'    => true,
	],
	'description' => [
		'title'       => __('Descripción', 'instapago'),
		'type'        => 'text',
		'desc_tip'    => true,
		'description' => __('Esto controla la descripción que el usuario ve durante la compra.', 'instapago'),
		'default'     => __('Puedes pagar con tu tarjeta de crédito.', 'instapago'),
	],
	'api_details' => [
		'title'       => __('Credenciales de la API de Instapago', 'instapago'),
		'type'        => 'title',
		'description' => sprintf(__('Ingrese su <strong>keyId</strong> y <strong>publicKeyId</strong>  puede obtenerlas haciendo clic %saquí%s.', 'instapago'), '<a href="https://banesco.instapago.com/Account/API" target="_blank">', '</a>'),
	],
	'api_keyId' => [
		'title'       => __('keyId', 'instapago'),
		'type'        => 'text',
		'description' => __('Se encuentra en su panel de usuario en instapago.com', 'instapago'),
		'default'     => '',
		'desc_tip'    => true,
		'placeholder' => __('Requerido', 'instapago'),
	],
	'api_publicKeyId' => [
		'title'       => __('publicKeyId', 'instapago'),
		'type'        => 'text',
		'description' => __('Se encuentra en su buzón de correo.', 'instapago'),
		'default'     => '',
		'desc_tip'    => true,
		'placeholder' => __('Requerido', 'instapago'),
	],
	'api_debug' => [
		'title'       => __('Modo de depuración', 'instapago'),
		'type'        => 'title',
		'description' => sprintf(__('Desactivar cuando terminen las pruebas de integración', 'instapago')),
	],
	'debug' => [
		'title'       => __('Debug Log', 'instapago'),
		'type'        => 'checkbox',
		'label'       => __('Enable logging', 'instapago'),
		'default'     => 'yes',
		'description' => sprintf(__('Save Instapago events inside <code>%s</code>', 'instapago'), wc_get_log_file_path('instapago')),
	],
];
