<?php

/**
 * @file plugins/generic/registerPage/includes/insertSubscriptions.inc.php
 *
 * Copyright (c) 2016 Language Science Press
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * short code to insert subscription data to database
 */

		if ($issetCheckboxNewsletter) {
			$registerPageDAO->insertSubscription($userId,'Newsletter');
		}
		if ($issetCheckboxML) {
			$registerPageDAO->insertSubscription($userId,'General Mailing List');
		}

		$userGroupNames = array('Supporter','Reader','Proofreader','Typesetter','Author','Volume Editor','Illustrator');

		foreach ($userGroupNames as $groupName) {
			$groupId = $registerPageDAO->getUserGroupIdByName($groupName,$contextId);
			$variableName  = 'issetCheckbox' . str_replace(' ', '', $groupName);
			$checkbox = $$variableName;
			if ($checkbox && $groupId) {
				$registerPageDAO->insertUserGroup($groupId,$userId);
			}
		}

?>
