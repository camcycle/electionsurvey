<?php


/*
	Camcycle Elections: Elections survey system
	Copyright (C) 2007-15  MLS and Cambridge Cycling Campaign
	Contributions welcome at: https://github.com/camcycle/electionsurvey
	License: GPL3
	
	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


/*	INSTALLATION NOTES
	
	Server requirements:
		Apache (should run on IIS but you will need to provide a URL rewriting solution)
		MySQL server
		PHP5 server with PDO-mysql installed
	
	PHP information:
		This is written as a self-contained PHP5 class
		The system should be error/warning/notice-free at error_reporting 2047
		Register_globals and similar deprecated things are not required
		
	Libraries:
		Some discrete libraries are needed. These are also PHP class files.
		They need to be in your PHP installation's include_path.
		For instance, if your site is at /srv/www/htdocs/ and your libraries are in a folder /libraries/, then the include_path must have /srv/www/htdocs/libraries/ within it
		Download these freely from these URLs.
		application.php		http://download.geog.cam.ac.uk/projects/application/
		database.php		http://download.geog.cam.ac.uk/projects/database/
		ultimateForm.php	http://download.geog.cam.ac.uk/projects/ultimateform/
			which has dependencies:
		pureContent.php		http://download.geog.cam.ac.uk/projects/purecontent/
		timedate.php		http://download.geog.cam.ac.uk/projects/timedate/
		
	Signin:
		You also need to create a library called signin.php which is just an authentication stub for the website administrator (not the candidates though)
		This acts as a means for the website administrator to log in to print off the printable letters.
		It just needs one method, user_has_privilege, which accepts an argument, and returns true or false. You need to hook into your site's own authentication system or write your own.
		signin::user_has_privilege ('elections')
		
	Stub launching file:
		A stub launching file needs to be created, to instantiate this class.
		Example is below.
		It is basically an array of settings, followed by loading the class and instantiating it.
		The settings noted in the class at $defaults are available, with NULL representing a required setting
		
	URL rewriting: .htaccess file
		Use the .htaccess file supplied
		
	SUMMARY
		- Download the above libraries and put them, together with this class file, into a folder that is in your include_path
		- Create the .htaccess file, and change the RewriteBase if necessary
		- Create the database structure, and a user with SELECT,INSERT,UPDATE rights
		- Create the stub launching file, index.html containing your settings; that file then just loads elections.php and runs the program with the specified settings
*/


/*	Database structure
	# Your database structure should be as follows, with modifications to be made in the elections_wards
	# The user only needs SELECT,INSERT,UPDATE rights at a minimum
	
	
	CREATE DATABASE elections;
	USE elections;
	
	CREATE TABLE IF NOT EXISTS `elections_candidates` (
	  `id` int(11) NOT NULL auto_increment COMMENT 'Unique key',
	  `election` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'Election / year (join to elections)',
	  `ward` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'Ward (join to wards)',
	  `forename` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'Forename',
	  `surname` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'Surname',
	  `address` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'Address',
	  `verification` varchar(6) collate utf8_unicode_ci NOT NULL COMMENT 'Verification number',
	  `affiliation` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'Affiliation (join to affiliations)',
	  `cabinetRestanding` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Whether the candidate is a restanding Cabinet member, and if so, their current Cabinet post',
	  `private` int(1) default NULL,
	  PRIMARY KEY  (`id`),
	  UNIQUE KEY `verification` (`verification`)
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Candidates' AUTO_INCREMENT=224 ;
	
	CREATE TABLE IF NOT EXISTS `elections_elections` (
	  `id` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'Unique key',
	  `name` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'Name of election',
	  `description` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'Description of election',
	  `directionsUrl` varchar(255) collate utf8_unicode_ci NOT NULL default 'http://www.cyclestreets.net/' COMMENT 'Directions to cycle to polling stations',
	  `startDate` date NOT NULL default '0000-00-00' COMMENT 'Start of election',
	  `resultsDate` date NOT NULL default '0000-00-00' COMMENT 'Date of visibility of submissions',
	  `endDate` date NOT NULL default '0000-00-00' COMMENT 'Close of election',
	  `respondentsDate` date NOT NULL default '0000-00-00' COMMENT 'Date when respondents become visible',
	  PRIMARY KEY  (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Election overview';
	
	CREATE TABLE IF NOT EXISTS `elections_questions` (
	  `id` int(11) NOT NULL auto_increment COMMENT 'Unique key',
	  `question` text collate utf8_unicode_ci NOT NULL COMMENT 'Text of question',
	  `links` text collate utf8_unicode_ci COMMENT 'Background links (as URL then text)',
	  PRIMARY KEY  (`id`)
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Available questions' AUTO_INCREMENT=58 ;
	
	CREATE TABLE IF NOT EXISTS `elections_responses` (
	  `id` int(11) NOT NULL auto_increment COMMENT 'Unique key',
	  `candidate` int(11) NOT NULL default '0' COMMENT 'Candidates (join to candidates)',
	  `survey` int(11) NOT NULL default '0' COMMENT 'Survey (join to surveys)',
	  `response` text collate utf8_unicode_ci COMMENT 'Response',
	  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP COMMENT 'Timestamp',
	  PRIMARY KEY  (`id`)
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Responses' AUTO_INCREMENT=729 ;
	
	CREATE TABLE IF NOT EXISTS `elections_surveys` (
	  `id` int(11) NOT NULL auto_increment COMMENT 'Unique key',
	  `election` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'Election / year (join to elections)',
	  `ward` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'Ward (join to wards)',
	  `question` int(11) NOT NULL default '0' COMMENT 'Question (join to questions)',
	  `ordering` int(1) default NULL COMMENT 'Ordering',
	  PRIMARY KEY  (`id`)
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Surveys' AUTO_INCREMENT=343 ;
	
	CREATE TABLE IF NOT EXISTS `elections_wards` (
	  `id` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'Unique key',
	  `prefix` varchar(255) collate utf8_unicode_ci default NULL COMMENT 'Ward name prefix',
	  `ward` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'Ward name',
	  `districtCouncil` enum('','Cambridge City Council','South Cambridgeshire District Council','East Cambridgeshire District Council','Fenland District Council','Huntingdonshire District Council') collate utf8_unicode_ci default NULL COMMENT 'District council',
	  `countyCouncil` enum('','Cambridgeshire County Council') collate utf8_unicode_ci default NULL COMMENT 'County Council',
	  `districtCouncillors` tinyint(1) default NULL COMMENT 'District councillors',
	  `countyCouncillors` tinyint(1) default NULL COMMENT 'County councillors',
	  `parishes` varchar(255) collate utf8_unicode_ci default NULL COMMENT 'Parishes incorporated',
	  PRIMARY KEY  (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Wards';
	
	*/


/*
	Stub launching file example
	Just create a PHP file containing settings and then running the class; something like:
	
	
	# Define the database credentials
	$credentials = array (
		
		# Database
		'hostname'	=> 'localhost',
		'database'	=> 'yourdatabase',
		'username'	=> 'yourusername',
		'password'	=> 'yourpassword',
		'tablePrefix'	=> 'elections_',	// If any
		
		# E-mail addresses
		'webmaster' => 'webmaster@example.com',
		'recipient' => 'elections@lists.example.com',
		
		# Texts
		'welcomeTextHtml' => "<p>Welcome to BLAH's Elections section, which profiles what candidates standing in the elections are saying about BLAH issues. Use your vote to raise the profile of BLAH in this election.</p>",
		'introductoryTextHtml' => "<p>We have sent a short list of questions to each candidate standing to find out what they think about improving BLAH. Their responses can be seen online within these pages.</p>",
		'imprint' => 'BLAH is a non-partisan body. All candidates are given an equal opportunity to submit their views. Information published by BLAH.',
		
		# Pre-formatted letters
		'letterheadHtml' => '
			<p><img src="/path/to/logo.gif" width="140" height="91" alt="BLAH logo" /><br />
			<strong>NAME</strong><br />
			ADDRESS1<br />
			ADDRESS2<br />
			EMAIL<br />
			WEBSITE</p>
			',
		'organisationIntroductionHtml' =>
			"<p>By way of introduction, ORGANISATION NAME is BLURB. Full information about our activities and views can be found on our extensive website, at URL .</p>" .
			"<p>We write to you, as a candidate, to ask your views on a range of issues of concern to our members. We would be most grateful if you could spend a few moments on the short survey below to let us know your views, as soon as you are able. All views submitted (as well as non-responses) will be shown on our website, for the information of voters. We would additionally be interested to hear from you if you have any other views or questions that we might be able to help with.</p>",
		'letterSignatureName' => 'John Smith',
		'letterSignaturePosition' => 'Media Officer',
		'letterSignatureOrganisationName' => 'BLAH',
	);
	
	
	# Load and run the elections system with that settings list
	require_once ('elections.php');
	new elections ($credentials);
	
 */


/*
	# Documentation - joins in the database structure are:
	
	candidates:
		election	Election / year (join to elections)
		ward	Ward (join to wards)
		affiliation	Affiliation (join to affiliations)
	
	responses:
		candidate	Candidates (join to candidates)
		survey	Survey (join to surveys)
	
	surveys:
		election	Election / year (join to elections)
		ward	Ward (join to wards)
		question	Question (join to questions)
 */



#!# Add some sort of testing candidate system



# Class to create an elections lobbying system
class elections
{
	# Defaults; NULL indicates a required argument
	var $defaults = array (
		
		# Database
		'hostname'	=> 'localhost',
		'database'	=> NULL,
		'username'	=> NULL,
		'password'	=> NULL,
		'tablePrefix'	=> 'elections_',
		
		# E-mail addresses
		'webmaster' => NULL,
		'recipient' => NULL,
		
		'page404' => 'sitetech/404.html',
		
		# Date/times
		'resultsVisibleTime' => '21:00:00',
		
		# Text
		'welcomeTextHtml' => NULL,
		'introductoryTextHtml' => NULL,
		'imprint' => NULL,
		'division' => 'ward',	// E.g. could be 'district' or 'constituency' instead
		'divisionPlural' => 'wards',
		
		# Pre-formatted letters
		'letterheadHtml' => NULL,
		'organisationIntroductionHtml' => NULL,
		'organisationConstituentsType' => 'members',	// E.g. could be 'supporters' or similar instead
		'letterSignatureName' => NULL,
		'letterSignaturePosition' => NULL,
		'letterSignatureOrganisationName' => NULL,
		
		# Post-submission HTML
		'postSubmissionHtml' => false,
		'postSubmissionHtmlLetters' => false,
		
		# Temporary override of admin privileges
		'overrideAdmin' => false,
		
		# Whether to show candidate addresses
		'showAddresses' => false,
		
		# Whether to list archived elections
		'listArchived' => true,
	);
	
	
	# Actions (pages) registry
	var $actions = array (
		'home' => 'Home',
		'overview' => 'Overview for an election',
		'allquestions' => 'Every question available in the database',
		'letters' => 'Letters to candidates containing questions for an election',
		'ward' => 'Overview for an area',
		'submit' => 'Candidate response submission',
		'allocations' => 'Create the question allocation SQL',
		'questions' => 'List of questions for an election',
		'elected' => 'Specify the elected candidates',
		'respondents' => 'List of respondents',
		'cabinet' => 'Restanding Cabinet members',
		'admin' => 'Administrative functions',
		'addelection' => 'Add an election',
		'addcandidates' => 'Add candidates',
		'addquestions' => 'Add questions',
		'addsurveys' => 'Add surveys',
	);
	
	
	# Constructor
	public function __construct ($settings = array ())
	{
		# Load external libraries
		require_once ('application.php');
		require_once ('database.php');
		require_once ('pureContent.php');
		
		# Get the base URL
		$this->baseUrl = application::getBaseUrl ();
		
		# Load the local stylesheet
		echo "\n<style type=\"text/css\" media=\"all\">@import \"{$this->baseUrl}/elections.css\";</style>";
		
		# Function to merge the arguments; note that $errors returns the errors by reference and not as a result from the method
		$this->errors = array ();
		if (!$this->settings = $this->mergeConfiguration ($this->defaults, $settings)) {
			echo "<p>The following setup error was found. The administrator needs to correct the setup before this system will run.</p>\n" . application::htmlUl ($this->errors);
			return false;
		}
		
		# Connect to the database or end
		$this->databaseConnection = new database ($settings['hostname'], $settings['username'], $settings['password']);
		if (!$this->databaseConnection->connection) {
 			mail ($this->settings['webmaster'], 'Problem with election system on ' . $_SERVER['SERVER_NAME'], wordwrap ('There was a problem with initalising the election facility at the database connection stage. The database server said: ' . mysql_error () . '.'));
			echo "<p class=\"warning\">Apologies - this facility is currently unavailable, as a technical error occured. The Webmaster has been informed and will investigate.</p>";
			return false;
		};
		
		# Determine whether the user is an administrator
		require_once ('signin.php');
		$this->userIsAdministrator = signin::user_has_privilege ('elections');
		
		# Set the action, checking that a valid page has been supplied
		if (!isSet ($_GET['action']) || !array_key_exists ($_GET['action'], $this->actions)) {
			$this->pageNotFound ();
			return false;
		}
		$this->action = $_GET['action'];
		
		# Get the elections available
		$this->elections = $this->getElections ();
		
		# Determine which election
		$this->election = ((isSet ($_GET['election']) && isSet ($this->elections[$_GET['election']])) ? $this->elections[$_GET['election']] : false);
		
		# Get the wards available for this election (or false if no wards)
		$this->ward = false;
		$this->wards = array ();
		if ($this->election) {
			$this->wards = $this->getWards ($this->election['id']);
			
			# Determine which ward
			$this->ward = ((isSet ($_GET['ward']) && isSet ($this->wards[$_GET['ward']])) ? $this->wards[$_GET['ward']] : false);
		}
		
		# Get the candidates standing in this election for this ward (or false if no candidates)
		$this->candidate = false;
		$this->candidates = array ();
		if ($this->ward) {
			$this->candidates = $this->getCandidates (false, $this->ward);
			
			# Determine which ward
			$this->candidate = ((isSet ($_GET['candidate']) && isSet ($this->wards[$_GET['candidate']])) ? $this->candidates[$_GET['candidate']] : false);
		}
		
		# Determine if there are any restanding Cabinet members in this election
		$this->cabinetRestanding = $this->getCandidates (false, false, false, $cabinetRestanding = true);
		
		# Show the heading
		echo "\n<h1>Elections</h1>";
		if ($this->election) {
			echo $this->droplistNavigation ();
		}
		
		# Run the page action
		$this->{$this->action} ();
		
		# End with disclaimer
		if ($this->action != 'letters') {
			echo "\n<p class=\"small comment\" style=\"margin-top: 50px;\"><em>{$this->settings['imprint']}</em></p>";
		}
	}
	
	
	# Page not found wrapper
	private function pageNotFound ()
	{
		# Create a 404 page
		header ('HTTP/1.0 404 Not Found');
		include ($this->settings['page404']);
	}
	
	
	# Homes page
	private function home ()
	{
		# Introductory text
		$html  = $this->settings['welcomeTextHtml'];
		$html .= "<p class=\"graphic\"><img src=\"/elections/pollingstations.jpg\" width=\"89\" height=\"121\" alt=\"Ballot box\" /></p>";
		$html .= $this->settings['introductoryTextHtml'];
		
		# Show current elections
		$html .= $this->showCurrentElections ();
		
		# Show administrative options
		if ($this->userIsAdministrator) {
			$html .= "\n<h2>Administrative options</h2>";
			$html .= "\n<p><a href=\"{$this->baseUrl}/admin/\">Administrative area</a></p>";
		}
		
		# Show the HTML
		echo $html;
	}
	
	
	# Main page for an election
	private function overview ()
	{
		# Validate the election
		if (!$this->election) {
			header ('HTTP/1.0 404 Not Found');
			echo $html = '<p>There is no such election. Please check the URL and try again.</p>';
			return false;
		}
		
		# Add introduction
		$html  = $this->settings['introductoryTextHtml'];
		
		# Add the summary table and wards
		$html .= "<p class=\"graphic\"><img src=\"/elections/pollingstations.jpg\" width=\"89\" height=\"121\" alt=\"Ballot box\" /></p>";
		$html .= $this->showOverviewDetails ($this->election);
		
		# Show the HTML
		echo $html;
	}
	
	
	# Function to show the summary table and wards
	private function showOverviewDetails ($election)
	{
		# Table of data about the election
		$html  = $this->summaryTable ($election);
		
		# List wards
		$html .= $this->showWards ($election);
		
		# Return the HTML
		return $html;
	}
	
	
	# Main page
	private function ward ()
	{
		# Validate the ward
		if (!$this->election) {
			header ('HTTP/1.0 404 Not Found');
			echo $html = '<p>There is no such election. Please check the URL and try again.</p>';
			return false;
		}
		
		# Validate the ward
		if (!$this->ward) {
			header ('HTTP/1.0 404 Not Found');
			echo $html = '<p>There is no such ' . $this->settings['division'] . ' being contested in this election. Please check the URL and try again.</p>';
			return false;
		}
		
		# Start the HTML
		$html  = '';
		
		# Remind administrators
		if ($this->userIsAdministrator && !$this->election['resultsVisible']) {
			$html .= "<p class=\"warning\"><strong>Note: any responses shown because are only visible to you because you are an administrator.</strong> The responses will not be made public until at least {$this->election['visibilityDateTime']}.</p>";
		}
		
		# Start with a table of data
		$html .= $this->summaryTable ($this->election);
		
		# List the questions asked
		$html .= $this->showQuestions ($this->ward['id']);
		
		# Echo the HTML
		echo $html;
	}
	
	
	# Function to construct a list of all questions, or show all responses to a single question, for a particular election
	private function questions ()
	{
		# Ensure there is an election which is validated
		if (!$this->election) {
			header ('HTTP/1.0 404 Not Found');
			echo $html = '<p>There is no such election. Please check the URL and try again.</p>';
			return false;
		}
		
		# Start the HTML
		$html = '';
		
		# Get all the questions in use in this election
		$questions = $this->getQuestionsForElection ($this->election['id']);
		
		# Determine if a question number is requested, without attempting to check its validity
		$questionNumber = (isSet ($_GET['question']) ? $_GET['question'] : false);
		
		# Show the question or list of questions
		if (strlen ($questionNumber)) {
			$html .= $this->showQuestionForElection ($questions, $questionNumber);
		} else {
			$html .= $this->listQuestionsForElection ($questions);
		}
		
		# Show the HTML
		echo $html;
	}
	
	
	# Function to show an individual question's responses within a particular election
	private function showQuestionForElection ($questions, $questionNumber)
	{
		# Validate the question number
		if (!isSet ($questions[$questionNumber])) {
			header ('HTTP/1.0 404 Not Found');
			return $html = '<p>The specific question number is invalid. Please check the URL and try again.</p>';
		}
		
		# Start the HTML
		$html = '';
		
		# Get the question
		$question = $questions[$questionNumber];
		
		# Determine the total number of questions in this survey
		$total = count ($questions);
		
		#!# This section is over-complex and involves multiple SQL lookups, for the sake of avoiding code duplication in responsesBlock (which has a certain datastructure) - ideally there would be a single OUTER JOIN that would list all candidates and show the responses where the candidate has answered, but this means duplicating lookups like candidate['_name']
		
		# Get the wards (and their associated survey IDs) where this question was asked
		$wardsQuery = "SELECT id,ward FROM {$this->settings['database']}.{$this->settings['tablePrefix']}surveys WHERE question = {$question['questionId']} AND election = '{$this->election['id']}';";
		$wards = $this->databaseConnection->getPairs ($wardsQuery);
		
		# Get the candidates having this question
		$candidates = $this->getCandidates (false, false, $wards);
		
		# Get the responses
		$surveyIds = array_keys ($wards);
		$candidateIds = array_keys ($candidates);
		$responses = $this->getResponses ($surveyIds, $candidateIds);
		
		# Start the HTML with the question
		$html .= "\n<p><em>&laquo; Back to <a href=\"{$this->baseUrl}/{$this->election['id']}/questions/\">list of all {$total} questions</a> for this election</em></p>";
		$html .= "\n<h2>Question {$questionNumber} - we asked:</h2>";
		$html .= $this->responsesBlock ($question, $candidates, $responses, $wards);
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to list all questions for a particular election
	private function listQuestionsForElection ($questions)
	{
		# Loop through each question
		$list = array ();
		foreach ($questions as $questionNumber => $question) {
			$questionText = htmlspecialchars ($question['question']);
			if ($question['highlight']) {
				$questionText = str_replace (htmlspecialchars ($question['highlight']), '<strong>' . htmlspecialchars ($question['highlight']) . '</strong>', $question['question']);
			}
			$list[] = $questionText . "<br /><a href=\"{$this->baseUrl}/{$this->election['id']}/questions/{$questionNumber}/\">Read all answers&hellip;</a>";
		}
		
		# Compile the HTML
		$html  = "\n<h2>List of questions</h2>";
		$html .= "\n<p>Here is a list of all the questions (across all wards) we have asked for this election:</p>";
		$html .= application::htmlOl ($list, 0, 'spaced');
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to get all the questions being asked in a particular election
	private function getQuestionsForElection ($electionId, $idToIndex = false)
	{
		# Get all the questions for this election
		$data = $this->getQuestions (false, $electionId, $groupByQuestionId = true);
		
		# Reindex from 1 (for the sake of nicer /question/<id>/ URLs) as the keys are effectively arbitrary, keeping only relevant fields (i.e. stripping bogus fields like ward that have become left behind from the GROUP BY operation)
		$questions = array ();
		$relevantFields = array ('questionId', 'question', 'links', 'highlight');
		$i = 1;
		foreach ($data as $question) {
			$key = $i++;
			$questions[$key] = array ();
			foreach ($relevantFields as $relevantField) {
				$questions[$key][$relevantField] = $question[$relevantField];
			}
		}
		
		# Reverse the indexing as questionId => orderId if required
		if ($idToIndex) {
			$questionsIdToIndex = array ();
			foreach ($questions as $order => $question) {
				$questionsIdToIndex[$question['questionId']] = $order;
			}
			$questions = $questionsIdToIndex;
		}
		
		# Return the list
		return $questions;
	}
	
	
	# Function to create a ward summary
	private function summaryTable ($election)
	{
		# Compile the HTML
		$html  = "\n<h2>{$election['name']}" . ($this->ward ? ': ' . $this->wardName ($this->ward) : '') . "</h2>";
		$table['Summary'] = (!$this->ward ? $election['description'] : "<a href=\"{$this->baseUrl}/{$election['id']}/\">{$election['description']}</a>");
		$table['Polling date'] = $election['polling date'];
		if ($this->ward) {$table['Ward'] = $this->droplistNavigation (true);}
		
		# List the candidates
		if ($this->ward) {
			$table['Candidates<br />(by surname)'] = $this->showCandidates ();
		}
		
		# Show the respondents
		if (!$this->ward) {
			$table['Questions'] = "<a href=\"{$this->baseUrl}/{$election['id']}/questions/\">" . ($election['active'] ? '' : '<strong><img src="/images/icons/bullet_go.png" class="icon" /> ') . 'Index of all questions for this election' . ($election['active'] ? '' : '</strong>') .  '</a>';
			$table['Respondents'] = "<a href=\"{$this->baseUrl}/{$election['id']}/respondents.html\">" . ($election['active'] ? '<strong><img src="/images/icons/bullet_go.png" class="icon" /> ' : '') . 'Index of all respondents' . ($election['active'] ? ' (so far)' : '') .  '</a>';
			if ($this->cabinetRestanding) {
				$table['Cabinet'] = "<a href=\"{$this->baseUrl}/{$election['id']}/cabinet.html\">Cabinet members in surveyed wards restanding in this election</a>";
			}
		}
		
		# Compile the HTML
		$html .= application::htmlTableKeyed ($table, array (), true, 'lines', $allowHtml = true);
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to get list of elections, including whether they are active
	private function getElections ()
	{
		# Get data
		$query = "SELECT
				*,
				IF(endDate>=(CAST(NOW() AS DATE)),1,0) AS active,
				IF(endDate=(CAST(NOW() AS DATE)),1,0) AS votingToday,
				IF(((DATEDIFF(CAST(NOW() AS DATE),endDate) < 28) && endDate<(CAST(NOW() AS DATE))),1,0) AS isRecent,
				IF((CAST(NOW() AS DATE))<resultsDate,0,1) AS resultsVisible,
				IF(NOW()<CONCAT(resultsDate,' ','{$this->settings['resultsVisibleTime']}'),0,1) AS resultsVisible,
				DATE_FORMAT(endDate,'%W %D %M %Y') AS 'polling date',
				DATE_FORMAT(CONCAT(resultsDate,' ','{$this->settings['resultsVisibleTime']}'),'%l%p, %W %D %M %Y') AS visibilityDateTime,
				DATE_FORMAT(respondentsDate,'%W %D %M %Y') AS respondentsDate,
				IF(name LIKE '%county%',1,0) AS isCounty
			FROM {$this->settings['database']}.{$this->settings['tablePrefix']}elections
			WHERE startDate <= (CAST(NOW() AS DATE))
			ORDER BY endDate DESC, isCounty DESC /* County before others if on same day */
		;";
		$data = $this->databaseConnection->getData ($query, "{$this->settings['database']}.{$this->settings['tablePrefix']}elections");
		
		# Return the data
		return $data;
	}
	
	
	# Function to show current elections in list format
	private function showCurrentElections ()
	{
		# Start the HTML
		$html = '';
		
		# Get the data
		if (!$this->elections) {
			$html .= '<h2>List of elections</h2>';
			$html .= '<p>There are no elections.</p>';
			return $html;
		}
		
		# Regroup by the election being active or not
		$elections = application::regroup ($this->elections, 'active', false);
		
		# Start with current elections
		$currentElectionsHeading = '<h2>Current election survey' . ((isSet ($elections[1]) && (count ($elections[1]) > 1)) ? 's' : '') . '</h2>';
		if (isSet ($elections[1])) {
			if (count ($elections[1]) == 1) {
				$currentElection = reset ($elections[1]);
				$html .= $this->showOverviewDetails ($currentElection);
			} else {
				$html .= $currentElectionsHeading;
				$html .= $this->listElections ($elections[1], false, 'spaced');
			}
			$html .= "\n<br />";
		} else {
			// $html .= $currentElectionsHeading;
			$html .= "\n<p>There are no election surveys at present. Have a look at previous surveys below.</p>";
		}
		
		# Regroup by the election being recent or not
		$elections = application::regroup ($this->elections, 'isRecent', false);
		
		# Now show recent elections
		if (isSet ($elections[1])) {
			$html .= '<h2>Recent election surveys</h2>';
			$html .= $this->listElections ($elections[1], false, 'spaced');
			$html .= "\n<br />";
		}
		
		# Now show archived, non-recent elections
		$html .= '<h2>Previous election surveys</h2>';
		if (isSet ($elections[0])) {
			$html .= $this->listElections ($elections[0], true);
			$html .= "\n<br />";
		} else {
			$html .= "\<p>There are no archived election surveys.</p>";
		}
		
		# Return the HTML
		return $html;
	}
	
	
	# Helper function to list elections
	private function listElections ($elections, $activeOrRecent = false, $class = false, $urlSuffix = false)
	{
		# Create the listing
		foreach ($elections as $key => $election) {
			if (!$this->settings['listArchived'] && !$election['active']) {continue;}
			$list[$key] = "<a href=\"{$this->baseUrl}/{$election['id']}/{$urlSuffix}\">{$election['name']}" . ($activeOrRecent ? '' : "<br />(polling date: {$election['polling date']})") . '</a>';
			if (!$activeOrRecent) {
				$list[$key] = "<strong>{$list[$key]}</strong>";
			}
		}
		
		# Construct the HTML
		$html = application::htmlUl ($list, 0, $class);
		
		# Return the HTML
		return $html;
	}
	
	
	
	# Function to get wards being contested in an election
	private function getWards ($elections)
	{
		# Get data
		$query = "SELECT
				{$this->settings['tablePrefix']}candidates.ward as id,
				{$this->settings['tablePrefix']}wards.prefix,
				{$this->settings['tablePrefix']}wards.ward,
				COUNT({$this->settings['database']}.{$this->settings['tablePrefix']}wards.id) as 'candidates'
			FROM {$this->settings['database']}.{$this->settings['tablePrefix']}candidates
			LEFT OUTER JOIN {$this->settings['database']}.{$this->settings['tablePrefix']}wards ON {$this->settings['database']}.{$this->settings['tablePrefix']}candidates.ward = {$this->settings['database']}.{$this->settings['tablePrefix']}wards.id
			WHERE election REGEXP '^({$elections})$'
			GROUP BY {$this->settings['tablePrefix']}wards.ward
			ORDER BY {$this->settings['tablePrefix']}wards.ward
		;";
		$data = $this->databaseConnection->getData ($query, "{$this->settings['database']}.{$this->settings['tablePrefix']}wards");
		
		# Add in the constructed ward name
		foreach ($data as $key => $ward) {
			$data[$key]['_name'] = $this->wardName ($ward);
		}
		
		# Return the data
		return $data;
	}
	
	
	# Function to add a droplist navigation
	private function droplistNavigation ($wardsOnly = false)
	{
		# Start the HTML
		$html  = '';
		
		# In wards-only mode, if there is only one ward, just return its name - no point showing the jumplist
		if ($wardsOnly) {
			if (count ($this->wards) == 1) {
				$ward = array_shift (array_values ($this->wards));
				$html = $this->wardName ($ward);
				return $html;
			}
		}
		
		# Start the list
		$list = array ();
		if (!$wardsOnly) {
			$list["{$this->baseUrl}/{$this->election['id']}/"] = 'Overview page';
			$list["{$this->baseUrl}/{$this->election['id']}/questions/"] = 'Questions index';
			$list["{$this->baseUrl}/{$this->election['id']}/respondents.html"] = 'Respondents';
			if ($this->cabinetRestanding) {
				$list["{$this->baseUrl}/{$this->election['id']}/cabinet.html"] = 'Cabinet restanding';
			}
			if ($this->election['votingToday']) {
				$list[$this->election['directionsUrl']] = 'Directions to polling stations';
			}
		}
		
		# Add each ward
		foreach ($this->wards as $key => $ward) {
			$location = "{$this->baseUrl}/{$this->election['id']}/{$ward['id']}/";
			$list[$location] = $this->wardName ($ward);
		}
		
		# Set the current page as the selected item
		$selected = $_SERVER['SCRIPT_URL'];
		
		# Deal with the per-question pages not matching the URL
		if ($this->action == 'questions') {
			$selected = "{$this->baseUrl}/{$this->election['id']}/questions/";
		}
		
		# Convert to a droplist
		#!# NB This doesn't work in IE7, probably because the window.location.href presumably needs a full URL rather than a location; fix needed upstream in pureContent library
		require_once ('pureContent.php');
		$submitTo = "{$this->baseUrl}/{$this->election['id']}/";
		$html = pureContent::htmlJumplist ($list, $selected, $submitTo, 'jumplist', $parentTabLevel = 3, ($wardsOnly ? '' : 'jumplist'), ($wardsOnly ? '' : 'Jump to:'));
		
		# Create a processor to handle changes
		pureContent::jumplistProcessor ();
		
		# Show directions to polling stations if required under the main jumplist
		if ($this->election['votingToday']) {
			if (!$wardsOnly) {
				$html .= "<p class=\"directionsbutton\"><a class=\"actions right\" href=\"{$this->election['directionsUrl']}\">" . '<img src="/images/icons/map.png" class="icon" /> Cycle to your polling station - get directions</a></p>';
			}
		}
		
		# Surround with a div
		if (!$wardsOnly) {
			$html = "\n<div class=\"navigation\">\n" . $html . "\n</div>";
		}
		
		# Return the HTML
		return $html;
	}
	
	
	
	# Function to show wards
	private function showWards ($election)
	{
		# Start the HTML
		$html  = "\n\n" . '<h2><img src="/images/general/next.jpg" width="20" height="20" alt="&gt;" border="0" /> Candidates\' responses for each ' . $this->settings['division'] . '</h2>';
		$html .= "\n<p>The following " . $this->settings['divisionPlural'] . " being contested are those for which we have sent questions to candidates:</p>";
		
		# Get the wards for this election
		$wards = $this->getWards ($election['id']);
		
		# Get the data
		if (!$wards) {
			return $html .= "\n<p>There are no " . $this->settings['divisionPlural'] . " being contested.</p>";
		}
		
		# Construct the HTML
		foreach ($wards as $key => $ward) {
			$wardName = $this->wardName ($ward);
			$candidates = "({$ward['candidates']} " . ($ward['candidates'] == 1 ? 'candidate' : 'candidates') . " standing)";
			$list[$key] = "<a href=\"{$this->baseUrl}/{$election['id']}/{$ward['id']}/\">{$wardName}</a> {$candidates}";
		}
		
		# Construct the HTML
		$html .= application::htmlUl ($list);
		
		# Show administrative options
		if ($this->userIsAdministrator) {
			$html .= "\n<h2>Administrative options</h2>";
			$html .= "\n<p>As an administrator you can also:</p>";
			$html .= "\n<ul>";
			$html .= "\n\t<li><a href=\"{$this->baseUrl}/{$election['id']}/letters.html\">See the printable letters to candidates for this election</a></li>";
			$html .= "\n</ul>";
		}
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to construct a ward name
	private function wardName ($ward)
	{
		# Construct and return the ward name
		return (!empty ($ward['prefix']) ? htmlspecialchars ($ward['prefix']) . ' ' : '') . htmlspecialchars ($ward['ward']);
	}
	
	
	# Function to get candidates in an election
	private function getCandidates ($all = false, $onlyWard = false, $inWards = false, $cabinetRestanding = false)
	{
		# Get data
		$query = "SELECT
				{$this->settings['tablePrefix']}candidates.id as id,
				{$this->settings['tablePrefix']}candidates.ward as wardId,
				{$this->settings['tablePrefix']}candidates.elected,
				{$this->settings['tablePrefix']}candidates.cabinetRestanding,
				private, prefix,
				{$this->settings['tablePrefix']}wards.ward,
				{$this->settings['tablePrefix']}wards.districtCouncil,
				forename, surname, verification, address, 
				{$this->settings['tablePrefix']}affiliations.id AS affiliationId,
				{$this->settings['tablePrefix']}affiliations.name as affiliation,
				{$this->settings['tablePrefix']}affiliations.colour,
				CONCAT(forename,' ',UPPER(surname)) as name
			FROM {$this->settings['database']}.{$this->settings['tablePrefix']}candidates
			LEFT OUTER JOIN {$this->settings['database']}.{$this->settings['tablePrefix']}affiliations ON {$this->settings['database']}.{$this->settings['tablePrefix']}candidates.affiliation = {$this->settings['database']}.{$this->settings['tablePrefix']}affiliations.id
			LEFT OUTER JOIN {$this->settings['database']}.{$this->settings['tablePrefix']}wards ON {$this->settings['database']}.{$this->settings['tablePrefix']}candidates.ward = {$this->settings['database']}.{$this->settings['tablePrefix']}wards.id
			WHERE
				election = '{$this->election['id']}'
				" . ($inWards ? " AND {$this->settings['tablePrefix']}candidates.ward IN('" . implode ("','", $inWards) . "')" : ($onlyWard ? "AND {$this->settings['tablePrefix']}candidates.ward = '{$onlyWard['id']}'" : '')) . "
				" . ($cabinetRestanding ? " AND ({$this->settings['tablePrefix']}candidates.cabinetRestanding IS NOT NULL AND {$this->settings['tablePrefix']}candidates.cabinetRestanding != '')" : '') . "
			ORDER BY " . ($inWards ? 'affiliation,surname,forename' : ($all ? 'wardId,surname' : 'surname,forename')) . "
		;";
		$data = $this->databaseConnection->getData ($query, "{$this->settings['database']}.{$this->settings['tablePrefix']}wards");
		
		# Add in the constructed complete name with affiliation
		foreach ($data as $key => $candidate) {
			$data[$key]['_nameUncoloured'] = htmlspecialchars ($candidate['name']) . ' &nbsp;(' . htmlspecialchars ($candidate['affiliation']) . ')';
			$data[$key]['_nameUncolouredNoAffiliation'] = htmlspecialchars ($candidate['name']);
			$data[$key]['_name'] = "<span style=\"color: #{$candidate['colour']}; font-weight: bold;\">" . htmlspecialchars ($candidate['name']) . ' &nbsp;(' . htmlspecialchars ($candidate['affiliation']) . ')</span>';
		}
		
		# Return the data
		return $data;
	}
	
	
	# Function to show candidates in a ward
	private function showCandidates ()
	{
		# Get the data
		if (!$this->candidates) {
			return $html = "\n<p>There are no candidates contesting this " . $this->settings['division'] . '.</p>';
		}
		
		# Construct the HTML
		foreach ($this->candidates as $key => $candidate) {
			$list[$key]  = $candidate['_name'];
			if ($this->settings['showAddresses']) {$list[$key] .= '<br /><span class="small comment">' . htmlspecialchars ($candidate['address']) . '</span>';}
		}
		
		# Construct the HTML
		$html  = '';
		// $html .= "<h3>Candidates standing for " . $this->wardName ($this->ward) . ' ' . $this->settings['division'] . '</h3>';
		// $html .= "\n<p>The following candidates (listed in surname order) are standing for " . $this->wardName ($this->ward) . ' ' . $this->settings['division'] . '</p>';
		$html .= application::htmlUl ($list, 0, 'nobullet' . ($this->settings['showAddresses'] ? ' spaced' : ''));
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to show questions
	private function showQuestions ($limitToWard = false)
	{
		# Start the HTML
		$html  = '';
		if (!$limitToWard) {$html .= "\n\n<h2>" . ($this->election ? 'Questions allocated to each ' . $this->settings['division'] : 'All available questions') . '</h2>';}
		
		# Get the data
		if (!$data = $this->getQuestions ($limitToWard, $this->election['id'])) {
			$wardName = $this->wards[$limitToWard]['_name'];
			$html .= "\n\n<h3 class=\"ward\" id=\"{$wardName}\">Questions for {$wardName} {$this->settings['division']} candidates</h3>";
			return $html .= "\n<p>There are no questions assigned for this {$this->settings['division']} at present.</p>";
		}
		
		# Regroup by ward
		$data = ((!$limitToWard && !$this->election) ? array ('_all' => $data) : application::regroup ($data, 'wardId', $removeGroupColumn = false));
		
		# Get responses from candidates if there are candidates
		$responses = false;
		if ($this->candidates) {
			$wardSurveyIds = array_keys ($data[$limitToWard]);
			$responses = $this->getResponses ($wardSurveyIds);
		}
		
		# Get all the question index numbers in use in this election - i.e. the public numbers 1,2,3.. (as shown on the question index page) rather than the internal IDs
		$questionNumbersPublic = $this->getQuestionsForElection ($this->election['id'], true);
		
		# Loop through each grouping
		$questionsHtml = '';
		foreach ($data as $ward => $questions) {
			
			# Miss out if no candidates in a ward
			#!# Need to fix for /elections/%election/questions.html where ward has no candidates, e.g. 2007may:girton
			// if ($limitToWard && !isSet ($this->wards[$ward])) {continue;}
			
			# Count the questions
			$totalQuestions = count ($questions);
			
			# Show the ward heading
			if ($this->election && $ward != '_all') {
				#!# Ward may not exist if no candidates
				$wardName = $this->wards[$ward]['_name'];
				$questionsHtml .= "\n\n<h3 class=\"ward\" id=\"{$ward}\">Questions for {$wardName} {$this->settings['division']} candidates ({$totalQuestions} questions)</h3>";
				$wardsHtml[] = "<a href=\"#{$ward}\">{$wardName} {$this->settings['division']}</a> ({$totalQuestions} questions)";
			}
			
			# Construct the HTML
			$i = 0;
			$questionsJumplist = array ();
			$list = array ();
			foreach ($questions as $surveyId => $question) {
				$i++;
				$link = "question{$i}" . (!$limitToWard && $this->election ? $ward : '');
				$questionsJumplist[] = "<strong><a href=\"#{$link}\">&nbsp;{$i}&nbsp;</a></strong>";
				$questionNumberPublic = $questionNumbersPublic[$question['questionId']];
				$list[$surveyId]  = "\n\n<h4 class=\"question\" id=\"{$link}\"><a href=\"#{$link}\">#</a> Question {$i}" . ($limitToWard ? '' : " &nbsp;[survey-id#{$surveyId}]") . '</h4>';	// In all-listing mode (i.e. admins-only), show the IDs
				$list[$surveyId] .= $this->responsesBlock ($question, $this->candidates, $responses, false, $questionNumberPublic);
			}
			
			# Construct the HTML
			$questionsHtml .= "<p>Jump to question: " . implode (' ', $questionsJumplist) . '</p>';
			$questionsHtml .= implode ($list);
		}
		
		# Add the questions HTML
		if (!$limitToWard) {
			$html .= "\n<p>Below is a list of " . ($this->election ? 'the questions allocated to each ' . $this->settings['division'] : 'all questions available in the database') . ":</p>";
			if ($this->election) {
				$html .= application::htmlUl ($wardsHtml);
				if ($this->userIsAdministrator) {
					$html .= "\n<p>As an administrator you can also return to the <a href=\"{$this->baseUrl}/admin/allquestions.html\">list of all questions available in the database</a>.</p>";
				}
			}
		}
		$html .= $questionsHtml;
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to create the block of candidate responses to a question
	private function responsesBlock ($question, $candidates, $responses, $crossWardMode = false, $questionNumberPublic = false)
	{
		# Start the HTML
		$html  = '';
		
		/*
		application::dumpData ($question);
		application::dumpData ($candidates);
		application::dumpData ($responses);
		*/
		
		# Add the question box at the top
		$question['question'] = htmlspecialchars ($question['question']);
		$html .= $this->questionBox ($question);
		
		# Add a link to all responses in other wards if required
		if ($questionNumberPublic) {
			$html .= "\n<p class=\"allresponseslink\"><a href=\"{$this->baseUrl}/{$this->election['id']}/questions/{$questionNumberPublic}/\">Responses to this question from all wards&hellip;</a></p>";
		}
		
		# End if no candidates
		if (!$candidates) {return $html;}
		
		# If the results are not yet visible, end at this point
		if (!$this->election['resultsVisible'] && !$this->userIsAdministrator) {
			return $html .= "\n<p><em>Candidates' responses are not yet visible. Please check back here from {$this->election['visibilityDateTime']}.</em></p>";
		}
		
		# State the wards and the number of responses, if in cross-ward mode
		if ($crossWardMode) {
			$wardNames = array ();
			foreach ($crossWardMode as $ward) {
				$wardNames[] = $this->wards[$ward]['_name'];
			}
			sort ($wardNames);
			$totalWardsAsked = count ($wardNames);
			$everyWardAsked = (count ($this->wards) == $totalWardsAsked);
			$html .= "\n<p>We asked this question " . ($everyWardAsked ? "in <strong>all {$totalWardsAsked} wards</strong>, namely: " : ($totalWardsAsked > 1 ? "in these <strong>{$totalWardsAsked} wards</strong>: " : 'only in ')) . implode (', ', $wardNames) . '.</p>';
			$totalCandidates = count ($candidates);
			$totalResponses = count ($responses);
			$percentageReplied = round (($totalResponses / $totalCandidates) * 100);
			$html .= "\n<p><strong>{$totalResponses}</strong> of the <strong>{$totalCandidates}</strong> candidates (<strong>{$percentageReplied}%</strong>) who were asked this question responded as below.</p>";
		}
		
		# Determine if this is a election with more than one person standing per party
		$multiPersonWards = false;
		foreach ($candidates as $candidateKey => $candidate) {
			if (isSet ($affiliations[$candidate['affiliationId']])) {$multiPersonWards = true;}
			$affiliations[$candidate['affiliationId']][$candidateKey] = 1 + (isSet ($affiliations[$candidate['affiliationId']]) ? 1 : 0);
		}
		
		# Loop through each candidate (so that all are listed, irrespective of whether they have responded)
		$responsesList = array ();
		$showsElected = 0;
		foreach ($candidates as $candidateKey => $candidate) {
			
			# If this is a multi-person ward election, determine the suffix to add to the unique ID below
			$multiPersonWardsIdSuffix = '';
			if ($multiPersonWards) {
				$multiPersonWardsIdSuffix = '_' . $affiliations[$candidate['affiliationId']][$candidateKey];
			}
			
			# Set a unique ID for use in the table, including the flag for whether the candidate is elected
			$id = $candidate['wardId'] . '_' . $candidate['affiliationId'] . $multiPersonWardsIdSuffix . ($candidate['elected'] ? ' elected' : '');
			if ($candidate['elected']) {$showsElected++;}
			
			# Assemble the name of the candidate
			$name = str_replace (' &nbsp;(', '<br />(', $candidate['_name']);
			
			# Occasionally a candidate might not give permission to make the response public
			if ($candidate['private']) {
				$responsesList[$id] = array ('name' => $name, 'answer' => '<p><em>This candidate has contacted the Campaign in response to the survey but has not given permission to make the response public.</em></p>');
				continue;
			}
			
			# If the candidate has not yet responded, state this
			$notRespondedText = '<span class="comment">The candidate has not' . ($this->election['active'] ? ' (yet)' : '') . ' responded to the survey.</span>';
			if ($crossWardMode) {	// Here we don't know the survey ID, but there is always one entry if it exists at all; so we check for existence then retrieve the surveyId
				if (!isSet ($responses[$candidateKey])) {
					if ($this->election['active']) {	// Omit the not-responded text after the election - all we care about from that point is what people said, not who didn't give a response
						$responsesList[$id] = array ('name' => $name, 'answer' => $notRespondedText);
					}
					continue;
				}
				$surveyId = key ($responses[$candidateKey]);
			} else {
				$surveyId = $question['id'];
				if (!isSet ($responses[$candidateKey][$surveyId])) {
					$responsesList[$id] = array ('name' => $name, 'answer' => $notRespondedText);
					continue;
				}
			}
			
			# If there is no response, state that
			if (!$responses[$candidateKey][$surveyId]['response']) {
				$responsesList[$id] = array ('name' => $name, 'answer' => '<span class="comment">The candidate ' . ($this->election['active'] ? 'has not entered' : 'did not enter') . ' a response for this question.</span>');
				continue;
			}
			
			# Show the response if the candidate has completed the survey
			$responsesList[$id] = array ('name' => $name, 'answer' => application::formatTextBlock (htmlspecialchars ($responses[$candidateKey][$surveyId]['response'])));
		}
		
		# Compile the HTML as a keyed table
		if ($crossWardMode && $showsElected) {
			$html .= "\n<p>Those candidate(s) which were elected are <span class=\"elected\">highlighted</span>.</p>";
		}
		$html .= application::htmlTable ($responsesList, array (), 'lines questions', false, false, $allowHtml = true, false, false, $addRowKeyClasses = true, array (), false, $showHeadings = false);
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to format the question and links inside a box
	private function questionBox ($question)
	{
		# Return the assembled HTML
		return "\n<div class=\"graybox\"><p>{$question['question']}</p>" . $this->formatLinks ($question['links']) . '</div>';
	}
	
	
	# Function to get all questions being asked
	private function getQuestions ($ward = false, $election = false, $groupByQuestionId = false)
	{
		# If there is an election, get all available questions (top level)
		if (!$election) {
			$query = "SELECT * FROM {$this->settings['database']}.{$this->settings['tablePrefix']}questions;";
			
		# Otherwise get surveys by ward
		} else {
			$query = "SELECT
					{$this->settings['tablePrefix']}surveys.id as id,
					{$this->settings['tablePrefix']}surveys.ward as wardId,
					{$this->settings['tablePrefix']}questions.id as questionId,
					{$this->settings['tablePrefix']}questions.question,
					{$this->settings['tablePrefix']}questions.links,
					{$this->settings['tablePrefix']}questions.highlight,
					{$this->settings['tablePrefix']}wards.prefix,
					{$this->settings['tablePrefix']}wards.ward
				FROM {$this->settings['database']}.{$this->settings['tablePrefix']}surveys
				LEFT OUTER JOIN {$this->settings['database']}.{$this->settings['tablePrefix']}wards ON {$this->settings['database']}.{$this->settings['tablePrefix']}surveys.ward = {$this->settings['database']}.{$this->settings['tablePrefix']}wards.id
				LEFT OUTER JOIN {$this->settings['database']}.{$this->settings['tablePrefix']}questions ON {$this->settings['database']}.{$this->settings['tablePrefix']}surveys.question = {$this->settings['database']}.{$this->settings['tablePrefix']}questions.id
				WHERE election = '{$election}'
				" . ($ward ? "AND {$this->settings['tablePrefix']}surveys.ward = '{$ward}'" : '') . "
				" . ($groupByQuestionId ? "GROUP BY questionId" : '') . "
				ORDER BY " . ($groupByQuestionId ? 'questionId' : "{$this->settings['tablePrefix']}surveys.ward,ordering,{$this->settings['tablePrefix']}surveys.id") . "
			;";
		}
		$data = $this->databaseConnection->getData ($query, "{$this->settings['database']}.{$this->settings['tablePrefix']}wards");
		
		# Return the data
		return $data;
	}
	
	
	# Function to format links
	private function formatLinks ($links, $letterMode = false)
	{
		# End if no links
		$links = trim ($links);
		if (empty ($links)) {return '';}
		
		# Introduce the links
		$html  = ($letterMode ? "\n<p class=\"links\">Further reading online:</p>" : "\n<p class=\"links\">Relevant links (each opens in a new window):</p>");
		
		# Split by newline
		$list = array ();
		$links = explode ("\n", $links);
		foreach ($links as $link) {
			$link = trim ($link);
			
			# Default the title to the URL
			$title = NULL;
			if (!$letterMode) {$title = $link;}
			
			# Explode by first space to check for a url + title -style line
			if (preg_match ("/[\s]+/", $link)) {
				list ($link, $title) = preg_split ("/[\s]+/", $link, 2);
				$title = htmlspecialchars ($title);
			} else {
				
				# Perform a match, taking care of any mirror website
				if (preg_match ('@^http://' . str_replace ('mirror.', 'www.', $_SERVER['SERVER_NAME']) . '(.*)@', $link, $matches)) {
					$link = ($letterMode ? str_replace ('mirror.', 'www.', $_SERVER['SERVER_NAME']) : '') . $matches[1];
					if (preg_match ('@/newsletters/([0-9]+)/article([0-9]+).html$@', $link, $newsletterMatches)) {
						$settingsFile = "newsletters/{$newsletterMatches[1]}/settings.html";
						if (is_readable ($_SERVER['DOCUMENT_ROOT'] . '/' . $settingsFile)) {
							include ($settingsFile);	// NOT include_once - as that would cache a previously-loaded settings file
							$index = $newsletterMatches[2] - 1;
							if (isSet ($newsletterSettings) && isSet ($newsletterSettings['articles']) && isSet ($newsletterSettings['articles'][$index])) {
								$title = "Newsletter {$newsletterMatches[1]}: {$newsletterSettings['articles'][$index]}";
							}
						}
					} else {
						
						# Extract the title
						$filename = $_SERVER['DOCUMENT_ROOT'] . str_replace (str_replace ('mirror.', 'www.', $_SERVER['SERVER_NAME']), '', $link) . (substr ($link, -1) == '/' ? 'index.html' : '');
						if (is_readable ($filename)) {
							$file = file_get_contents ($filename);
							$title = application::getTitleFromFileContents ($file, 200);
							$title = htmlspecialchars ($title);
						}
					}
				}
			}
			
			# Construct the link
			if ($letterMode) {
				$list[] = ($title ? "{$title}:<br />" : '') . str_replace ('http://www.', 'www.', $link);
			} else {
				$list[] = "<a target=\"_blank\" title=\"[Link opens in a new window]\" href=\"" . $link . "\">" . $title . '</a>';
			}
		}
		
		# Compile the HTML
		$html .= application::htmlUl ($list, 0, 'links');
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to get candidates' responses
	private function getResponses ($surveys = array (), $candidateId = false)
	{
		# Get the data
		#!# This should do latest-grouping here rather than within regroup
		#!# Limit by election key/year?
		$query = "SELECT
				id,
				candidate as candidateId,
				survey as surveyId,
				response, timestamp
			FROM {$this->settings['database']}.{$this->settings['tablePrefix']}responses
			WHERE 1=1
			" . ($surveys ? " AND survey REGEXP '^(" . implode ('|', $surveys) . ")$'" : '') . "
			" . (is_array ($candidateId) ? " AND candidate IN(" . implode (',', $candidateId) . ")" : ($candidateId ? " AND candidate = '{$candidateId}'" : '')) . "
			ORDER BY surveyId, candidateId
		;";
		if (!$data = $this->databaseConnection->getData ($query, "{$this->settings['database']}.{$this->settings['tablePrefix']}responses")) {
			return array ();
		}
		
		# Regroup the data; this uses the latest update if updates have been done
		$data = application::regroup ($data, 'candidateId', false);
		foreach ($data as $candidate => $responses) {
			$rearrangedResponses = array ();
			foreach ($responses as $id => $response) {
				$rearrangedResponses[$response['surveyId']] = $response;
			}
			$data[$candidate] = $rearrangedResponses;
		}
		
		# Return the responses
		return $data;
	}
	
	
	# Candidate response submission
	private function submit ($showIds = false)
	{
		# Get the list of all wards currently being surveyed
		if (!$wards = $this->getActiveWards ()) {
			echo $html = "\n<p>No {$this->settings['divisionPlural']} are currently being surveyed.</p>";
			return;
		}
		
		# Determine whether a (validly-structured) second-stage submission has been made
		$secondStagePosted = (isSet ($_POST['questions']) && is_array ($_POST['questions']) && isSet ($_POST['questions']['verification']) && is_array ($_POST['questions']['verification']) && isSet ($_POST['questions']['verification']['number']) && isSet ($_POST['questions']['verification']['ward']));
		
		# Load the form
		require_once ('ultimateForm.php');
		
		# Run verification first
		if (!$secondStagePosted) {
			
			# Load and instantiate the form library
			$form = new form (array (
				'name' => 'verification',
				'formCompleteText' => false,
				'div'	=> false,
				'display'	=> 'paragraphs',
				'displayRestrictions'	=> false,
			));
			$form->heading ('p', "<strong>Welcome</strong>, candidate. <strong>Thank you</strong> for responding to our survey.</p>\n<p>We've done this survey online so that constituents - including our {$this->settings['organisationConstituentsType']} - in each {$this->settings['division']} can see what each candidate thinks. Voters can then take these views into account alongside other issues of concern to them. The questions we've posed are relevant/specific to each {$this->settings['division']}.");
			$form->heading ('p', '<br />Please firstly enter the verification number given in the letter/e-mail you received, which ensures the security of your response.');
			$form->input (array (
				'name'			=> 'number',
				'title'			=> 'Verification number',
				'required'		=> true,
				'maxlength'		=> 6,
				'regexp'		=> '^([0-9]{6})$',
			));
			$form->select (array (
				'name'			=> 'ward',
				'title'			=> ucfirst ($this->settings['division']),
				'required'		=> 1,
				#!# Remove this hack
				'values'		=> str_replace ('&amp;', '&', $wards),
			));
			
			# Process the form or end
			if (!$result = $form->process ()) {return false;}
		}
		
		# Determine the number and ward to be checked
		$number = ($secondStagePosted ? $_POST['questions']['verification']['number'] : $result['number']);
		$ward = ($secondStagePosted ? $_POST['questions']['verification']['ward'] : $result['ward']);
		
		# Confirm the details
		#!# Use getUnfinalised to improve the UI here
		if (!$candidate = $this->verifyCandidate ($number, $ward)) {
			echo "\n<p>The verification/{$this->settings['division']} pair you submitted does not seem to be correct. Please check the letter/e-mail we sent you and <a href=\"{$this->baseUrl}/submit/\">try again</a>.</p>";
			return false;
		}
		
		# Retrieve and cache the election data
		$this->election = $this->elections[$candidate['electionId']];
		
		# Create a shortcut to the ward name
		$wardName = $wards[$candidate['wardId']];
		
		# Start the page
		$html  = "\n\n<h2 class=\"ward\" id=\"{$wardName}\">Questions for {$wardName} {$this->settings['division']} candidates</h2>";
		
		# End if election is over
		if (!$this->election['active']) {
			echo $html .= "<p>The election is now over, so submissions cannot be made any longer.</p>";
			return false;
		}
		
		# Show the candidate's data
		$table['Election'] = $this->election['name'];
		$table['Election date'] = $this->election['polling date'];
		$table['Ward'] = $wardName;
		$table['Name'] = $candidate['name'];
		$table['Affiliation'] = "<span style=\"color: #{$candidate['colour']};\">" . htmlspecialchars ($candidate['affiliation']) . '</span>';
		$html .= application::htmlTableKeyed ($table, array (), true, 'lines', $allowHtml = true);
		
		# Get the questions for this candidate's ward
		if (!$questions = $this->getQuestions ($candidate['wardId'], $candidate['electionId'])) {
			echo $html .= "\n<p>There are no questions assigned for this {$this->settings['division']} at present.</p>";
			return false;
		}
		
		# Get the responses for this candidate's questions
		if ($responses = $this->getResponses (array_keys ($questions), $candidate['id'])) {
			$responses = $responses[$candidate['id']];
		}
		
		# Prevent updates after the results are visible
		if ($responses && $this->election['resultsVisible']) {
			echo $html .= "<p>You have previously submitted a set of responses, which is now <a href=\"{$this->baseUrl}/{$this->election['id']}/{$candidate['wardId']}/\">shown online</a>, so submissions cannot be made any longer. Thank you for taking part.</p>";
			return false;
		}
		
		# Build up the template
		$total = count ($questions);
		$i = 0;
		$template  = '<p>There ' . ($total == 1 ? 'is 1 question' : "are {$total} questions") . " for this {$this->settings['division']} on which we would invite your response.<br /><strong>Please kindly enter your responses in the boxes below and click the 'submit' button at the end.</strong></p>";
		if ($responses) {$template .= "<p>You are able to update your previous answers below, before they become visible online at {$this->election['visibilityDateTime']}.</p>";}
		$template .= "<p>Your answers will not be visible on this website until {$this->election['visibilityDateTime']}.</p>";
		$template .= '<p>{[[PROBLEMS]]}</p>';
		foreach ($questions as $key => $question) {
			$i++;
			$template .= "\n\n<h4 class=\"question\"> Question {$i}" . ($showIds ? " &nbsp;[survey-id#{$key}]" : '') . '</h4>';
			$question['question'] = htmlspecialchars ($question['question']);
			$template .= $this->questionBox ($question);
			$template .= "\n<p>Your response:</p>";
			$template .= "{question{$key}}";
		}
		$template .= "{_heading1}";
		$template .= '<p>{[[SUBMIT]]}</p>';
		
		# Create the form using the built template
		$form = new form (array (
			'name' => 'questions',
			'formCompleteText' => false,
			'div'	=> false,
			'display'	=> 'template',
			'displayTemplate'	=> $template,
			'displayRestrictions'	=> false,
			'cols' => 90,
			'fixMailHeaders' => true,
		));
		$i = 0;
		foreach ($questions as $key => $question) {
			$i++;
			$fieldname = "question{$key}";
			$fields[] = $fieldname;
			$question['question'] = htmlspecialchars ($question['question']);
			$form->textarea (array (
				'name'			=> $fieldname,
				'title'			=> "Question {$i} - {$question['question']}",
				'required'		=> false,
				'default'		=> (isSet ($responses[$key]) && isSet ($responses[$key]['response']) ? $responses[$key]['response'] : false),
			));
		}
		$form->heading ('p', '<strong>Please check your responses above before pressing the submit button.</strong>');
		
		# Ensure at least one field is submitted
		$form->validation ('either', $fields);
		
		# Cache the verification credentials
		$form->hidden (array (
			'name' => 'verification',
			'values' => array (
				'number' => ($secondStagePosted ? htmlspecialchars ($_POST['questions']['verification']['number']) : $result['number']),
				'ward' => ($secondStagePosted ? htmlspecialchars ($_POST['questions']['verification']['ward']) : $result['ward']),
			),
		));
		
		# Set an e-mail backup record
		$form->setOutputEmail ($this->settings['recipient'], $this->settings['webmaster'], $subjectTitle = str_replace ('&amp;', '&', "Election submission - {$wardName} - {$candidate['name']} - {$candidate['affiliation']}") . ($responses ? ' (update)' : ''), $chosenElementSuffix = NULL, $replyToField = NULL, $displayUnsubmitted = true);
		
		# Process the form or end
		if (!$result = $form->process ($html)) {
			echo $html;
			return false;
		}
		
		# Prepare the data
		$data = array ();
		foreach ($questions as $questionId => $attributes) {
			$data[$questionId] = array (
				"candidate" => $candidate['id'],
				"survey" => $questionId,
				'response' => $result["question{$questionId}"],
			);
		}
		
		# Insert/update the data into the database
		foreach ($data as $questionId => $insert) {
			
			# Update the data if previously submitted
			if (isSet ($responses[$key]) && isSet ($responses[$key]['response'])) {
				$data = array ('response' => $result["question{$questionId}"]);
				$conditions = array ("candidate" => $candidate['id'], "survey" => $questionId,);
				if (!$this->databaseConnection->update ($this->settings['database'], "{$this->settings['tablePrefix']}responses", $data, $conditions)) {
					echo "<p>There was a problem saving your updated responses. Please kindly contact the webmaster.</p>";
					return false;
				}
				
			# Otherwise do a normal insert
			} else {
				if (!$this->databaseConnection->insert ($this->settings['database'], "{$this->settings['tablePrefix']}responses", $insert)) {
					echo "<p>There was a problem saving the responses. Please kindly contact the webmaster.</p>";
					return false;
				}
			}
		}
		
		# Confirm success, resetting the HTML
		$action = ($responses ? 'entering' : 'updating');
		$html  = "\n<div class=\"graybox\">";
		if ($this->election['resultsVisible']) {
			$html .= "\n<p><img src=\"/images/icons/tick.png\" class=\"icon\" /> <strong>Thank you for {$action} your responses.</strong> They are now <a href=\"{$this->baseUrl}/{$this->election['id']}/{$candidate['wardId']}/\">shown online</a>, along with those of other candidates.</p>";
		} else {
			$html .= "\n<p><img src=\"/images/icons/tick.png\" class=\"icon\" /> <strong>Thank you for {$action} your responses.</strong> They will be <a href=\"{$this->baseUrl}/{$this->election['id']}/{$candidate['wardId']}/\">shown online</a>, along with those of other candidates, at {$this->election['visibilityDateTime']}.</p>";
			$html .= "\n<p>You can <a href=\"{$this->baseUrl}/submit/\">update your submission</a> using the same webpage at any time before {$this->election['visibilityDateTime']}.</p>";
		}
		$html .= "\n</div>";
		
		# Extra text
		if ($this->settings['postSubmissionHtml']) {
			$html .= "\n<div class=\"graybox\">";
			$html .= $this->settings['postSubmissionHtml'];
			$html .= "\n</div>";
		}
		
		# Show the HTML
		echo $html;
	}
	
	
	# Function to get active wards across all current elections
	private function getActiveWards ()
	{
		# Get data
		$query = "SELECT
				{$this->settings['database']}.{$this->settings['tablePrefix']}candidates.id,
				{$this->settings['tablePrefix']}candidates.ward as wardId,
				prefix, {$this->settings['tablePrefix']}wards.ward
			FROM {$this->settings['database']}.{$this->settings['tablePrefix']}candidates
			LEFT OUTER JOIN {$this->settings['database']}.{$this->settings['tablePrefix']}elections ON {$this->settings['database']}.{$this->settings['tablePrefix']}candidates.election = {$this->settings['database']}.{$this->settings['tablePrefix']}elections.id
			LEFT OUTER JOIN {$this->settings['database']}.{$this->settings['tablePrefix']}wards ON {$this->settings['database']}.{$this->settings['tablePrefix']}candidates.ward = {$this->settings['database']}.{$this->settings['tablePrefix']}wards.id
			WHERE
				{$this->settings['database']}.{$this->settings['tablePrefix']}elections.endDate >= (CAST(NOW() AS DATE))
			GROUP BY {$this->settings['tablePrefix']}candidates.ward
			ORDER BY {$this->settings['tablePrefix']}candidates.ward
		;";
		if (!$data = $this->databaseConnection->getData ($query, "{$this->settings['database']}.{$this->settings['tablePrefix']}wards")) {
			return false;
		}
		
		# Rearrange as key=>value
		$wards = array ();
		foreach ($data as $key => $values) {
			$wards[$values['wardId']] = $this->wardName ($values);
		}
		
		# Return the data
		return $wards;
	}
	
	
	# Function to verify the candidate and return their details
	private function verifyCandidate ($number, $ward)
	{
		# Get the data
		$query = "SELECT
				{$this->settings['tablePrefix']}candidates.*,
				ward as wardId,
				{$this->settings['tablePrefix']}affiliations.id as affiliationId,
				{$this->settings['tablePrefix']}affiliations.name as affiliation,
				{$this->settings['tablePrefix']}affiliations.colour,
				election as electionId,
				CONCAT(forename,' ',UPPER(surname)) as name
			FROM {$this->settings['database']}.{$this->settings['tablePrefix']}candidates
			LEFT OUTER JOIN {$this->settings['database']}.{$this->settings['tablePrefix']}affiliations ON {$this->settings['database']}.{$this->settings['tablePrefix']}candidates.affiliation = {$this->settings['database']}.{$this->settings['tablePrefix']}affiliations.id
			WHERE
				verification = '" . addslashes ($number) . "'
				AND ward = '" . addslashes ($ward) . "'
		;";
		if (!$data = $this->databaseConnection->getOne ($query)) {
			return false;
		}
		
		# Return the data
		return $data;
	}
	
	
	# Function to show the list of respondents
	private function respondents ()
	{
		# Validate the election
		if (!$this->election) {
			header ('HTTP/1.0 404 Not Found');
			echo $html = '<p>There is no such election. Please check the URL and try again.</p>';
			return false;
		}
		
		# Title
		$html  = "<h2>List of respondents" . ($this->election['active'] && $this->election['respondentsVisible'] && !$this->userIsAdministrator ? ' (so far)' : '') .  "</h2>";
		
		# Get the data
		$query = "SELECT
				{$this->settings['database']}.{$this->settings['tablePrefix']}candidates.id,
				CONCAT({$this->settings['database']}.{$this->settings['tablePrefix']}candidates.forename,' ',UPPER({$this->settings['database']}.{$this->settings['tablePrefix']}candidates.surname)) as name,
				{$this->settings['database']}.{$this->settings['tablePrefix']}wards.id as wardId,
				{$this->settings['database']}.{$this->settings['tablePrefix']}wards.prefix,
				{$this->settings['database']}.{$this->settings['tablePrefix']}wards.ward,
				{$this->settings['database']}.{$this->settings['tablePrefix']}wards.districtCouncil,
				{$this->settings['database']}.{$this->settings['tablePrefix']}affiliations.name as affiliation,
				{$this->settings['database']}.{$this->settings['tablePrefix']}affiliations.colour
			FROM {$this->settings['database']}.{$this->settings['tablePrefix']}responses
			LEFT OUTER JOIN {$this->settings['database']}.{$this->settings['tablePrefix']}candidates ON {$this->settings['database']}.{$this->settings['tablePrefix']}responses.candidate = {$this->settings['database']}.{$this->settings['tablePrefix']}candidates.id
			LEFT OUTER JOIN {$this->settings['database']}.{$this->settings['tablePrefix']}wards ON {$this->settings['database']}.{$this->settings['tablePrefix']}candidates.ward = {$this->settings['database']}.{$this->settings['tablePrefix']}wards.id
			LEFT OUTER JOIN {$this->settings['database']}.{$this->settings['tablePrefix']}affiliations ON {$this->settings['database']}.{$this->settings['tablePrefix']}candidates.affiliation = {$this->settings['database']}.{$this->settings['tablePrefix']}affiliations.id
			WHERE
				election = '{$this->election['id']}'
			ORDER BY ward,surname
		;";
		$respondents = $this->databaseConnection->getData ($query, "{$this->settings['database']}.{$this->settings['tablePrefix']}responses");
		
		# Count the responses
		$total = count ($respondents);
		
		# Regroup the data by ward
		$wards = application::regroup ($respondents, 'wardId', false);
		
		# Get the total number of candidates standing
		$allCandidates = $this->getCandidates (true);
		$totalCandidates = count ($allCandidates);
		$percentageReplied = round (($total / $totalCandidates) * 100);
		
		# Construct a table of response rates by district Council
		$responseRatesByDistrictTable = $this->responseRatesByAspectTable ($allCandidates, $respondents, 'districtCouncil', 'district');
		
		# Construct a table of response rates by affiliation (party)
		$colours = array ();
		foreach ($allCandidates as $candidate) {
			$colours[$candidate['affiliation']] = $candidate['colour'];
		}
		$responseRatesByPartyTable = $this->responseRatesByAspectTable ($allCandidates, $respondents, 'affiliation', 'affiliation (party)', $colours, true);
		
		# Construct the HTML
		$html .= "\n<p>The following is an index to all candidates " . ($responseRatesByDistrictTable ? '' : "({$total}, out of {$totalCandidates} standing, i.e. {$percentageReplied}%)") . " who have submitted public responses. Click on the {$this->settings['division']} name to see them.</p>";
		$html .= $responseRatesByDistrictTable;
		$html .= $responseRatesByPartyTable;
		$html .= "\n<p><em>This list is ordered by {$this->settings['division']} and then surname.</em></p>";
		foreach ($this->wards as $ward => $attributes) {
			$html .= "<h4><a href=\"{$this->baseUrl}/{$this->election['id']}/{$ward}/\">{$this->wards[$ward]['_name']} <span>[view responses]</span></a>:</h4>";
			if (!isSet ($wards[$ward])) {
				$html .= "\n<p class=\"noresponse faded\"><em>No candidate for {$this->wards[$ward]['_name']} has yet submitted a response.</em></p>";
			} else {
				$candidates = $wards[$ward];
				$candidateList = array ();
				foreach ($candidates as $candidate) {
					$affilation = "<span style=\"color: #{$candidate['colour']};\">" . htmlspecialchars ($candidate['affiliation']) . '</span>';
					$candidateList[] = "{$candidate['name']} ({$affilation})";
				}
				$html .= application::htmlUl ($candidateList);
			}
		}
		
		# Show the HTML
		echo $html;
	}
	
	
	# Function to show the status of Cabinet members restanding in this election
	private function cabinet ()
	{
		# Validate the election
		if (!$this->election) {
			header ('HTTP/1.0 404 Not Found');
			echo $html = '<p>There is no such election. Please check the URL and try again.</p>';
			return false;
		}
		
		# End if no Cabinet members restanding in this election
		if (!$this->cabinetRestanding) {
			header ('HTTP/1.0 404 Not Found');
			echo $html = '<p>There are no Cabinet members in wards we are surveying restanding in this election. Please check the URL and try again.</p>';
			return false;
		}
		
		# Get the responses
		$candidateIds = array_keys ($this->cabinetRestanding);
		$responses = $this->getResponses (false, $candidateIds);
		
		# Create a table
		$cabinetMembers = array ();
		foreach ($this->cabinetRestanding as $candidateId => $candidate) {
			$surveyLink = "{$this->baseUrl}/{$this->election['id']}/{$candidate['wardId']}/";
			$cabinetMembers[] = array (
				'Candidate' => str_replace (' &nbsp;(', '<br />(', $candidate['_name']),
				'Responded?' => (isSet ($responses[$candidateId]) ? "<a href=\"{$surveyLink}\"><strong>Yes - view responses</strong></a>" : '<span class="warning"><strong>No</strong>, the candidate ' . ($this->election['active'] ? 'has not (yet) responded' : 'did not respond') . '</span>'),
				'Post' => $candidate['cabinetRestanding'],
				'Ward' => "<a href=\"{$surveyLink}\">" . $candidate['ward'] . '</a>',
			);
		}
		
		# Compile the HTML
		$html  = "\n<h2>Cabinet members in surveyed wards restanding in this election</h2>";
		$html .= "\n<p>The <strong>Cabinet</strong> is the Executive of the Council, formed of members of the political party in power. They implement and drive the Council's policy. As such, their views arguably have greater effect than any other Councillors.</p>";
		$html .= "\n<p>The listing below shows all the Cabinet members in wards we are surveying who are restanding in this election, and whether they have responded to our survey or not.</p>";
		$html .= application::htmlTable ($cabinetMembers, array (), 'lines regulated', $keyAsFirstColumn = false, false, $allowHtml = true, $showColons = true);
		
		# Show the HTML
		echo $html;
	}
	
	
	# Function to create a table of response rates by aspect (e.g. District)
	private function responseRatesByAspectTable ($allCandidates, $respondents, $aspect, $aspectLabel, $colours = array (), $showBooleanWhenSingleCandidateStanding = false)
	{
		# Regroup the datasets by aspect
		$candidatesByAspectStanding = application::regroup ($allCandidates, $aspect, false);
		$candidatesByAspectResponded = application::regroup ($respondents, $aspect, false);
		
		# If only one grouping, end, as there is no need for a table
		if (count ($candidatesByAspectStanding) == 1) {return;}
		
		# Construct the aspect label
		$aspectLabel = 'Response rates by ' . $aspectLabel;
		
		# Assemble the data
		$responseRatesByAspect = array ();
		$totalResponses = 0;
		$totalCandidates = 0;
		foreach ($candidatesByAspectStanding as $grouping => $candidatesThisAspect) {
			
			# Exit if an aspect name is missing (e.g. missing District Council name)
			#!# Need to report this to the Webmaster as indicating missing data
			if (!strlen ($grouping)) {return false;}
			
			# Count the figures
			$totalResponsesThisGrouping = (isSet ($candidatesByAspectResponded[$grouping]) ? count ($candidatesByAspectResponded[$grouping]) : 0);
			$totalCandidatesThisGrouping = count ($candidatesThisAspect);
			$percentageThisGroupingReplied = round (($totalResponsesThisGrouping / $totalCandidatesThisGrouping) * 100) . '%';
			
			# If required, convert a single candidate response of 100% to 'Yes'
			if ($showBooleanWhenSingleCandidateStanding) {
				if ($totalCandidatesThisGrouping == 1) {
					$percentageThisGroupingReplied = ($totalResponsesThisGrouping ? 'Yes' : '0%');
				}
			}
			
			# Add to the global totals
			$totalResponses += $totalResponsesThisGrouping;
			$totalCandidates += $totalCandidatesThisGrouping;
			
			# Register this in the table
			$responseRatesByAspect[$grouping] = array (
				$aspectLabel	=> ($colours ? "<span style=\"color: #{$colours[$grouping]};\">" . htmlspecialchars ($grouping) . '</span>' : htmlspecialchars ($grouping)),
				'Response rate'	=> '<strong>' . $percentageThisGroupingReplied . '</strong>',
				'Responses'		=> $totalResponsesThisGrouping,
				'Candidates'	=> $totalCandidatesThisGrouping,
			);
		}
		
		# Sort by district name
		ksort ($responseRatesByAspect);
		
		# Add the global totals
		$percentageReplied = round (($totalResponses / $totalCandidates) * 100) . '%';
		$responseRatesByAspect['Total'] = array (
			$aspectLabel	=> '<strong>Total</strong>',
			'Response rate'	=> '<strong>' . $percentageReplied . '</strong>',
			'Responses'		=> $totalResponses,
			'Candidates'	=> $totalCandidates,
		);
		
		# Compile as a table
		$html = application::htmlTable ($responseRatesByAspect, array (), 'responserates lines compressed', $keyAsFirstColumn = false, false, $allowHtml = true, $showColons = true);
		
		# Return the HTML
		return $html;
	}
	
	
	# Function used by assignConfiguration to merge defaults with supplied config
	private function mergeConfiguration ($defaults, $suppliedArguments)
	{
		# Start a list of errors (so that all setup errors are shown at once)
		$errors = array ();
		
		# Merge the defaults
		$arguments = array ();
		foreach ($defaults as $argument => $defaultValue) {
			
			# Sanity check: fields marked NULL or array() in the defaults MUST be supplied in the config and must not be an empty string
			if ((is_null ($defaultValue) || $defaultValue === array ()) && (!isSet ($suppliedArguments[$argument]) || !strlen ($suppliedArguments[$argument]))) {
				$errors[] = "No '<strong>{$argument}</strong>' has been set in the configuration.";
				
			# Having passed the check, reverting to the default value if no value is specified in the supplied config
			} else {
				$arguments[$argument] = (isSet ($suppliedArguments[$argument]) ? $suppliedArguments[$argument] : $defaultValue);
			}
		}
		
		# Assign and return the errors if there are any
		if ($errors) {
			$this->errors += $errors;
			return false;
		}
		
		# Return the arguments
		return $arguments;
	}
	
	
	# Admin area
	private function admin ()
	{
		# Ensure the user is an administrator
		if (!$this->userIsAdministrator && !$this->settings['overrideAdmin']) {
			echo $html = '<p>You must be signed in as an administrator to access this page.</p>';
			return false;
		}
		
		# Start the HTML
		$html  = "\n<h2>Administrative functions</h2>";
		$html .= "\n<p><em>This section is accessible only to Administrators.</em></p>";
		
		# Candidate control
		$html .= "\n<h3>Candidate control</h3>
		<ul>
			<li><a href=\"{$this->baseUrl}/admin/allquestions.html\">See every question available in the database</a></li>
			<li><a href=\"{$this->baseUrl}/submit/\">Use/view the candidate submission form</a></li>
			<li><a href=\"{$this->baseUrl}/admin/letters.html\">See the printable letters to candidates</a></li>
			<li><a href=\"{$this->baseUrl}/admin/elected.html\">Specify the elected candidates</a></li>
		</ul>";
		
		# Data import
		$html .= "\n<h3>Data import</h3>
		<ul>
			<li><a href=\"{$this->baseUrl}/admin/addelection.html\">Add an election</a></li>
			<li><a href=\"{$this->baseUrl}/admin/addcandidates.html\">Add candidates</a></li>
			<li><a href=\"{$this->baseUrl}/admin/addquestions.html\">Add questions</a></li>
			<li><a href=\"{$this->baseUrl}/admin/addsurveys.html\">Add surveys</a></li>
			<li><a href=\"{$this->baseUrl}/admin/allocations.html\">Convert an allocations spreadsheet into SQL</a></li>
		</ul>";
		
		# Show the HTML
		echo $html;
	}
	
	
	# List of all questions in the entire database
	private function allquestions ()
	{
		# Ensure the user is an administrator
		if (!$this->userIsAdministrator && !$this->settings['overrideAdmin']) {
			echo $html = '<p>You must be signed in as an administrator to access this page.</p>';
			return false;
		}
		
		# Ensure that an election is not being supplied
		if ($this->election) {
			header ('HTTP/1.0 404 Not Found');
			echo $html = '<p>This listing is not election-specific. Please check the URL and try again.</p>';
			return false;
		}
		
		# Get data
		$html = $this->showQuestions ();
		
		# Show the HTML
		echo $html;
	}
	
	
	# Function to add an election
	public function addelection ()
	{
		# Ensure the user is an administrator
		if (!$this->userIsAdministrator && !$this->settings['overrideAdmin']) {
			echo $html = '<p>You must be signed in as an administrator to access this page.</p>';
			return false;
		}
		
		# Start the HTML
		$html  = "\n<h2>Add an election</h2>";
		
		# Get current IDs
		$currentIds = $this->databaseConnection->selectPairs ($this->settings['database'], 'elections_elections', array (), array ('id'), true, $orderBy = 'id');
		
		# Create a new form
		require_once ('ultimateForm.php');
		$form = new form (array (
			'databaseConnection' => $this->databaseConnection,
			'picker' => true,
		));
		$form->dataBinding (array (
			'database'	=> $this->settings['database'],
			'table'		=> 'elections_elections',
			'attributes' => array (
				'id' => array ('current' => $currentIds, 'regexp' => '^[a-z0-9]+$'),
			),
		));
		#!# Need to add constraints to ensure date ordering is correct
		if (!$result = $form->process ($html)) {
			echo $html;
			return;
		}
		
		# Insert the election
		$this->databaseConnection->insert ($this->settings['database'], 'elections_elections', $result);
		
		# Confirm success
		$html  = "\n<p><img src=\"/images/icons/tick.png\" class=\"icon\" /> The <a href=\"{$this->baseUrl}/{$result['id']}/\">election</a> has been added.</p>";
		$html .= "\n<p>You may wish to add data for it.</p>";
		
		# Show the HTML
		echo $html;
	}
	
	
	# Function to add candidates for an election
	public function addcandidates ()
	{
		# Ensure the user is an administrator
		if (!$this->userIsAdministrator && !$this->settings['overrideAdmin']) {
			echo $html = '<p>You must be signed in as an administrator to access this page.</p>';
			return false;
		}
		
		# Start the HTML
		$html  = "\n<h2>Add candidates</h2>";
		$html .= "\n<p>Note that this will replace the data for the selected election.</p>";
		
		# Define the required fields
		$requiredFields = array ('forename', 'surname', 'ward', 'affiliation', 'address');
		
		# Create a new form
		require_once ('ultimateForm.php');
		$form = new form (array (
			'databaseConnection' => $this->databaseConnection,
			'picker' => true,
		));
		$form->select (array (
			'name'			=> 'election',
			'title'			=> 'Which election',
			'values'		=> $this->getElectionNames (),
			'required'		=> true,
		));
		$form->textarea (array (
			'name'			=> 'data',
			'title'			=> 'Enter the candidate data, pasted from your spreadsheet, which must contain headings (in order): <strong>' . implode ('</strong>, <strong>', $requiredFields) . '</strong>',
			'required'		=> true,
			'cols'			=> 80,
			'rows'			=> 10,
		));
		$data = array ();
		if ($unfinalisedData = $form->getUnfinalisedData ()) {
			if ($unfinalisedData['election'] && $unfinalisedData['data']) {
				if (!$data = $this->getTsvData ($unfinalisedData['data'], $requiredFields, $errorMessage)) {
					$form->registerProblem ('tsvinvalid', $errorMessage);
				}
				
				# Verify affiliations
				$affiliations = $this->getAffiliationNames ();
				foreach ($data as $candidate) {
					if (!array_key_exists ($candidate['affiliation'], $affiliations)) {
						$form->registerProblem ('unknownaffiliation', 'The affiliation ' . htmlspecialchars ($candidate['affiliation']) . ' was not recognised; please register the affiliation if it is correct.');
						break;
					}
				}
				
				# Verify ward names
				$wards = $this->getWardNames ();
				foreach ($data as $candidate) {
					if (!array_key_exists ($candidate['ward'], $wards)) {
						$form->registerProblem ('unknownward', 'The ward ' . htmlspecialchars ($candidate['ward']) . ' was not recognised; please register the ward if it is correct.');
						break;
					}
				}
			}
		}
		if (!$result = $form->process ($html)) {
			echo $html;
			return;
		}
		
		# Process the data to add fixed fields
		foreach ($data as $index => $candidate) {
			
			# Add election ID
			$data[$index]['election'] = $result['election'];
			
			# Add random verification number for candidate login; note that uniqueness across the dataset is not actually required
			$data[$index]['verification'] = rand (100000, 999999);
		}
		
		# Clear any existing data
		$this->databaseConnection->delete ($this->settings['database'], 'elections_candidates', array ('election' => $result['election']));
		
		# Insert the data
		if (!$this->databaseConnection->insertMany ($this->settings['database'], 'elections_candidates', $data)) {
			$error = $this->databaseConnection->error ();
			$html  = "\n<p><img src=\"/images/icons/cross.png\" class=\"icon\" /> Sorry, an error occured. The database server said:</p>";
			$html .= "\n<p><tt>" . $error[2] . '</tt></p>';
			echo $html;
			return false;
		}
		
		# Confirm success
		$total = count ($data);
		#!# Ideally the message should make clear if this was entirely new or a replacement
		$html  = "\n<p><img src=\"/images/icons/tick.png\" class=\"icon\" /> The candidate data (total: {$total}) for this <a href=\"{$this->baseUrl}/{$result['election']}/\">election</a> has been entered.</p>";
		
		# Show the HTML
		echo $html;
	}
	
	
	# Function to process submitted TSV batch string and assemble the data from it
	private function getTsvData ($tsv, $requiredFields, &$errorMessage = '')
	{
		# Parse the TSV string
		require_once ('csv.php');
		$data = csv::tsvToArray (trim ($tsv), $firstColumnIsId = false, $firstColumnIsIdIncludeInData = true);
		
		# Ensure headers are valid and that required headers are present
		foreach ($data as $filename => $metadata) {
			$missingRequiredFields = array_diff ($requiredFields, array_keys ($metadata));
			break;	// Only check the first row, i.e. the heading row
		}
		if ($missingRequiredFields) {
			$errorMessage = "The fields in the pasted data do not match the specification noted above. Please correct the spreadsheet and try again.";
			return false;
		}
		
		# Return the data
		return $data;
	}
	
	
	# Helper function to get election names
	private function getElectionNames ()
	{
		# Assemble the elections list
		$electionNames = array ();
		foreach ($this->elections as $key => $value) {
			$electionNames[$key] = $value['name'];
		}
		
		# Return the list
		return $electionNames;
	}
	
	
	# Function to add questions
	public function addquestions ()
	{
		# Ensure the user is an administrator
		if (!$this->userIsAdministrator && !$this->settings['overrideAdmin']) {
			echo $html = '<p>You must be signed in as an administrator to access this page.</p>';
			return false;
		}
		
		# Define number of recent questions to show
		$mostRecent = 10;
		
		# Start the HTML
		$html  = "\n<h2>Add questions</h2>";
		$html .= "\n<p>In this section, you can add questions that can then be used in a survey. Note that questions have to be added one at a time.</p>";
		$html .= "\n<p>The {$mostRecent} most recently-added questions are shown below.</p>";
		
		# Create a new form
		require_once ('ultimateForm.php');
		$form = new form (array (
			'databaseConnection' => $this->databaseConnection,
			'picker' => true,
			'size' => 80,
			'rows' => 7,
			'cols' => 80,
		));
		$form->dataBinding (array (
			'database'	=> $this->settings['database'],
			'table'		=> 'elections_questions',
			'intelligence' => true,
			'size'	=> 80,	#!# This is here due to a bug in ultimateForm
			'attributes' => array (
				
			),
		));
		#!# Need to check that highlight text appears in the question
		if (!$result = $form->process ($html)) {
			$html .= $this->recentlyAddedQuestions ();
			echo $html;
			return;
		}
		
		# Insert the question
		if (!$this->databaseConnection->insert ($this->settings['database'], 'elections_questions', $result)) {
			$html  = "\n<p><img src=\"/images/icons/cross.png\" class=\"icon\" /> Sorry, an error occured.</p>";
			echo $html;
			return false;
		}
		$questionId = $this->databaseConnection->getLatestId ();
		
		# Confirm success
		$html  = "\n<p><img src=\"/images/icons/tick.png\" class=\"icon\" /> The question has been added, as shown below.</p>";
		$html .= "\n<p>Do you wish to <a href=\"{$this->baseUrl}/admin/" . __FUNCTION__ . ".html\">add another</a>?</p>";
		$html .= $this->recentlyAddedQuestions ($questionId);
		
		# Show the HTML
		echo $html;
	}
	
	
	# Function to create a list of questions most recently-added to the database
	private function recentlyAddedQuestions ($highlightQuestionId = false)
	{
		# Get the latest data, but ordered most recent last
		$recentQuestions = $this->databaseConnection->selectPairs ($this->settings['database'], 'elections_questions', array (), array ('id', 'question'), true, $orderBy = 'id DESC', $mostRecent = 10);
		$recentQuestions = array_reverse ($recentQuestions, true);
		
		# Assemble as a list
		$list = array ();
		foreach ($recentQuestions as $id => $recentQuestion) {
			$list[$id] = '#' . $id . ': ' . htmlspecialchars ($recentQuestion);
		}
		
		# Highlight one question if present
		if (isSet ($list[$highlightQuestionId])) {
			$list[$highlightQuestionId] = "<strong>{$list[$highlightQuestionId]}</strong>";
		}
		
		# Compile the HTML
		$html  = "\n<h3>Most recently-added questions</h3>";
		$html .= "\n<p>Here are the {$mostRecent} questions most recently-added to the database:</p>";
		$html .= application::htmlUl ($list, 0, 'spaced');
		
		# Surround in a div
		$html = "\n<div class=\"graybox\">{$html}</div>";
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to add surveys
	public function addsurveys ()
	{
		# Ensure the user is an administrator
		if (!$this->userIsAdministrator && !$this->settings['overrideAdmin']) {
			echo $html = '<p>You must be signed in as an administrator to access this page.</p>';
			return false;
		}
		
		# Define number of recent questions to show
		$mostRecent = 10;
		
		# Start the HTML
		$html  = "\n<h2>Add questions</h2>";
		$html .= "\n<p>In this section, you can add questions that can then be used in a survey. Note that questions have to be added one at a time.</p>";
		$html .= "\n<p>The {$mostRecent} most recently-added questions are shown below.</p>";
		
		# Create a new form
		require_once ('ultimateForm.php');
		$form = new form (array (
			'databaseConnection' => $this->databaseConnection,
		));
		$form->select (array (
			'name'			=> 'election',
			'title'			=> 'Which election',
			'values'		=> $this->getElectionNames (),
			'required'		=> true,
		));
		$form->select (array (
			'name'			=> 'ward',
			'title'			=> 'Which ward',
			'values'		=> $this->getWardNames (),
			'required'		=> true,
		));
		$form->select (array (
			'name'			=> 'questions',
			'title'			=> 'Questions, in order, for this survey',
			'values'		=> $this->getQuestionTexts (),
			'required'		=> true,
			'multiple'		=> true,
			'expandable'	=> true,
			'output'		=> array ('processing' => 'compiled'),
		));
		if (!$result = $form->process ($html)) {
			echo $html;
			return;
		}
		
		# Post-process the multiple select output format in ultimateForm
		#!# This annoyance in ultimateForm really needs to be fixed - 'rawcomponents' is usually wrong, and compiled is in an unhelpful format for processing
		$result['questions'] = explode (",\n", $result['questions']);
		
		#!# Ordering gets broken
		
		# Define standard data for each entry in the survey
		$constraints = array (
			'election'	=> $result['election'],
			'ward'		=> $result['ward'],
		);
		
		# Construct the list of entries for the survey
		$data = array ();
		foreach ($result['questions'] as $index => $question) {
			$data[$index] = $constraints;
			$data[$index]['question'] = $question;
		}
		
		# Clear any existing data
		$this->databaseConnection->delete ($this->settings['database'], 'elections_surveys', $constraints);
		
		# Insert the data
		if (!$this->databaseConnection->insertMany ($this->settings['database'], 'elections_surveys', $data)) {
			$html  = "\n<p><img src=\"/images/icons/cross.png\" class=\"icon\" /> Sorry, an error occured.</p>";
			echo $html;
			return false;
		}
		
		# Confirm success
		$html  = "\n<p><img src=\"/images/icons/tick.png\" class=\"icon\" /> The <a href=\"{$this->baseUrl}/{$result['election']}/{$result['ward']}/\">survey</a> has been added.</p>";
		$html .= "\n<p>Do you wish to <a href=\"{$this->baseUrl}/admin/" . __FUNCTION__ . ".html\">add another</a>?</p>";
		
		# Show the HTML
		echo $html;
	}
	
	
	# Function to get a list of ward IDs and names
	private function getWardNames ()
	{
		return $this->databaseConnection->selectPairs ($this->settings['database'], 'elections_wards', array (), array ('id', "CONCAT_WS(' ', prefix, ward) AS name"), true, $orderBy = 'name');
	}
	
	
	# Function to get a list of affiliation IDs and names
	private function getAffiliationNames ()
	{
		return $this->databaseConnection->selectPairs ($this->settings['database'], 'elections_affiliations', array (), array ('id', 'name'), true, $orderBy = 'name');
	}
	
	
	# Function to get a list of question IDs and texts
	private function getQuestionTexts ()
	{
		return $this->databaseConnection->selectPairs ($this->settings['database'], 'elections_questions', array (), array ('id', "CONCAT(id, ': ', SUBSTRING(question, 1, 70), ' ...') AS text"), true, $orderBy = 'id');
	}
	
	
	# Admin helper function to create SQL INSERTS
	private function allocations ()
	{
		# Start the HTML
		$html  = '<h2>Create the question allocation SQL</h2>';
		
		# Ensure the user is an administrator
		if (!$this->userIsAdministrator && !$this->settings['overrideAdmin']) {
			echo $html  = '<p>You must be signed in as an administrator to access this page.</p>';
			return false;
		}
		
		# Create the form
		require_once ('ultimateForm.php');
		$form = new form (array (
			'formCompleteText' => false,
		));
		$form->select (array (
			'name'			=> 'election',
			'title'			=> 'Which election',
			'values'		=> $this->getElectionNames (),
		));
		$form->textarea (array (
			'name'			=> 'allocations',
			'title'			=> 'Enter the allocations, as wardname[tab]q1[tab]q2, etc., per line',
			'required'		=> true,
			'cols'			=> 80,
			'rows'			=> 10,
		));
		
		# Process the result
		if (!$result = $form->process ()) {return false;}
		
		# Compile the SQL
		$sql  = "INSERT INTO {$this->settings['database']}.{$this->settings['tablePrefix']}surveys (election,ward,question) VALUES \n";
		$wards = explode ("\n", trim ($result['allocations']));
		$set = array ();
		foreach ($wards as $ward) {
			list ($wardId, $questions) = preg_split ("/\s+/", trim ($ward), 2);
			$allocations = preg_split ("/\s+/", trim ($questions));
			foreach ($allocations as $allocation) {
				$set[] .= "\t('{$result['election']}', '{$wardId}', {$allocation})";
			}
		}
		$sql .= implode ($set, ",\n");
		$sql .= "\n;";
		
		# Show the SQL
		echo nl2br (htmlspecialchars ($sql));
	}
	
	
	# Function to produce printable letters to candidates containing questions for an election
	private function letters ()
	{
		# Start the HTML
		$html  = '<h2>Printable letters to candidates</h2>';
		
		# Ensure the user is an administrator
		if (!$this->userIsAdministrator && !$this->settings['overrideAdmin']) {
			echo $html .= '<p>You must be signed in as an administrator to access this page.</p>';
			return false;
		}
		
		# Ensure there is an election supplied
		if (!$this->election) {
			$html .= "\n<p>Please select which election:</p>";
			$html .= $this->listElections ($this->elections, true, false, 'letters.html');
			echo $html;
			return false;
		}
		
		# Get the candidates
		if (!$candidates = $this->getCandidates (true)) {
			echo $html .= '<p>There are no candidates at present.</p>';
			return false;
		}
		
		# Get the surveys
		if (!$surveys = $this->getQuestions (false, $this->election['id'])) {
			echo $html .= '<p>There are no questions at present.</p>';
			return false;
		}
		
		# Increase allowed execution time
		ini_set ('max_execution_time', 3600);
		
		# Regroup
		$surveys = application::regroup ($surveys, 'wardId', $removeGroupColumn = false);
		$candidates = application::regroup ($candidates, 'wardId', $removeGroupColumn = false);
		
		# Loop through by ward having surveys
		foreach ($surveys as $ward => $questionnaire) {
			
			# Miss out if no candidates in a ward
			if (!isSet ($this->wards[$ward])) {continue;}
			
			# Loop through by candidate
			foreach ($candidates[$ward] as $key => $candidate) {
				
				# Assemble the ward name
				$wardName = $this->wardName ($candidate);
				
				# Avoid a house number appearing on its own
				$candidate['address'] = preg_replace ('/^([0-9]+[a-zA-Z]?)\,/', '$1', $candidate['address']);
				
				# Determine if a screenshot is available, showing less internet-aware users how to enter a URL
				$screenshotHtml = false;
				$screenshotLocation = $this->baseUrl . '/screenshot.png';
				if (file_exists ($_SERVER['DOCUMENT_ROOT'] . $screenshotLocation)) {
					list ($width, $height, $type, $attributes) = getimagesize ($_SERVER['DOCUMENT_ROOT'] . $screenshotLocation);
					$screenshotHtml = "<img src=\"{$screenshotLocation}\" {$attributes} />";
				}
				
				# Show the letterhead
				$html .= "
					<table class=\"header\" cellpadding=\"0\" cellspacing=\"0\">
						<tr>
							<td class=\"address\">
								{$candidate['_nameUncolouredNoAffiliation']},<br />
								" . str_replace (',', ',<br />', htmlspecialchars ($candidate['address'])) . "
							</td>
							<td class=\"letterhead\">
								{$this->settings['letterheadHtml']}
								<p>" . date ('jS F Y') . "</p>
							</td>
						</tr>
						<tr>
							<td colspan=\"2\">
								<p>&nbsp;</p>
								<p>&nbsp;</p>
								<p>Dear " . $wardName . ' ' . $this->settings['division'] . " candidate,</p>
								" . $this->settings['organisationIntroductionHtml'] . "
								<p>You can write back to us via the contact details above. However, if you have internet access, it would save us time if you could directly submit your responses via the automated facility on our website, if possible. Just go to: <u>" . ((substr ($_SERVER['SERVER_NAME'], 0, 4) != 'www.') ? 'http://' : '') . "{$_SERVER['SERVER_NAME']}{$this->baseUrl}/submit/</u> and enter your verification number: <strong>{$candidate['verification']}</strong>. The website version also contains links giving further information.</p>
								" . $screenshotHtml . "
								<p>Many thanks for your time.<br />Yours sincerely,</p>
								<p>" . htmlspecialchars ($this->settings['letterSignatureName']) . ",<br />" . htmlspecialchars ($this->settings['letterSignaturePosition']) . ", " . htmlspecialchars ($this->settings['letterSignatureOrganisationName']) . "</p>
								<p>&nbsp;</p>
							</td>
						</tr>
					</table>
				";
				
				# Show the questions
				$i = 0;
				foreach ($questionnaire as $key => $question) {
					$i++;
					$html .= '<hr />';
					$question['question'] = htmlspecialchars ($question['question']);
					$html .= "\n<p><strong>Question {$i}</strong>: {$question['question']}</p>";
					$html .= "\n" . $this->formatLinks ($question['links'], true);
					$html .= "\n<p>Your response (ideally, please submit this online - see above) :</p>";
					$html .= "<p>&nbsp;</p><p>&nbsp;</p>";
				}
				
				# Page break
				$html .= "<p>&nbsp;</p><p>&nbsp;</p><p>(End of survey)</p>";
				$html .= $this->settings['postSubmissionHtmlLetters'];
				$html .= "<div class=\"pagebreak\"></div>";
			}
		}
		
		# Show the (printable) HTML
		echo $html;
	}
	
	
	# Function to specify the elected candidates
	private function elected ()
	{
		# Start the HTML
		$html  = '<h2>Specify the elected candidates</h2>';
		
		# Ensure the user is an administrator
		if (!$this->userIsAdministrator && !$this->settings['overrideAdmin']) {
			echo $html .= '<p>You must be signed in as an administrator to access this page.</p>';
			return false;
		}
		
		# Ensure there is an election supplied
		if (!$this->election) {
			$html .= "\n<p>Please select which election:</p>";
			$html .= $this->listElections ($this->elections, true, false, 'elected.html');
			echo $html;
			return false;
		}
		
		# Ensure the election is no longer active
		if ($this->election['active']) {
			echo $html .= '<p>This cannot be done until after the election is over.</p>';
			return false;
		}
		
		# Get the candidates
		if (!$candidates = $this->getCandidates (true)) {
			echo $html .= '<p>There are no candidates at present.</p>';
			return false;
		}
		
		# Arrange the candidates by ward
		$candidates = application::regroup ($candidates, 'ward', false);
		
		# Arrange to be added to a multi-select
		$candidatesByWard = array ();
		$elected = array ();	// From a previous import - helpful to maintain this to avoid re-entry of every ward if there was a mistake
		foreach ($candidates as $wardName => $candidatesThisWard) {
			$elected[$wardName] = array ();
			foreach ($candidatesThisWard as $candidateId => $candidate) {
				$candidatesByWard[$wardName][$candidateId] = str_replace ('&nbsp;', '', $candidate['_nameUncoloured']);
				if ($candidate['elected']) {
					$elected[$wardName][] = $candidateId;
				}
			}
		}
		
		# Create a form
		require_once ('ultimateForm.php');
		$form = new form (array (
			'displayRestrictions'	=> false,
			'nullText' => false,
			'formCompleteText' => 'The results have been saved. These are now visible on the Ward and question pages.',
			'unsavedDataProtection' => true,
		));
		$form->heading ('p', 'Use this form to specify the elected candidates, which will be marked in the listings as having been elected.');
		$i = 0;
		foreach ($candidatesByWard as $wardName => $candidates) {
			$form->select (array (
				'name'			=> 'ward' . $i++,
				'title'			=> $wardName,
				'values'		=> $candidates,
				'default'		=> $elected[$wardName],
				// 'required'		=> true,
				'multiple'		=> true,
				'expandable'	=> true,
			));
		}
		
		# Process the form
		if ($result = $form->process ($html)) {
			
			# Compile into a list
			$electedCandidates = array ();
			foreach ($result as $ward => $candidates) {
				foreach ($candidates as $candidateId => $isElected) {
					if ($isElected) {
						$electedCandidates[] = $candidateId;
					}
				}
			}
			
			# Clear any previous specification for this election
			$this->databaseConnection->update ($this->settings['database'], "{$this->settings['tablePrefix']}candidates", array ('elected' => NULL), array ('election' => $this->election['id']));
			
			# Add the winning candidates
			$in = implode (',', $electedCandidates);
			$query = "UPDATE {$this->settings['database']}.{$this->settings['tablePrefix']}candidates SET elected = 1 WHERE id IN({$in});";
			$this->databaseConnection->query ($query);
		}
		
		# Show the HTML
		echo $html;
	}
}

?>
