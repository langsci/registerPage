<?php

/**
 * @file plugins/generic/registerPage/RegisterPagePlugin.inc.php
 *
 * Copyright (c) 2016 Language Science Press
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class RegisterPagePlugin
 *
 */


import('lib.pkp.classes.plugins.GenericPlugin');

class RegisterPagePlugin extends GenericPlugin {


	function register($category, $path) {
			
		
		if (parent::register($category, $path)) {
			$this->addLocaleData();
			
			if ($this->getEnabled()) {
				HookRegistry::register ('LoadHandler', array(&$this, 'handleRegisterPageRequest'));
			}
			return true;
		}
		return false;
	}

	function handleRegisterPageRequest($hookName, $args) {

		$request = $this->getRequest();
		$templateMgr = TemplateManager::getManager($request);

		$page = $args[0];
		$op = $args[1];

		if ($page=='user' && in_array($op,
				  array('register', 
						'validation',
						'verifyEmail',
						'saveSubscriptions',
						'subscriptions'))) {

			define('REGISTERPAGE_PLUGIN_NAME', $this->getName());
			define('HANDLER_CLASS', 'RegisterPageHandler');

			$this->import('RegisterPageHandler');

			return true;
		}
		return false;
	}

	function getDisplayName() {
		return __('plugins.generic.registerPage.displayName');
	}

	function getDescription() {
		return __('plugins.generic.registerPage.description');
	}

	function getTemplatePath() {
		return parent::getTemplatePath() . 'templates/';
	}

	function getInstallSchemaFile() {
		return $this->getPluginPath() . '/schema.xml';
	}

}

?>
