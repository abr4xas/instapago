<?php

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;

final class WC_Instapago_Gateway_Blocks_Support extends AbstractPaymentMethodType
{
	protected $name = 'instapago';

	public function initialize(): void
	{
		$this->settings = get_option( "woocommerce_{$this->name}_settings", [] );
	}

	public function is_active(): bool
	{
		return ! empty( $this->settings['enabled'] ) && 'yes' === $this->settings['enabled'];
	}

	public function get_payment_method_script_handles(): array
	{
        $path =  WP_PLUGIN_DIR.'/instapago';

        $asset_path   = $path .  '/build/index.asset.php';
		$version      = null;
		$dependencies = [];

		if ( file_exists( $asset_path ) ) {
			$asset        = require $asset_path;
			$version      = $asset['version'] ?? $version;
			$dependencies = $asset['dependencies'] ?? $dependencies;
		}

		wp_register_script(
			'wc-instapago-gateway-blocks-integration',
            $path .  '/build/index.js',
			$dependencies,
			$version,
			true
		);

		return [
            'wc-instapago-gateway-blocks-integration'
        ];

	}

	public function get_payment_method_data(): array
	{
		return [
            'title'       => $this->get_setting( 'title' ),
            'description' => $this->get_setting( 'description' ),
        ];
	}
}
