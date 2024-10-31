<?php

class WPZ_WPPop_EDD_Pro extends  WPZ_WPPop_EDD {
	
	function __construct() {
		parent::__construct();
		
		add_filter('wppopups_rules/rule_values/wpz-edd-has-active-key', [$this, 'choicesDownloads']);
		add_filter('wppopups_rules/rule_values/wpz-edd-no-active-key', [$this, 'choicesDownloads']);
		
		add_filter('wppopups_rules_rule_match_wpz-edd-has-expiring-keys', [$this, 'checkHasExpiringKeys']);
		add_filter('wppopups_rules_rule_match_wpz-edd-has-active-key', [$this, 'checkHasActiveKey']);
		add_filter('wppopups_rules_rule_match_wpz-edd-no-active-key', [$this, 'checkNoActiveKey']);
	}
	
	function scripts() {
		parent::scripts();
		wp_enqueue_script('wpz-edd-wppopups-pro', $this->pluginDirUrl.'js/frontend-pro.js', ['wpz-edd-wppopups'], self::VERSION, !function_exists('edd_scripts_in_footer') || edd_scripts_in_footer());
	}
	
	function getRuleOptions() {
		return array_merge(
			parent::getRuleOptions(),
			[
				'wpz-edd-has-active-key' => __('User has active license key for product', 'popups-for-easy-digital-downloads'),
				'wpz-edd-no-active-key' => __('User does not have active license key for product', 'popups-for-easy-digital-downloads'),
				'wpz-edd-has-expiring-keys' => __('User has license key(s) expiring within (days)', 'popups-for-easy-digital-downloads'),
			]
		);
	}
	
	function triggerOptions($options) {
		$options = parent::triggerOptions($options);
		$options['wpz-edd-subscription-cancel-pre'] = __('Easy Digital Downloads - before subscription cancellation', 'popups-for-easy-digital-downloads');
		return $options;
	}
	
	function ruleOperators($operators, $ruleId) {
		switch ($ruleId) {
			case 'wpz-edd-has-expiring-keys':
				return [
					'==' => __('equals', 'popups-for-easy-digital-downloads'),
					'!=' => __('does not equal', 'popups-for-easy-digital-downloads'),
					'>' => __('greater than', 'popups-for-easy-digital-downloads'),
					'>=' => __('greater than or equals', 'popups-for-easy-digital-downloads'),
					'<' => __('less than', 'popups-for-easy-digital-downloads'),
					'<=' => __('less than or equals', 'popups-for-easy-digital-downloads'),
				];
			case 'wpz-edd-has-active-key':
			case 'wpz-edd-no-active-key':
				return [
					'==' => __('equals', 'popups-for-easy-digital-downloads'),
				];
		}
		return parent::ruleOperators($operators, $ruleId);
	}
	
	function ruleValueType($valueType, $ruleId) {
		switch ($ruleId) {
			case 'wpz-edd-has-expiring-keys':
				return 'number';
			case 'wpz-edd-has-active-key':
			case 'wpz-edd-no-active-key':
				return 'select';
		}
		return parent::ruleValueType($valueType, $ruleId);
	}
	
	function checkHasExpiringKeys($rule) {
		if (function_exists('edd_software_licensing')) {
			$keys = edd_software_licensing()->get_license_keys_of_user();
			foreach ($keys as $key) {
				if (($key->status == 'active' || $key->status == 'inactive')
						&& $this->numericCheck(($key->expiration - current_time('timestamp')) / 86400, $rule['value'], $rule['operator'])) {
					return true;
				}
			}
		}
		return false;
	}
	
	function checkHasActiveKey($rule) {
		return function_exists('edd_software_licensing')
				&& edd_software_licensing()->get_license_keys_of_user(0, (int) $rule['value'], 'active')
				&& edd_software_licensing()->get_license_keys_of_user(0, (int) $rule['value'], 'inactive');
	}
	
	function checkNoActiveKey($rule) {
		return !$this->checkHasActiveKey($rule);
	}
}