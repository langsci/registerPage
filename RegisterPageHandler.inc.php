<?php

/**
 * @file plugins/generic/registerPage/RegisterPageHandler.inc.php
 *
 * Copyright (c) 2016 Language Science Press
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class RegisterPageHandler
 *
 */

import('classes.handler.Handler');
import('plugins.generic.registerPage.RegisterPageDAO');
import('lib.pkp.classes.form.Form');
require('lib/pkp/lib/vendor/phpmailer/phpmailer/class.phpmailer.php');

class RegisterPageHandler extends Handler {

	function register($args, $request) {

		include('includes/data.inc.php');
		$templateMgr = TemplateManager::getManager($request);
		$registerPagePlugin = PluginRegistry::getPlugin('generic', REGISTERPAGE_PLUGIN_NAME);

		// randomly select a captcha item
		$captchaItem = rand(0,count($captchaQuestions)-1);

		//////////////////////////////////////////////////////////////
		///// assign all variables needed in the register page ///////
		//////////////////////////////////////////////////////////////

		$templateMgr->assign('pageTitle', 'plugins.generic.registerPage.title');
		// assign user groups
		$templateMgr->assign('issetCheckboxSupporter',false);
		$templateMgr->assign('issetCheckboxIllustrator',false);
		$templateMgr->assign('issetCheckboxReader',false);
		$templateMgr->assign('issetCheckboxReviewer',false);
		$templateMgr->assign('issetCheckboxProofreader',false);
		$templateMgr->assign('issetCheckboxTypesetter',false);
		// reviewer details
		$templateMgr->assign('areas',$areas);
		$templateMgr->assign('checkboxEnglish',false);
		$templateMgr->assign('checkboxGerman',false);
		$templateMgr->assign('checkboxFrench',false);
		$templateMgr->assign('checkboxOther',false);
		// newsletter and mailing list
		$templateMgr->assign('issetCheckboxNewsletter',false);
		$templateMgr->assign('issetCheckboxML',false);
		// captcha questions
		$templateMgr->assign('captchaQuestion',$captchaQuestions[$captchaItem]);
		$templateMgr->assign('captchaSolution',$captchaSolutions[$captchaItem]);
		$templateMgr->assign('captchaCorrect',false);
		$templateMgr->assign('displayCaptchaRequired',false);
		// password variables
		$templateMgr->assign('emptyPassword',false);
		$templateMgr->assign('emptyPassword2',false);
		$templateMgr->assign('passwordsMatch',true);
		$templateMgr->assign('password6Characters',true);

		// validities of other variables
		$templateMgr->assign('emptyEmail',false);
		$templateMgr->assign('emailValid',true);
		$templateMgr->assign('usernameAlphanumeric',true);
		$templateMgr->assign('usernameUnique',true);
		$templateMgr->assign('urlOkay',true);
		$templateMgr->assign('languageOkay',true);
		$templateMgr->assign('emailUnique',true);

		$templateMgr->assign('issetCheckboxConfirmation',false);
		$templateMgr->assign('baseUrl', $request->getBaseUrl());	
		$templateMgr->display($registerPagePlugin->getTemplatePath().'register.tpl');	
	}


	function validation($args, $request) {

		include('includes/data.inc.php');
		$templateMgr = TemplateManager::getManager($request);
		$registerPagePlugin = PluginRegistry::getPlugin('generic', REGISTERPAGE_PLUGIN_NAME);
		$context = $request->getContext();
		$contextId = $context->getId();
		$locale = AppLocale::getLocale();

		//////////////////////////////////////////////
		// get variables /////////////////////////////
		//////////////////////////////////////////////

		include('includes/getSubscriptionData.inc.php');

		$academicTitle = $_POST['inputAcademicTitle'];
		$firstName = $_POST['inputFirstName'];
		$lastName = $_POST['inputLastName'];
		$username = $_POST['inputUsername'];
		$password = $_POST['inputPassword'];
		$password2 = $_POST['inputPassword2'];
		$affiliation = $_POST['inputAffiliation'];
		$email = $_POST['inputEmail'];
		$userUrl = $_POST['inputUserUrl'];
		$captchaAnswer = $_POST['inputCaptcha'];
		$captchaQuestion = $_POST['captchaQuestion'];
		$captchaSolution = $_POST['captchaSolution'];
		$issetCheckboxCaptchaCorrect = isset($_POST['checkboxCaptchaCorrect']);
		$issetCheckboxConfirmation = isset($_POST['checkboxConfirmation']);

		// required fields
		$emptyAcademicTitle = empty($academicTitle);
		$emptyFirstName = empty($firstName);
		$emptyLastName = empty($lastName);
		$emptyPassword = empty($password);
		$emptyPassword2 = empty($password2);
		$emptyUsername = empty($username);
		$emptyAffiliation = empty($affiliation);
		$emptyEmail = empty($email);
		$emptyUserUrl = empty($userUrl);

		// get validation variables
		$passwordsMatch = $password==$password2;
		$password6Characters = strlen($password)>5;

		$usernameAlphanumeric = false;  
		if (preg_match('/^[a-z0-9_]+$/',$username)){
			$usernameAlphanumeric = true; 
		} 

		$usernameUnique = false; 
		$registerPageDAO = new RegisterPageDAO;
		$ompUsernames = $registerPageDAO->getUsernames();
		if (!in_array ($username, $ompUsernames)) {
			$usernameUnique = true;
		}

		$emailUnique = false;
		$ompEmails = $registerPageDAO->getEmails();
		if (!in_array ($email, $ompEmails)) {
			$emailUnique = true;
		} 

		$emailValid = false;
		if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
  			$emailValid = true;
		}

		$urlOkay = true;
		/* url validation removed
		$urlOkay = false;
		if (filter_var($userUrl, FILTER_VALIDATE_URL)||$emptyUserUrl) {
  			$urlOkay = true;
		} */

		$captchaCorrect = $issetCheckboxCaptchaCorrect || $captchaAnswer==$captchaSolution;

		////////////////////////////////////////////////////////////////////////////////
		// validity check (no -> repeat register.tpl, yes -> write data to database) ///
		////////////////////////////////////////////////////////////////////////////////

		if ($emptyAcademicTitle||$emptyFirstName||$emptyLastName||$emptyPassword||$emptyPassword2||
			 $emptyUsername||$emptyAffiliation||$emptyEmail||!$passwordsMatch||!$password6Characters||!$emailUnique||
			 !$usernameAlphanumeric||!$usernameUnique||!$emailValid||!$urlOkay||!$areaOkay||!$languageOkay||!$captchaCorrect) {

			///////////////////////////////////////////////////////
			/// repeat register.tpl ///////////////////////////////
			///////////////////////////////////////////////////////


			// assign variables
			include('includes/assignSubscriptionData.inc.php');
			$templateMgr->assign('pageTitle', 'plugins.generic.registerPage.title');
			$templateMgr->assign('academicTitle',$academicTitle);
			$templateMgr->assign('firstName',$firstName);
			$templateMgr->assign('lastName',$lastName);
			$templateMgr->assign('username',$username);
			$templateMgr->assign('password',$password);
			$templateMgr->assign('password2',$password2);
			$templateMgr->assign('affiliation',$affiliation);
			$templateMgr->assign('email',$email);
			$templateMgr->assign('userUrl',$userUrl);
			
			$templateMgr->assign('emptyAcademicTitle',$emptyAcademicTitle);
			$templateMgr->assign('emptyFirstName',$emptyFirstName);
			$templateMgr->assign('emptyLastName',$emptyLastName);
			$templateMgr->assign('emptyPassword',$emptyPassword);
			$templateMgr->assign('emptyPassword2',$emptyPassword2);
			$templateMgr->assign('emptyUsername',$emptyUsername);
			$templateMgr->assign('emptyAffiliation',$emptyAffiliation);
			$templateMgr->assign('emptyEmail',$emptyEmail);
			$templateMgr->assign('emptyOtherLanguages',$emptyArea1);

			$templateMgr->assign('passwordsMatch',$passwordsMatch);
			$templateMgr->assign('password6Characters',$password6Characters);
			$templateMgr->assign('usernameAlphanumeric',$usernameAlphanumeric);
			$templateMgr->assign('usernameUnique',$usernameUnique);
			$templateMgr->assign('emailValid',$emailValid);
			$templateMgr->assign('emailUnique',$emailUnique);
			$templateMgr->assign('urlOkay',$urlOkay);
			$templateMgr->assign('areaOkay',$areaOkay);
			$templateMgr->assign('languageOkay',$languageOkay);
			$templateMgr->assign('captchaCorrect',$captchaCorrect);
			$templateMgr->assign('captchaQuestion',$captchaQuestion);
			$templateMgr->assign('captchaSolution',$captchaSolution);
			$templateMgr->assign('displayCaptchaRequired',true);
			$templateMgr->assign('displayCaptcha',false);

			$templateMgr->assign('issetCheckboxSupporter',$issetCheckboxSupporter);
			$templateMgr->assign('issetCheckboxIllustrator',$issetCheckboxIllustrator);
			$templateMgr->assign('issetCheckboxReader',$issetCheckboxReader);
			$templateMgr->assign('issetCheckboxAuthor',$issetCheckboxAuthor);
			$templateMgr->assign('issetCheckboxVolumeEditor',$issetCheckboxVolumeEditor);

			$templateMgr->assign('issetCheckboxReviewer',$issetCheckboxReviewer);
			$templateMgr->assign('issetCheckboxProofreader',$issetCheckboxProofreader);
			$templateMgr->assign('issetCheckboxTypesetter',$issetCheckboxTypesetter);
			$templateMgr->assign('issetCheckboxNewsletter',$issetCheckboxNewsletter);
			$templateMgr->assign('issetCheckboxML',$issetCheckboxML);

			$templateMgr->assign('issetCheckboxConfirmation',$issetCheckboxConfirmation);

			include('includes/assignSubscriptionData.inc.php');
			$templateMgr->display($registerPagePlugin->getTemplatePath().'register.tpl');	

		} else {

			///////////////////////////////////////////////////////////////////////
			/// insert user into database /////////////////////////////////////////
			///////////////////////////////////////////////////////////////////////


			// get user id
			$registerPageDAO = new RegisterPageDAO;
			$userId = $registerPageDAO->getNextUserId();

			// default: user is included in the hall of fame and has a public profile
			if ($registerPageDAO->existsTable('langsci_website_settings')) {
				$registerPageDAO->insertWebsiteSetting($userId,'HallOfFame');
				$registerPageDAO->insertWebsiteSetting($userId,'PublicProfile');
			}

			// add data to table 'users'
			$registerPageDAO ->insertUser($userId,$username,md5($username.$password),$firstName,$lastName,$academicTitle,$email,$userUrl);

			// add affiliation to table 'user_settings'
			$registerPageDAO->insertAffiliation($userId,$affiliation,$locale);

			include('includes/insertSubscriptions.inc.php');

			// reviewer 
			$groupId = $registerPageDAO->getUserGroupIdByName('Reviewer',$contextId);
			if ($groupId && $issetCheckboxReviewer) {
				$registerPageDAO->insertUserGroup($groupId,$userId);
				$registerPageDAO->insertArea($userId,1,$area1);
				if (!empty($area2)) {$registerPageDAO->insertArea($userId,2,$area2);}
				if (!empty($area3)) {$registerPageDAO->insertArea($userId,3,$area3);}
				if ($issetCheckboxEnglish) {$registerPageDAO->insertLanguage($userId,"English");}
				if ($issetCheckboxGerman) {$registerPageDAO->insertLanguage($userId,"German");}
				if ($issetCheckboxFrench) {$registerPageDAO->insertLanguage($userId,"French");}
				if ($issetCheckboxOther) {$registerPageDAO->insertLanguage($userId,$otherLanguage);}
			}	

			$templateMgr->assign('pageTitle', 'plugins.generic.registerPage.title');
			$templateMgr->assign('content', __('plugins.generic.registerPage.finished'));
			$templateMgr->display($registerPagePlugin->getTemplatePath().'registered.tpl');	


			/////////////////////////////////////////////////////////
			// send confirmation mail ///////////////////////////////
			/////////////////////////////////////////////////////////


			if ($issetCheckboxConfirmation) {
				$confmail = new PHPMailer();
	 			$confmail->CharSet  =  "utf-8";
 				$confmail->From = "noreply@langsci-press.org";	// sender address
  				$confmail->FromName = "Language Science Press";				// sender name	
  				$confmail->AddAddress($email);								// recipient address
  				$confmail->Subject = "Your login data for Language Science Press";
 				$confmail->Body = "Dear " .$firstName. " ".$lastName.", \r\n\r\nThank you for registering with Language Science Press!
									\r\nYour username is: " . $username . "\r\nYour password is: " . $password .
									"\r\n\r\nSincerely,\r\nLanguage Science Press";
				$confmail->Send();
			} 

			// email verification /*todo*/
			// url must work for LangSci urls and local urls 			
			/*$host = "http://$_SERVER[HTTP_HOST]";
			$shortUrl = substr($_SERVER[REQUEST_URI],1,strpos($_SERVER[REQUEST_URI], 'user')-1);
			$nativeUrl = substr($shortUrl ,0,strpos($shortUrl, '/'));
			if (!$nativeUrl=="") {
				$nativeUrl = $nativeUrl . "/";
			}*/

	 		$hostx = "http://$_SERVER[HTTP_HOST]";
			$shortUrlx = substr($_SERVER['REQUEST_URI'],1,strpos($_SERVER['REQUEST_URI'],'user')-1);
			$pathx = $hostx ."/".$shortUrlx;
	
			$registerPageDAO->setDisabled(1,"account not activated",$userId);
			$activationCode = md5($email . $registerPageDAO->getDateRegistered($userId));
			$activationRef = $pathx. "user/verifyEmail?key=".$activationCode."&id=".$userId;
			$verificationmail = new PHPMailer();
	 		$verificationmail->CharSet  =  "utf-8";
 			$verificationmail->From = "noreply@langsci-press.org";	// sender address
  			$verificationmail->FromName = "Language Science Press";				// sender name	
  			$verificationmail->AddAddress($email);								// recipient address
  			$verificationmail->Subject = "Please confirm your registration with Languages Science Press";
			$verificationmail->IsHTML(true); 
 			$verificationmail->Body = "<p>Dear " .$firstName. " ".$lastName.",</p><p>To activate your account with Language Science Press, 
												click the link below:</p><p><a href='".$activationRef."'>".$activationRef."</a></p>
												<p>Sincerely,<br>Language Science Press</p>";
			$verificationmail->Send();

		}
	}

	function verifyEmail($args, $request)	{

		$userId = $_GET['id'];
		$key = $_GET['key'];


		$templateMgr = TemplateManager::getManager($request);
		$registerPagePlugin = PluginRegistry::getPlugin('generic', REGISTERPAGE_PLUGIN_NAME);

		if (isset($key)&&isset($userId)) {

			$registerPageDAO = new RegisterPageDAO;
			$keyCompare = md5($registerPageDAO->getEmail($userId).$registerPageDAO->getDateRegistered($userId));

			if ($key==$keyCompare) {
				$registerPageDAO->setDisabled(0,"",$userId);
				$templateMgr->assign('content', __('plugins.generic.registerPage.accountActivated'));
			} else {
				$templateMgr->assign('content', __('plugins.generic.registerPage.activationFailed'));
			}	

		} else {
			$templateMgr->assign('content', __('plugins.generic.registerPage.activationFailed'));
		}

		$templateMgr->display($registerPagePlugin->getTemplatePath().'registered.tpl');	
	}

	function subscriptions($args, $request) {

		$userRoles = $this->getAuthorizedContextObject(ASSOC_TYPE_USER_ROLES);

		// redirect to index page if no user is logged in 
		$user = $request->getUser();
		if (!$user) {
			$request->redirect('index');
		}
		$username = $user-> getUsername();	
		$userId = $user->getId();

		include('includes/data.inc.php');

		$registerPageDAO = new RegisterPageDAO;

		$userGroups = $registerPageDAO->getUserGroups($username);
		$subscriptions = $registerPageDAO->getSubscriptions($username);
		$reviewAreas = $registerPageDAO->getReviewAreas($username);
		$reviewLanguages = $registerPageDAO->getReviewLanguages($username);

		$area1 = $reviewAreas[0];
		$area2 = $reviewAreas[1];
		$area3 = $reviewAreas[2];

		$issetCheckboxEnglish = false;
		$issetCheckboxGerman = false;
		$issetCheckboxFrench = false;
		$issetCheckboxOther = false;
		$otherLanguage = "";

		for ($i=0; $i<=sizeof($reviewLanguages)-1; $i++) {
			switch($reviewLanguages[$i]) {
  				case ("English"):
  					$issetCheckboxEnglish = true;
  					break;
  				case ("German"):
  					$issetCheckboxGerman = true;
  					break;
  				case ("French"):
  					$issetCheckboxFrench = true;
  					break;			
				default:
					$issetCheckboxOther  = true;
					$otherLanguage = $reviewLanguages[$i];					
					break;
			}
		} 
		$languageOkay=true;
		$emptyArea1=false;

		// assign variables and display template
		$templateMgr = TemplateManager::getManager($request);
		$registerPagePlugin = PluginRegistry::getPlugin('generic', REGISTERPAGE_PLUGIN_NAME);
		$baseUrl = $request->getBaseUrl();
		include('includes/assignSubscriptionData.inc.php');
		$templateMgr->assign('pageTitle', 'plugins.generic.title.subscriptions');
		$templateMgr->assign('userGroups', $userGroups);
		$templateMgr->assign('subscriptions', $subscriptions);
		$templateMgr->assign('baseUrl', $baseUrl);
		$templateMgr->assign('userRoles', $userRoles);
		$templateMgr->display($registerPagePlugin->getTemplatePath().'subscriptions.tpl');	

	}

	function saveSubscriptions($args, $request) {

		$userRoles = $this->getAuthorizedContextObject(ASSOC_TYPE_USER_ROLES);
		$context = $request->getContext();
		$contextId = $context->getId();
		if (!isset($_POST['buttonSaveSubscriptionChanges'])) {
			if (!$username) {
				Request::redirect(null, 'index');
			}
		}

		include('includes/data.inc.php');

		// redirect to index page if no user is logged in 
		$user = $request->getUser();
		if (!$user) {
			$request->redirect('index');
		}
		$username = $user-> getUsername();	
		$userId = $user->getId();

		if (!$username) {
			Request::redirect(null, 'index');
		}
		$registerPageDAO = new RegisterPageDAO;

		include('includes/getSubscriptionData.inc.php');

		// update subscriptions and roles
		$registerPageDAO->deleteSubscriptions($userId);	
		$registerPageDAO->deleteUserGroups($userId,implode(",",
		array('"Illustrator"','"Supporter"','"Reader"','"Proofreader"','"Typesetter"','"Author"','"Volume Editor"')));

		include('includes/insertSubscriptions.inc.php');

		$groupId = $registerPageDAO->getUserGroupIdByName('Reviewer',$contextId);
		if ($groupId) {
			$registerPageDAO->deleteReviewer($userId,$groupId);
			if ($issetCheckboxReviewer) {
				if ($languageOkay && $areaOkay) {
					include('includes/insertReviewerData.inc.php');
				}
			} else {
				$area1=""; $area2=""; $area3=""; $otherLanguage="";
				$issetCheckboxEnglish=false;
				$issetCheckboxGerman=false;
				$issetCheckboxFrench=false;
				$issetCheckboxOther=false;
				$emptyArea1 =false;
				$languageOkay=true;
			}
		}

		$subscriptions = $registerPageDAO->getSubscriptions($username);
		$userGroups = $registerPageDAO->getUserGroups($username);

		// assign variables
		$templateMgr = TemplateManager::getManager($request);
		$registerPagePlugin = PluginRegistry::getPlugin('generic', REGISTERPAGE_PLUGIN_NAME);
		$templateMgr->assign('pageTitle', 'plugins.generic.title.subscriptions');
		$templateMgr->assign('userGroups', $userGroups);
		$templateMgr->assign('subscriptions', $subscriptions);
		include('includes/assignSubscriptionData.inc.php');
		$templateMgr->assign('languageOkay',$languageOkay);
		$templateMgr->assign('emptyArea1',$emptyArea1);	
		$templateMgr->assign('userRoles', $userRoles);
		$templateMgr->display($registerPagePlugin->getTemplatePath().'subscriptions.tpl');	
	}

}

?>
