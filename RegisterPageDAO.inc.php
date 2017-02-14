<?php

/**
 * @file plugins/generic/registerPage/RegisterPageDAO.inc.php
 *
 * Copyright (c) 2016 Language Science Press
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class RegisterPageDAO
 *
 */

class RegisterPageDAO extends DAO {
	/**
	 * Constructor
	 */
	function RegisterPageDAO() {
		parent::DAO();
	}

	function existsTable($table) {

		$result = $this->retrieve(
			"SHOW TABLES LIKE '".$table."'"
		);

		if ($result->RecordCount() == 0) {
			$result->Close();
			return false;
		} else {
			$result->Close();
			return true;
		}
	}

	function getUserIdByUsername($username) {
		$result = $this->retrieve(
			"SELECT user_id FROM users WHERE username = '". $username ."'"			
		);

		if ($result->RecordCount() == 0) {
			$result->Close();
			return null;
		} else {
			$row = $result->getRowAssoc(false);
			$result->Close();
			return $this->convertFromDB($row['user_id'],null);
		}
	}

	/**
	 * Retrieve all usernames
	 * @return array
	 */
	function getUsernames() {
		$users = array();

		$result = $this->retrieve(
			'SELECT username FROM users;'
		);

		if ($result->RecordCount() == 0) {
			$returner = null;
			$result->Close();
			return $returner;

		} else {
			while (!$result->EOF) {
				$row = $result->getRowAssoc(false);
				$value = $this->convertFromDB($row['username'],null);
				$users[] = $value;
				$result->MoveNext();
			}
			$result->Close();

			return $users;
		}
	}

	/**
	 * Retrieve all emails
	 * @return array
	 */
	function getEmails() {
		$emails = array();

		$result = $this->retrieve(
			'SELECT email FROM users;'
		);

		if ($result->RecordCount() == 0) {
			$returner = null;
			$result->Close();
			return $returner;

		} else {
			while (!$result->EOF) {
				$row = $result->getRowAssoc(false);
				$value = $this->convertFromDB($row['email'],null);
				$emails[] = $value;
				$result->MoveNext();
			}
			$result->Close();

			return $emails;
		}
	}

	/**
	 * Retrieve user_group_id by user group name
	 * @return user_group_id
	 */

	function getUserGroupIdByName($user_group_name,$context_id) {

		$result = $this->retrieve(
			'select b.user_group_id, s.setting_value from user_groups b left join user_group_settings s on
			b.user_group_id=s.user_group_id where s.setting_name="name" and s.setting_value="'.$user_group_name.'"
			and b.context_id='.$context_id
		);
		if ($result->RecordCount() == 0) {
			$result->Close();
			return null;
		} else {
			$row = $result->getRowAssoc(false);
			$result->Close();
			return $this->convertFromDB($row['user_group_id'],null);
		}	
	}


	/**
	 * Retrieve date_registered by user_id
	 * @return date_registered
	 */
	function getDateRegistered($user_id) {

		$result = $this->retrieve(
			'SELECT date_registered FROM users WHERE user_id='.$user_id
		);

		if ($result->RecordCount() == 0) {
			$returner = null;
			$result->Close();
			return $returner;

		} else {
			$row = $result->getRowAssoc(false);
			$dateRegistered = $this->convertFromDB($row['date_registered'],null);
			$result->Close();

			return $dateRegistered;
		}
	}

	/**
	 * Retrieve email from tables users by user_id
	 * @return email
	 */
	function getEmail($user_id) {

		$result = $this->retrieve(
			'SELECT email FROM users WHERE user_id='.$user_id
		);

		if ($result->RecordCount() == 0) {
			$returner = null;
			$result->Close();
			return $returner;

		} else {
			$row = $result->getRowAssoc(false);
			$email = $this->convertFromDB($row['email'],null);
			$result->Close();

			return $email;
		}
	}

	function getUserGroups($username) {

		$result = $this->retrieve(
			'SELECT setting_value FROM user_group_settings WHERE locale = "en_US" AND setting_name="name" AND
			user_group_id IN (SELECT user_group_id FROM user_user_groups WHERE user_id=(select user_id from users where username="'.$username.'"))'
		);

		if ($result->RecordCount() == 0) {
			$result->Close();
			return array();
		} else {
			$userGroups = array();
			while (!$result->EOF) {
				$row = $result->getRowAssoc(false);
				$userGroups[]= $this->convertFromDB($row['setting_value'],null);				 
				$result->MoveNext();
			}
			$result->Close();
			return $userGroups;
		}
	}

	function getSubscriptions($username) {

		$result = $this->retrieve(
			'SELECT subscription FROM langsci_user_subscriptions WHERE user_id=(SELECT user_id FROM users WHERE username="'.$username.'")'
		);

		if ($result->RecordCount() == 0) {
			$result->Close();
			return array();
		} else {
			$subscriptions = array();
			while (!$result->EOF) {
				$row = $result->getRowAssoc(false);
				$subscriptions[]= $this->convertFromDB($row['subscription'],null);				 
				$result->MoveNext();
			}
			$result->Close();
			return $subscriptions;
		}
	}

	function getReviewAreas($username) {

		$result = $this->retrieve(
			'SELECT reviewarea FROM langsci_user_reviewareas WHERE user_id=(SELECT user_id FROM users WHERE username="'.$username.'")'
		);

		if ($result->RecordCount() == 0) {
			$returner = null;
			$result->Close();
			return $returner;
		} else {
			$reviewAreas = array();
			while (!$result->EOF) {
				$row = $result->getRowAssoc(false);
				$reviewAreas[]= $this->convertFromDB($row['reviewarea'],null);				 
				$result->MoveNext();
			}
			$result->Close();
			return $reviewAreas;
		}
	}

	function getReviewLanguages($username) {

		$result = $this->retrieve(
			'SELECT reviewlanguage FROM langsci_user_reviewlanguages WHERE user_id=(SELECT user_id FROM users WHERE username="'.$username.'")'
		);

		if ($result->RecordCount() == 0) {
			$returner = null;
			$result->Close();
			return $returner;
		} else {
			$reviewLanguages = array();
			while (!$result->EOF) {
				$row = $result->getRowAssoc(false);
				$reviewLanguages[]= $this->convertFromDB($row['reviewlanguage'],null);				 
				$result->MoveNext();
			}
			$result->Close();
			return $reviewLanguages;
		}
	}

	function insertWebsiteSetting($user_id,$setting) {

		$result = $this->update(
				"INSERT INTO langsci_website_settings VALUES(".$user_id.",'".$setting."','true')");
	}

	function insertAffiliation($nextUserId,$affiliation,$locale) {

		$this->update(
			'INSERT INTO user_settings values('.$nextUserId.',"'.$locale.'","affiliation",0,0,"'.$affiliation.'","string")'
		);
	}

	function insertSubscription($userId,$type) {

		$this->update(
			'INSERT INTO langsci_user_subscriptions values('.$userId.',"'.$type.'")'
		);
	}

	function insertArea($userId,$rank,$area) {

		$this->update(
			'INSERT INTO langsci_user_reviewareas values('.$userId.','.$rank.',"'.$area.'")'
		);
	}

	function insertLanguage($userId,$language) {

		$this->update(
			'INSERT INTO langsci_user_reviewlanguages values('.$userId.',"'.$language.'")'
		);
	}

	function insertUser($username,$password,$firstName,$lastName,$academic_title,$email,$userUrl) {

		$this->update(
			'INSERT INTO users (username, password, first_name, last_name, salutation,email, url, date_registered,disabled)
				values("'.$username.'","'.$password.'","'.$firstName.'","'.$lastName.'","'.$academic_title.'","'.$email.'","'.$userUrl.'",NOW(),0)'
		);	
	}

	function insertUserGroup($userGroupId,$userId) {

		$this->update(
			'INSERT INTO user_user_groups VALUES ('.$userGroupId.','.$userId.')'
		);
	}

	function setDisabled($disabled,$disabledReason,$userId) {

		$this->update(
			'UPDATE users SET disabled='.$disabled. ',disabled_reason="'.$disabledReason.'" WHERE user_id='.$userId
		);
	}

	function deleteUserGroups($userId,$userGroups) {

		$this->update(
			'DELETE FROM user_user_groups WHERE user_id='.$userId.' AND user_group_id IN (SELECT user_group_id FROM user_group_settings WHERE locale="en_US" AND setting_value IN ('.$userGroups.'))'
		);
	}

	function deleteSubscriptions($userId){

		$this->update(
			'DELETE FROM langsci_user_subscriptions WHERE user_id='.$userId
		);
	}

	function deleteReviewer($userId,$groupId){

		$this->update(
			'DELETE FROM user_user_groups WHERE user_id='.$userId.' AND user_group_id ='.$groupId
		);

		$this->update(
			'DELETE FROM langsci_user_reviewareas WHERE user_id='.$userId
		);

		$this->update(
			'DELETE FROM langsci_user_reviewlanguages WHERE user_id='.$userId
		);

	}
}

?>
