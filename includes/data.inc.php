<?php

/**
 * @file plugins/generic/registerPage/includes/data.inc.php
 *
 * Copyright (c) 2016 Language Science Press
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * data for register page plugin
 */

	$areas = array('Syntax',
		'Computational Linguistics',
		'Historical Linguistics',
		'Semantics',
		'Phonetics and Phonology',
		'Morphology',
		'Psycholinguistics',
		'Sociolinguistics',
		'Pragmatics',
		'Typology',
		'Corpus Linguistics',
		'Language Contact',
		'Information Structure',
		'Phraseology',
		'Language Acquisition',
		'Sociolinguistics, Variational Linguistics',
		'History of Linguistics',
		'Translation Studies',
		'Discourse Analysis',
		'Language Documentation',
		'Multilingualism',
		'Language and Cognition',
		'Sign languages',
		'Writing Research');

	$captchaQuestions=array("What is the color of an orange book?",
									"Which word is spelled exactly like the word <i>bank</i>?",
									"What is the concatenation of <i>conca</i> and <i>tenation</i>?",
									"What is the next word in this sentence?<br <i>A rose is a rose is a...</i>",
									"What is Marie Curie's last name?");

	$captchaSolutions=array("orange",
									"bank",
									"concatenation",
									"rose",
									"Curie");

	

?>
