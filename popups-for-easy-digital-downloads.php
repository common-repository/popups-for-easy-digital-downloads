<?php
/*
 * Plugin Name:        Popups for Easy Digital Downloads
 * Version:            1.0.0
 * Description:        Easy Digital Downloads integration for WP Popups
 * Author:             WP Zone
 * Author URI          https://wpzone.co
 * License:            GPLv3+
 * License URI:        http://www.gnu.org/licenses/gpl.html
 * Domain Path:        includes/languages
 * Requires at least:  6.0
 * Requires PHP:       8.0
 * Text Domain:        popups-for-easy-digital-downloads
 */

class WPZ_WPPop_EDD {

	const VERSION = '1.0.0';

	protected $pluginDirUrl;

	function __construct() {
		$this->pluginDirUrl = plugin_dir_url( __FILE__ );

		add_action( 'wp_enqueue_scripts', [ $this, 'scripts' ], 999 );

		add_filter( 'wppopups/rules/options', [ $this, 'ruleOptions' ] );
		add_filter( 'wppopups/rules/operators', [ $this, 'ruleOperators' ], 10, 2 );
		add_filter( 'wppopups/rules/field_type', [ $this, 'ruleValueType' ], 10, 2 );

		add_filter( 'wppopups_rules/rule_values/wpz-edd-has-purchased', [ $this, 'choicesDownloads' ] );
		add_filter( 'wppopups_rules/rule_values/wpz-edd-has-in-cart', [ $this, 'choicesDownloads' ] );

		add_filter( 'wppopups_rules_rule_match_wpz-edd-cart-count', [ $this, 'checkCartCount' ] );
		add_filter( 'wppopups_rules_rule_match_wpz-edd-has-purchased', [ $this, 'checkHasPurchased' ] );
		add_filter( 'wppopups_rules_rule_match_wpz-edd-has-in-cart', [ $this, 'checkHasInCart' ] );
		add_filter( 'wppopups_rules_rule_match_wpz-edd-purchases-count', [ $this, 'checkPurchasesCount' ] );
		add_filter( 'wppopups_rules_rule_match_wpz-edd-purchases-total', [ $this, 'checkPurchasesTotal' ] );

		add_filter( 'wppopups/triggers/options', [ $this, 'triggerOptions' ] );
	}

	function scripts() {
		wp_enqueue_script( 'wpz-edd-wppopups', $this->pluginDirUrl . 'js/frontend.js', [ 'jquery' ], self::VERSION, ! function_exists( 'edd_scripts_in_footer' ) || edd_scripts_in_footer() );
		$this->addScriptDependency( 'edd-ajax', 'wpz-edd-wppopups' );
	}

	protected function addScriptDependency( $scriptId, $dependency ) {
		global $wp_scripts;
		if ( isset( $wp_scripts->registered[ $scriptId ] ) ) {
			if ( empty( $wp_scripts->registered[ $scriptId ]->deps ) ) {
				$wp_scripts->registered[ $scriptId ]->deps = [];
			}
			$wp_scripts->registered[ $scriptId ]->deps[] = $dependency;
		}
	}

	function ruleOptions( $options ) {
		$options[ __( 'Easy Digital Downloads', 'popups-for-easy-digital-downloads' ) ] = $this->getRuleOptions();

		return $options;
	}

	function getRuleOptions() {
		return [
			'wpz-edd-has-purchased'   => __( 'User has purchased product', 'popups-for-easy-digital-downloads' ),
			'wpz-edd-has-in-cart'     => __( 'User has product in cart', 'popups-for-easy-digital-downloads' ),
			'wpz-edd-cart-count'      => __( 'User cart product count', 'popups-for-easy-digital-downloads' ),
			'wpz-edd-purchases-count' => __( 'User purchase count', 'popups-for-easy-digital-downloads' ),
			'wpz-edd-purchases-total' => __( 'User purchase total', 'popups-for-easy-digital-downloads' ),
		];
	}

	function triggerOptions( $options ) {
		$options['wpz-edd-cart-add-pre'] = __( 'Easy Digital Downloads - before add to cart', 'popups-for-easy-digital-downloads' );
		$options['wpz-edd-cart-add']     = __( 'Easy Digital Downloads - added to cart', 'popups-for-easy-digital-downloads' );

		return $options;
	}

	function ruleOperators( $operators, $ruleId ) {
		switch ( $ruleId ) {
			case 'wpz-edd-cart-count':
			case 'wpz-edd-purchases-count':
			case 'wpz-edd-purchases-total':
				return [
					'==' => __( 'equals', 'popups-for-easy-digital-downloads' ),
					'!=' => __( 'does not equal', 'popups-for-easy-digital-downloads' ),
					'>'  => __( 'greater than', 'popups-for-easy-digital-downloads' ),
					'>=' => __( 'greater than or equals', 'popups-for-easy-digital-downloads' ),
					'<'  => __( 'less than', 'popups-for-easy-digital-downloads' ),
					'<=' => __( 'less than or equals', 'popups-for-easy-digital-downloads' ),
				];
			case 'wpz-edd-has-purchased':
			case 'wpz-edd-has-in-cart':
				return [
					'==' => __( 'equals', 'popups-for-easy-digital-downloads' ),
				];
		}

		return $operators;
	}

	function ruleValueType( $valueType, $ruleId ) {
		switch ( $ruleId ) {
			case 'wpz-edd-cart-count':
			case 'wpz-edd-purchases-count':
			case 'wpz-edd-purchases-total':
				return 'number';
			case 'wpz-edd-has-purchased':
			case 'wpz-edd-has-in-cart':
				return 'select';
		}

		return $valueType;
	}

	function choicesDownloads( $choices ) {
		return array_column( get_posts( [
			'post_type'           => 'download',
			'nopaging'            => true,
			'ignore_sticky_posts' => true,
			'order'               => 'ASC',
			'orderby'             => 'title'
		] ), 'post_title', 'ID' );
	}

	protected function numericCheck( $currentValue, $compareValue, $operator ) {
		$currentValue = (float) $currentValue;
		$compareValue = (float) $compareValue;
		switch ( $operator ) {
			case '==':
				return $currentValue == $compareValue;
			case '!=':
				return $currentValue != $compareValue;
			case '>':
				return $currentValue > $compareValue;
			case '>=':
				return $currentValue >= $compareValue;
			case '<':
				return $currentValue < $compareValue;
			case '<=':
				return $currentValue <= $compareValue;
		}

		return false;
	}

	function checkCartCount( $rule ) {
		return function_exists( 'edd_get_cart_contents' ) && $this->numericCheck( count( edd_get_cart_contents() ), $rule['value'], $rule['operator'] );
	}

	function checkHasPurchased( $rule ) {
		return function_exists( 'edd_has_user_purchased' ) && edd_has_user_purchased( get_current_user_id(), [ (int) $rule['value'] ] );
	}

	function checkHasInCart( $rule ) {
		return function_exists( 'edd_item_in_cart' ) && edd_item_in_cart( (int) $rule['value'] );
	}

	function checkPurchasesCount( $rule ) {
		return function_exists( 'edd_count_purchases_of_customer' ) && $this->numericCheck( edd_count_purchases_of_customer(), $rule['value'], $rule['operator'] );
	}

	function checkPurchasesTotal( $rule ) {
		return function_exists( 'edd_purchase_total_of_user' ) && $this->numericCheck( edd_purchase_total_of_user( get_current_user_id() ), $rule['value'], $rule['operator'] );
	}

}

// Following lines for Pro only
require_once( __DIR__ . '/includes/pro.php' );
new WPZ_WPPop_EDD_Pro();

// Following line for Free only (uncommented)
// new WPZ_WPPop_EDD();