<?php

/**
 * @file plugins/generic/registerPage/includes/insertReviewerData.inc.php
 *
 * Copyright (c) 2016 Language Science Press
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * short code to insert reviewer data to database
 */
	$groupId = $registerPageDAO->getUserGroupIdByName('Reviewer',$contextId);

	if ($groupId) {
		$registerPageDAO->insertUserGroup($groupId,$userId);
		$registerPageDAO->insertArea($userId,1,$area1);
		if (!empty($area2)) {$registerPageDAO->insertArea($userId,2,$area2);}
		if (!empty($area3)) {$registerPageDAO->insertArea($userId,3,$area3);}
		if ($issetCheckboxEnglish) {$registerPageDAO->insertLanguage($userId,"English");}
		if ($issetCheckboxGerman) {$registerPageDAO->insertLanguage($userId,"German");}
		if ($issetCheckboxFrench) {$registerPageDAO->insertLanguage($userId,"French");}
		if ($issetCheckboxOther) {$registerPageDAO->insertLanguage($userId,$otherLanguage);}

	}

?>
