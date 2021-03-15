<?php

#!# Add some sort of testing candidate system

# Class to create an elections lobbying system
class elections
{
	# Defaults; NULL indicates a required argument
	var $defaults = array (
		
		# Application
		'applicationName' => 'Elections',
		
		# GUI header/footer, if required
		'headerHtml' => false,
		'footerHtml' => false,
		
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
		
		# Libraries
		'richtextEditorBasePath' => '/_ckeditor/',
		
		# Date/times
		'resultsVisibleTime' => '21:00:00',
		
		# Text
		'welcomeTextHtml' => NULL,
		'introductoryTextHtml' => NULL,
		'imprint' => NULL,
		'division' => 'ward',	// E.g. could be 'district' or 'constituency' instead
		'divisionPlural' => 'wards',
		
		# Pre-formatted letters
		'organisationConstituentsType' => 'members',	// E.g. could be 'supporters' or similar instead
		'letterSignaturePosition' => 'Committee member',
		'letterSignatureOrganisationName' => NULL,
		
		# Post-submission HTML
		'postSubmissionHtml' => false,
		'postSubmissionHtmlLetters' => false,
		
		# E-mails
		'emailSubject' => false,
		'emailFrom' => false,
		'emailCc' => false,
		
		# Temporary override of admin privileges
		'overrideAdmin' => false,
		
		# Whether to show candidate addresses
		'showAddresses' => false,
		
		# Whether to list archived elections
		'listArchived' => true,
	);
	
	
	# Actions (pages) registry
	public function actions ()
	{
		# Define and return the actions
		return $actions = array (
			'home'			=> array (
				'description' => false,
				'url' => '',
			),
			'overview'		=> array (
				'description' => false,
				'url' => '',
				'election' => true,
			),
			'ward'			=> array (
				'description' => false,
				'url' => '%ward/',
				'election' => true,
			),
			'questions'		=> array (
				'description' => false,
				'url' => 'questions/',
				'election' => true,
			),
			'respondents'	=> array (
				'description' => false,
				'url' => 'respondents.html',
				'election' => true,
			),
			'cabinet'		=> array (
				'description' => 'Cabinet members in surveyed wards restanding in this election',
				'url' => 'cabinet.html',
				'election' => true,
			),
			'admin'			=> array (
				'description' => false,
				'url' => 'admin/',
				'administrator' => true,
			),
			'addelection'	=> array (
				'description' => 'Start an election survey',
				'url' => 'admin/addelection.html',
				'administrator' => true,
				'admingroup' => 'election',
				'election' => false,
			),
			'editelection'	=> array (
				'description' => 'Edit settings for an election',
				'url' => 'admin/editelection.html',
				'administrator' => true,
				'admingroup' => 'election',
				'election' => true,
			),
			'addward'		=> array (
				'description' => 'Add a ward/division',
				'url' => 'admin/addward.html',
				'administrator' => true,
				'admingroup' => 'wardaffilations',
			),
			#!# Not present
			'editward'		=> array (
				'description' => 'Show/edit existing wards/divisions',
				'url' => 'admin/editward.html',
				'administrator' => true,
				'admingroup' => 'wardaffilations',
			),
			'addaffiliations'	=> array (
				'description' => 'Add details of a political party/group',
				'url' => 'admin/addaffiliations.html',
				'administrator' => true,
				'admingroup' => 'wardaffilations',
			),
			#!# Not present
			'editaffiliations'	=> array (
				'description' => 'Show/edit existing political parties/groups',
				'url' => 'admin/editaffiliations.html',
				'administrator' => true,
				'admingroup' => 'wardaffilations',
			),
			'addquestions'	=> array (
				'description' => 'Add a question',
				'url' => 'admin/addquestions.html',
				'administrator' => true,
				'admingroup' => 'questions',
			),
			'allquestions'	=> array (
				'description' => 'List all questions available',
				'url' => 'admin/allquestions.html',
				'administrator' => true,
				'admingroup' => 'questions',
			),
			'deletequestions'	=> array (
				'description' => 'Delete a question',
				'url' => 'admin/deletequestions.html',
				'administrator' => true,
				'admingroup' => 'questions',
			),
			'addsurveys'	=> array (
				'description' => 'Create a survey for an area',
				'url' => 'admin/addsurveys.html',
				'administrator' => true,
				'admingroup' => 'surveys',
				//'election' => true,
			),
			#!# Not present
			'editsurveys'	=> array (
				'description' => 'Show/edit existing surveys',
				'url' => 'admin/editsurveys.html',
				'administrator' => true,
				'admingroup' => 'surveys',
				'election' => true,
			),
			'allocations'	=> array (
				'description' => 'Convert an questions allocations spreadsheet into SQL',
				'url' => 'admin/allocations.html',
				'administrator' => true,
				'admingroup' => 'surveys',
				//'election' => true,
			),
			'addcandidates'	=> array (
				'description' => 'Add candidates',
				'url' => 'admin/addcandidates.html',
				'administrator' => true,
				'admingroup' => 'candidates',
				//'election' => true,
			),
			#!# Not present
			'editcandidates'	=> array (
				'description' => 'Show/edit candidates',
				'url' => 'admin/editcandidates.html',
				'administrator' => true,
				'admingroup' => 'candidates',
				'election' => true,
			),
			'mailout'		=> array (
				'description' => 'Send e-mail mailout to candidates containing the survey',
				'url' => 'admin/mailout.html',
				'administrator' => true,
				'admingroup' => 'candidates',
				'election' => true,
			),
			'letters'		=> array (
				'description' => 'Print mailout (letters) to candidates containing the survey',
				'url' => 'admin/letters.html',
				'administrator' => true,
				'admingroup' => 'candidates',
				'election' => true,
			),
			'reminders'		=> array (
				'description' => 'Send reminder e-mails to candidates who have not yet responded to the survey',
				'url' => 'admin/reminders.html',
				'administrator' => true,
				'admingroup' => 'candidates',
				'election' => true,
			),
			'reissue'		=> array (
				'description' => 'Reissue an e-mail to a candidate',
				'url' => 'admin/reissue.html',
				'administrator' => true,
				'admingroup' => 'candidates',
				'election' => true,
			),
			'submit'		=> array (
				'description' => 'Candidate survey response form',
				'url' => 'submit/',
				'admingroup' => 'candidates',
			),
			'elected'		=> array (
				'description' => 'Specify the elected candidates',
				'url' => 'admin/elected.html',
				'administrator' => true,
				'admingroup' => 'postelection',
				'election' => true,
			),
		);
	}
	
	
	# Constructor
	public function __construct ($settings = array ())
	{
		# Load external libraries
		ini_set ('include_path', ini_get ('include_path') . PATH_SEPARATOR . './libraries/');
		require_once ('application.php');
		require_once ('database.php');
		require_once ('pureContent.php');
		
		# Start the HTML
		$html = '';
		
		# Get the base URL
		$this->baseUrl = application::getBaseUrl ();
		
		# Load the local stylesheet
		$html .= "\n<style type=\"text/css\" media=\"all\">@import \"{$this->baseUrl}/elections.css\";</style>";
		
		# Function to merge the arguments; note that $errors returns the errors by reference and not as a result from the method
		$this->errors = array ();
		if (!$this->settings = $this->mergeConfiguration ($this->defaults, $settings)) {
			$html .= "<p>The following setup error was found. The administrator needs to correct the setup before this system will run.</p>\n" . application::htmlUl ($this->errors);
			$html = $this->settings['headerHtml'] . $html . $this->settings['footerHtml'];
			echo $html;
			return false;
		}
		
		# Connect to the database or end
		$this->databaseConnection = new database ($this->settings['hostname'], $this->settings['username'], $this->settings['password'], $this->settings['database']);
		if (!$this->databaseConnection->connection) {
 			mail ($this->settings['webmaster'], 'Problem with election system on ' . $_SERVER['SERVER_NAME'], wordwrap ('There was a problem with initalising the election facility at the database connection stage. The database server said: ' . mysql_error () . '.'));
			$html .= "<p class=\"warning\">Apologies - this facility is currently unavailable, as a technical error occured. The Webmaster has been informed and will investigate.</p>";
			$html = $this->settings['headerHtml'] . $html . $this->settings['footerHtml'];
			echo $html;
			return false;
		};
		
		# Obtain the actions
		$this->actions = $this->actions ();
		
		# Determine whether the user is an administrator
		require_once ('signin.php');
		$this->userIsAdministrator = signin::user_has_privilege ('elections');
		
		# Set the action, checking that a valid page has been supplied
		if (!isSet ($_GET['action']) || !array_key_exists ($_GET['action'], $this->actions)) {
			$html = $this->settings['headerHtml'] . $html . $this->settings['footerHtml'];
			echo $html;
			$html .= $this->pageNotFound ();
			return false;
		}
		$this->action = $_GET['action'];

		# On pages requiring administrative credentials, ensure the user is an administrator
		if (isSet ($this->actions[$this->action]['administrator']) && $this->actions[$this->action]['administrator']) {
			if (!$this->userIsAdministrator && !$this->settings['overrideAdmin']) {
				$html = "\n" . '<p>You must be <a href="/signin/">signed in</a> as an administrator to access this page.</p>';
				$html = $this->settings['headerHtml'] . $html . $this->settings['footerHtml'];
				echo $html;
				return false;
			}
		}
		
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
		
		# Open the div surrounding the application
		$html .= "\n<div id=\"elections\">";
		
		# Show the heading
		$html .= "\n<h1>" . htmlspecialchars ($this->settings['applicationName']) . '</h1>';
		if ($this->election) {
			$html .= $this->droplistNavigation ();
		}
		
		# Add link to admin menu
		if (isSet ($this->actions[$this->action]['administrator']) && $this->actions[$this->action]['administrator']) {
			if ($this->action != 'admin') {		// Don't add link to self
				$html .= "\n<p class=\"alignright\"><a href=\"{$this->baseUrl}/admin/\">&laquo; Return to admin menu</a></p>";
			}
		}
		
		# Show the title if present
		if ($this->actions[$this->action]['description']) {
			$html .= "\n<h2>" . htmlspecialchars ($this->actions[$this->action]['description']) . '</h2>';
		}
		
		# Run the page action
		$html .= $this->{$this->action} ();
		
		# End with disclaimer
		if ($this->action != 'letters') {
			$html .= "\n<p class=\"small comment\" style=\"margin-top: 50px;\"><em>{$this->settings['imprint']}</em></p>";
		}
		
		# Close the div surrounding the application
		$html .= "\n</div>";
		
		# Surround with header and footer, if supplied
		$html = $this->settings['headerHtml'] . $html . $this->settings['footerHtml'];
		
		# Show the HTML
		echo $html;
	}
	
	
	# Function to define the database structure
	#!# Not currently auto-executed on first run
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
	private function databaseStructure ()
	{
		# The database structure should be as follows, with modifications to be made in the elections_wards table for areas
		# The user only needs SELECT,INSERT,UPDATE rights at a minimum
		return $sql = "
			
			CREATE DATABASE {$this->settings['database']};
			USE {$this->settings['database']};
			
			CREATE TABLE IF NOT EXISTS `{$this->settings['tablePrefix']}_candidates` (
			  `id` int(11) NOT NULL auto_increment COMMENT 'Unique key',
			  `election` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'Election / year (join to elections)',
			  `ward` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'Ward (join to wards)',
			  `forename` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'Forename',
			  `surname` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'Surname',
			  `address` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'Address',
			  `email` varchar(255) collate utf8_unicode_ci NULL COMMENT 'E-mail address',
			  `verification` varchar(6) collate utf8_unicode_ci NOT NULL COMMENT 'Verification number',
			  `affiliation` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'Affiliation (join to affiliations)',
			  `cabinetRestanding` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Whether the candidate is a restanding Cabinet member, and if so, their current Cabinet post',
			  `private` int(1) default NULL,
			  PRIMARY KEY  (`id`),
			  UNIQUE KEY `verification` (`verification`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Candidates';
			
			CREATE TABLE IF NOT EXISTS `{$this->settings['tablePrefix']}_elections` (
			  `id` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'Unique ID (used for the URL)',
			  `name` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'Name of election',
			  `description` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'Description of election',
			  `startDate` date NOT NULL default '0000-00-00' COMMENT 'Survey opening date (e.g. as soon as all info loaded)',
			  `resultsDate` date NOT NULL default '0000-00-00' COMMENT 'Date when responses become visible (e.g. 2 weeks before election day)',
			  `endDate` date NOT NULL default '0000-00-00' COMMENT 'Date of election (and close of survey submissions)',
			  `letterheadHtml` TEXT NOT NULL COMMENT  'Letterhead address (top-right)',
			  `organisationIntroductionHtml` TEXT NOT NULL COMMENT 'Survey letter/e-mail introduction',
			  `letterSignatureName` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'Signature name',
			  `directionsUrl` varchar(255) collate utf8_unicode_ci NOT NULL default 'https://www.cyclestreets.net/' COMMENT 'Directions to cycle to polling stations',
			  PRIMARY KEY  (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Election overview';
			
			CREATE TABLE IF NOT EXISTS `{$this->settings['tablePrefix']}_questions` (
			  `id` int(11) NOT NULL auto_increment COMMENT 'Unique key',
			  `question` text collate utf8_unicode_ci NOT NULL COMMENT 'Text of question',
			  `links` text collate utf8_unicode_ci COMMENT 'Background links (as URL then text)',
			  PRIMARY KEY  (`id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Available questions';
			
			CREATE TABLE IF NOT EXISTS `{$this->settings['tablePrefix']}_responses` (
			  `id` int(11) NOT NULL auto_increment COMMENT 'Unique key',
			  `candidate` int(11) NOT NULL default '0' COMMENT 'Candidates (join to candidates)',
			  `survey` int(11) NOT NULL default '0' COMMENT 'Survey (join to surveys)',
			  `response` text collate utf8_unicode_ci COMMENT 'Response',
			  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP COMMENT 'Timestamp',
			  PRIMARY KEY  (`id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Responses';
			
			CREATE TABLE IF NOT EXISTS `{$this->settings['tablePrefix']}_surveys` (
			  `id` int(11) NOT NULL auto_increment COMMENT 'Unique key',
			  `election` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'Election / year (join to elections)',
			  `ward` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'Ward (join to wards)',
			  `question` int(11) NOT NULL default '0' COMMENT 'Question (join to questions)',
			  `ordering` int(1) default NULL COMMENT 'Ordering',
			  PRIMARY KEY  (`id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Surveys';
			
			CREATE TABLE IF NOT EXISTS `{$this->settings['tablePrefix']}_wards` (
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
		";
	}
	
	
	# Page not found wrapper
	private function pageNotFound ()
	{
		# Create a 404 page
		#!# Needs to return HTML instead of echo
		header ('HTTP/1.0 404 Not Found');
		include ($this->settings['page404']);
	}
	
	
	# Homes page
	public function home ()
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
		
		# Return the HTML
		return $html;
	}
	
	
	# Main page for an election
	public function overview ()
	{
		# Validate the election
		if (!$this->election) {
			header ('HTTP/1.0 404 Not Found');
			$html = '<p>There is no such election. Please check the URL and try again.</p>';
			return $html;
		}
		
		# Add introduction
		$html  = $this->settings['introductoryTextHtml'];
		
		# Add the summary table and wards
		$html .= "<p class=\"graphic\"><img src=\"/elections/pollingstations.jpg\" width=\"89\" height=\"121\" alt=\"Ballot box\" /></p>";
		$html .= $this->showOverviewDetails ($this->election);
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to show the summary table and wards
	private function showOverviewDetails ($election)
	{
		# Table of data about the election
		$html  = $this->summaryTable ($election);
		
		# List wards
		$html .= $this->showWards ($election);
		
		# Show administrative options for this election
		if ($this->userIsAdministrator) {
			$html .= "\n<br />";
			$html .= "\n<h2>Administrative options for this election</h2>";
			$html .= "\n<p><a href=\"{$this->baseUrl}/{$this->election['id']}/admin/\">Administrative area for this election</a></p>";
		}
		
		# Return the HTML
		return $html;
	}
	
	
	# Main page
	public function ward ()
	{
		# Start the HTML
		$html = '';
		
		# Validate the ward
		if (!$this->election) {
			header ('HTTP/1.0 404 Not Found');
			$html = '<p>There is no such election. Please check the URL and try again.</p>';
			return $html;
		}
		
		# Validate the ward
		if (!$this->ward) {
			header ('HTTP/1.0 404 Not Found');
			$html = '<p>There is no such ' . $this->settings['division'] . ' being contested in this election. Please check the URL and try again.</p>';
			return $html;
		}
		
		# Remind administrators
		if ($this->userIsAdministrator && !$this->election['resultsVisible']) {
			$html .= "\n<p class=\"warning\"><strong>Note: any responses shown because are only visible to you because you are an administrator.</strong> The responses will not be made public until at least {$this->election['visibilityDateTime']}.</p>";
		}
		
		# Start with a table of data
		$html .= $this->summaryTable ($this->election);
		
		# List the questions asked
		$html .= $this->showQuestions ($this->ward['id']);
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to construct a list of all questions, or show all responses to a single question, for a particular election
	public function questions ()
	{
		# Start the HTML
		$html = '';
		
		# Ensure there is an election which is validated
		if (!$this->election) {
			header ('HTTP/1.0 404 Not Found');
			$html = '<p>There is no such election. Please check the URL and try again.</p>';
			return $html;
		}
		
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
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to show an individual question's responses within a particular election
	private function showQuestionForElection ($questions, $questionNumber)
	{
		# Start the HTML
		$html = '';
		
		# Validate the question number
		if (!isSet ($questions[$questionNumber])) {
			header ('HTTP/1.0 404 Not Found');
			return $html = '<p>The specific question number is invalid. Please check the URL and try again.</p>';
		}
		
		# Get the question
		$question = $questions[$questionNumber];
		
		# Determine the total number of questions in this survey
		$total = count ($questions);
		
		#!# This section is over-complex and involves multiple SQL lookups, for the sake of avoiding code duplication in responsesBlock (which has a certain datastructure) - ideally there would be a single OUTER JOIN that would list all candidates and show the responses where the candidate has answered, but this means duplicating lookups like candidate['_name']
		
		# Get the wards (and their associated survey IDs) where this question was asked
		$wardsQuery = "SELECT id,ward FROM {$this->settings['tablePrefix']}surveys WHERE question = {$question['questionId']} AND election = '{$this->election['id']}';";
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
			$questionHtml = htmlspecialchars ($question['question']);
			$questionHtml = $this->applyHighlighting ($questionHtml, $question['highlight']);
			$list[] = $questionHtml . "<br /><a href=\"{$this->baseUrl}/{$this->election['id']}/questions/{$questionNumber}/\">Read all answers&hellip;</a>";
		}
		
		# Compile the HTML
		$html  = "\n<h2>List of questions</h2>";
		$html .= "\n<p>Here is a list of all the questions (across all wards) we have asked for this election:</p>";
		$html .= application::htmlOl ($list, 0, 'spaced');
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to apply highlighting to a question
	private function applyHighlighting ($questionHtml, $highlight)
	{
		# Return unmodified if not required
		if (!$highlight) {return $questionHtml;}
		
		# Perform replacement and return the result
		$questionHtml = str_replace (htmlspecialchars ($highlight), '<strong>' . htmlspecialchars ($highlight) . '</strong>', $questionHtml);
		return $questionHtml;
	}
	
	
	# Function to get all the questions being asked in a particular election
	private function getQuestionsForElection ($electionId  /* will be false if all questions */, $idToIndex = false)
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
	private function getElections ($includeForthcoming = false)
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
				CONCAT( LOWER( DATE_FORMAT(CONCAT(resultsDate,' ','{$this->settings['resultsVisibleTime']}'),'%l%p, ') ), DATE_FORMAT(CONCAT(resultsDate,' ','{$this->settings['resultsVisibleTime']}'),'%W %D %M %Y') ) AS visibilityDateTime,
				IF(name LIKE '%county%',1,0) AS isCounty
			FROM {$this->settings['tablePrefix']}elections
			" . ($includeForthcoming ? '' : "WHERE startDate <= (CAST(NOW() AS DATE))") . "
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
				COUNT({$this->settings['tablePrefix']}wards.id) as 'candidates'
			FROM {$this->settings['tablePrefix']}candidates
			LEFT OUTER JOIN {$this->settings['tablePrefix']}wards ON {$this->settings['tablePrefix']}candidates.ward = {$this->settings['tablePrefix']}wards.id
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
		$html = '';
		
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
		$submitTo = "{$this->baseUrl}/{$this->election['id']}/";
		$html = application::htmlJumplist ($list, $selected, $submitTo, 'jumplist', $parentTabLevel = 3, ($wardsOnly ? '' : 'jumplist'), ($wardsOnly ? '' : 'Jump to:'));
		
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
				CONCAT(forename,' ',UPPER(surname)) as name,
				{$this->settings['tablePrefix']}candidates.email
			FROM {$this->settings['tablePrefix']}candidates
			LEFT OUTER JOIN {$this->settings['tablePrefix']}affiliations ON {$this->settings['tablePrefix']}candidates.affiliation = {$this->settings['tablePrefix']}affiliations.id
			LEFT OUTER JOIN {$this->settings['tablePrefix']}wards ON {$this->settings['tablePrefix']}candidates.ward = {$this->settings['tablePrefix']}wards.id
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
	private function showQuestions ($limitToWard = false /* will be false if listing all questions across all elections */)
	{
		# Start the HTML
		$html = '';
		
		# Get the data
		$electionId = ($limitToWard ? $this->election['id'] : false);
		if (!$data = $this->getQuestions ($limitToWard, $electionId)) {
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
		$questionNumbersPublic = $this->getQuestionsForElection ($electionId, true);
		
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
		$html = '';
		
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
		# If there is not an election specified, i.e. top-level listing of all questions, retrieve all available questions
		if (!$election) {
			$query = "SELECT *, id AS questionId FROM {$this->settings['tablePrefix']}questions;";
			
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
				FROM {$this->settings['tablePrefix']}surveys
				LEFT OUTER JOIN {$this->settings['tablePrefix']}wards ON {$this->settings['tablePrefix']}surveys.ward = {$this->settings['tablePrefix']}wards.id
				LEFT OUTER JOIN {$this->settings['tablePrefix']}questions ON {$this->settings['tablePrefix']}surveys.question = {$this->settings['tablePrefix']}questions.id
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
				if (preg_match ('@^https?://' . str_replace ('mirror.', 'www.', $_SERVER['SERVER_NAME']) . '(.*)@', $link, $matches)) {
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
				$list[] = ($title ? "{$title}:<br />" : '') . str_replace (array ('http://www.', 'https://www.'), 'www.', $link);
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
			FROM {$this->settings['tablePrefix']}responses
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
	public function submit ($showIds = false)
	{
		# Start the HTML
		$html = '';
		
		# Get the list of all wards currently being surveyed
		if (!$wards = $this->getActiveWards ()) {
			$html .= "\n<p>No {$this->settings['divisionPlural']} are currently being surveyed.</p>";
			return $html;
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
				'autofocus' => true,
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
			if (!$result = $form->process ($html)) {
				return $html;
			}
		}
		
		# Determine the number and ward to be checked
		$number = ($secondStagePosted ? $_POST['questions']['verification']['number'] : $result['number']);
		$ward = ($secondStagePosted ? $_POST['questions']['verification']['ward'] : $result['ward']);
		
		# Confirm the details
		#!# Use getUnfinalised to improve the UI here
		if (!$candidate = $this->verifyCandidate ($number, $ward)) {
			$html .= "\n<p>The verification/{$this->settings['division']} pair you submitted does not seem to be correct. Please check the letter/e-mail we sent you and <a href=\"{$this->baseUrl}/submit/\">try again</a>.</p>";
			return $html;
		}
		
		# Retrieve and cache the election data
		$this->election = $this->elections[$candidate['electionId']];
		
		# Create a shortcut to the ward name
		$wardName = $wards[$candidate['wardId']];
		
		# Start the page with a new heading
		$html  = "\n\n<h2 class=\"ward\" id=\"{$wardName}\">Questions for {$wardName} {$this->settings['division']} candidates</h2>";
		
		# End if election is over
		if (!$this->election['active']) {
			return $html .= "<p>The election is now over, so submissions cannot be made any longer.</p>";
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
			return $html .= "\n<p>There are no questions assigned for this {$this->settings['division']} at present.</p>";
		}
		
		# Get the responses for this candidate's questions
		if ($responses = $this->getResponses (array_keys ($questions), $candidate['id'])) {
			$responses = $responses[$candidate['id']];
		}
		
		# Prevent updates after the results are visible
		if ($responses && $this->election['resultsVisible']) {
			return $html .= "<p>You have previously submitted a set of responses, which is now <a href=\"{$this->baseUrl}/{$this->election['id']}/{$candidate['wardId']}/\">shown online</a>, so submissions cannot be made any longer. Thank you for taking part.</p>";
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
			return $html;
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
					return "<p>There was a problem saving your updated responses. Please kindly contact the webmaster.</p>";
				}
				
			# Otherwise do a normal insert
			} else {
				if (!$this->databaseConnection->insert ($this->settings['database'], "{$this->settings['tablePrefix']}responses", $insert)) {
					return "<p>There was a problem saving the responses. Please kindly contact the webmaster.</p>";
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
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to get active wards across all current elections
	private function getActiveWards ()
	{
		# Get data
		$query = "SELECT
				{$this->settings['tablePrefix']}candidates.id,
				{$this->settings['tablePrefix']}candidates.ward as wardId,
				prefix, {$this->settings['tablePrefix']}wards.ward
			FROM {$this->settings['tablePrefix']}candidates
			LEFT OUTER JOIN {$this->settings['tablePrefix']}elections ON {$this->settings['tablePrefix']}candidates.election = {$this->settings['tablePrefix']}elections.id
			LEFT OUTER JOIN {$this->settings['tablePrefix']}wards ON {$this->settings['tablePrefix']}candidates.ward = {$this->settings['tablePrefix']}wards.id
			WHERE
				{$this->settings['tablePrefix']}elections.endDate >= (CAST(NOW() AS DATE))
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
			FROM {$this->settings['tablePrefix']}candidates
			LEFT OUTER JOIN {$this->settings['tablePrefix']}affiliations ON {$this->settings['tablePrefix']}candidates.affiliation = {$this->settings['tablePrefix']}affiliations.id
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
	public function respondents ()
	{
		# Validate the election
		if (!$this->election) {
			header ('HTTP/1.0 404 Not Found');
			$html = '<p>There is no such election. Please check the URL and try again.</p>';
			return $html;
		}
		
		# Title
		$html  = "<h2>List of respondents" . ($this->election['active'] ? ' (so far)' : '') .  "</h2>";
		
		# Get the data
		$query = "SELECT
				{$this->settings['tablePrefix']}candidates.id,
				CONCAT({$this->settings['tablePrefix']}candidates.forename,' ',UPPER({$this->settings['tablePrefix']}candidates.surname)) as name,
				{$this->settings['tablePrefix']}wards.id as wardId,
				{$this->settings['tablePrefix']}wards.prefix,
				{$this->settings['tablePrefix']}wards.ward,
				{$this->settings['tablePrefix']}wards.districtCouncil,
				{$this->settings['tablePrefix']}affiliations.name as affiliation,
				{$this->settings['tablePrefix']}affiliations.colour
			FROM {$this->settings['tablePrefix']}responses
			LEFT OUTER JOIN {$this->settings['tablePrefix']}candidates ON {$this->settings['tablePrefix']}responses.candidate = {$this->settings['tablePrefix']}candidates.id
			LEFT OUTER JOIN {$this->settings['tablePrefix']}wards ON {$this->settings['tablePrefix']}candidates.ward = {$this->settings['tablePrefix']}wards.id
			LEFT OUTER JOIN {$this->settings['tablePrefix']}affiliations ON {$this->settings['tablePrefix']}candidates.affiliation = {$this->settings['tablePrefix']}affiliations.id
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
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to show the status of Cabinet members restanding in this election
	public function cabinet ()
	{
		# Start the HTML
		$html = '';
		
		# Validate the election
		if (!$this->election) {
			header ('HTTP/1.0 404 Not Found');
			$html .= '<p>There is no such election. Please check the URL and try again.</p>';
			return $html;
		}
		
		# End if no Cabinet members restanding in this election
		if (!$this->cabinetRestanding) {
			header ('HTTP/1.0 404 Not Found');
			$html .= '<p>There are no Cabinet members in wards we are surveying restanding in this election. Please check the URL and try again.</p>';
			return $html;
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
		$html .= "\n<p>The <strong>Cabinet</strong> is the Executive of the Council, formed of members of the political party in power. They implement and drive the Council's policy. As such, their views arguably have greater effect than any other Councillors.</p>";
		$html .= "\n<p>The listing below shows all the Cabinet members in wards we are surveying who are restanding in this election, and whether they have responded to our survey or not.</p>";
		$html .= application::htmlTable ($cabinetMembers, array (), 'lines regulated', $keyAsFirstColumn = false, false, $allowHtml = true, $showColons = true);
		
		# Return the HTML
		return $html;
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
	public function admin ()
	{
		# Start the HTML with a customised heading
		$html = "\n<h2>Administrative functions" . ($this->election ? ' for this election' : '') . '</h2>';
		
		# Add introduction
		$html .= "\n<p><em>This section is accessible only to Administrators.</em></p>";
		
		# Define the groups
		$groups = array (
			'election' => array (
				'title' => 'Election details',
				'icon' => 'application_view_list',
				'introduction' => 'To run a survey for an election, you need to create settings for that election, defining its name, date, and other details.',
			),
			'wardaffilations' => array (
				'title' => 'Geographical areas and party details',
				'icon' => 'map',
				'introduction' => 'In order to create a survey, you need to ensure that each geographical area and party name exists in the database. Once added, these are available for any survey you create for any election.',
			),
			'questions' => array (
				'title' => 'Questions',
				'icon' => 'help',
				'introduction' => 'In order to create surveys for each geographical area, you must add each available question to the database. Once added, a question can be used for any surveys, e.g. across multiple geographical areas or in future elections.',
			),
			'surveys' => array (
				'title' => 'Surveys',
				'icon' => 'script',
				'introduction' => 'Having defined an election, ensured that the geographical areas and party names are in the database, and that the questions are available, you can now create the survey for each area.',
			),
			'candidates' => array (
				'title' => 'Candidates',
				'icon' => 'group',
				'introduction' => 'At the start of the election, you can either e-mail or print out letters for each candidate, inviting them to contribute their answers. You can also send reminder e-mails, or reissue an e-mail. You can also check what a candidate sees when they visit the submission form. Candidates can submit responses at any time from the opening of the survey until the date of the election; they can also edit an existing response until the responses are made live.',
			),
			'postelection' => array (
				'title' => 'After the election',
				'icon' => 'medal_gold_2',
				'introduction' => 'After the election, you can specify the winning candidates, so that their answers are shown highlighted.',
			),
		);
		
		# Construct the page
		foreach ($groups as $groupId => $group) {
			$html .= "\n<div class=\"graybox\">";
			$html .= "\n<h3><img src=\"/images/icons/{$group['icon']}.png\" class=\"icon\" /> " . htmlspecialchars ($group['title']) . '</h3>';
			$html .= "\n\t<p>" . htmlspecialchars ($group['introduction']) . '</p>';
			$html .= "\n\t<ul>";
			foreach ($this->actions as $actionId => $action) {
				if (isSet ($action['admingroup']) && $action['admingroup'] == $groupId) {
					if ($this->election && isSet ($action['election']) && !$action['election']) {continue;}	// Skip if explicitly false
					if (method_exists ($this, $actionId)) {
						$url = "{$this->baseUrl}/" . ((isSet ($action['election']) && $this->election) ? str_replace ('admin/', "{$this->election['id']}/", $action['url']) : $action['url']);
						$html .= "\n\t\t<li><a href=\"{$url}\">" . htmlspecialchars ($action['description']) . '</a></li>';
					} else {
						$html .= "\n\t\t<li><span class=\"comment\">" . htmlspecialchars ($action['description']) . '</span> (not yet available)</li>';
					}
				}
			}
			$html .= "\n\t<ul>";
			$html .= "\n</div>";
		};
		
		# Return the HTML
		return $html;
	}
	
	
	# List of all questions in the entire database
	public function allquestions ()
	{
		# Start the HTML
		$html = '';
		
		# Ensure that an election is not being supplied
		if ($this->election) {
			header ('HTTP/1.0 404 Not Found');
			return $html = '<p>This listing is not election-specific. Please check the URL and try again.</p>';
		}
		
		# Get data
		$html .= $this->showQuestions ();
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to add an election
	public function addelection ()
	{
		# Start the HTML
		$html = '';
		
		# Get current IDs
		$currentIds = $this->databaseConnection->selectPairs ($this->settings['database'], "{$this->settings['tablePrefix']}elections", array (), array ('id'), true, $orderBy = 'id');
		
		# Process the form
		if ($result = $this->electionForm (array (), $currentIds, $html /* returned by reference */)) {
			
			# Insert the election
			$this->databaseConnection->insert ($this->settings['database'], "{$this->settings['tablePrefix']}elections", $result);
			
			# Confirm success
			$html .= "\n<p><img src=\"/images/icons/tick.png\" class=\"icon\" /> The <a href=\"{$this->baseUrl}/{$result['id']}/\">election</a> has been added.</p>";
			$html .= "\n<p>You may wish to <a href=\"{$this->baseUrl}/admin/\">add data</a> for it.</p>";
		}
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to edit settings for an election
	public function editelection ()
	{
		# Start the HTML
		$html = '';
		
		# Get all elections, including forthcoming
		$this->elections = $this->getElections (true);
		$this->election = ((isSet ($_GET['election']) && isSet ($this->elections[$_GET['election']])) ? $this->elections[$_GET['election']] : false);
		
		# Ensure there is an election supplied
		if (!$this->election) {
			$html .= "\n<p>Please select which election:</p>";
			$html .= $this->listElections ($this->elections, true, false, 'editelection.html');
			return $html;
		}
		
		# Process the form
		if ($result = $this->electionForm ($this->election, array (), $html /* returned by reference */)) {
			
			# Insert the election
			$this->databaseConnection->update ($this->settings['database'], "{$this->settings['tablePrefix']}elections", $result, array ('id' => $this->election['id']));
			
			# Confirm success
			$html .= "\n<p><img src=\"/images/icons/tick.png\" class=\"icon\" /> The <a href=\"{$this->baseUrl}/{$result['id']}/editelection.html\">settings</a> for this <a href=\"{$this->baseUrl}/{$result['id']}/\">election</a> have been updated.</p>";
		}
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to create a form to add/edit settings for an election
	private function electionForm ($data, $currentIds, &$html)
	{
		# Create a new form
		require_once ('ultimateForm.php');
		$form = new form (array (
			'databaseConnection' => $this->databaseConnection,
			'displayRestrictions' => false,
			'formCompleteText' => false,
			'picker' => true,
			'div' => 'ultimateform electionform',
			'display' => 'paragraphs',
			'richtextEditorBasePath' => $this->settings['richtextEditorBasePath'],
		));
		$form->dataBinding (array (
			'database'	=> $this->settings['database'],
			'table'		=> "{$this->settings['tablePrefix']}elections",
			'data'		=> $data,
			'intelligence' => true,
			'attributes' => array (
				'id' => array ('editable' => (!$data), 'current' => $currentIds, 'regexp' => '^[a-z0-9]+$', 'placeholder' => 'E.g. ' . date ('Y') . 'election', ),
				'name' => array ('placeholder' => 'E.g. Elections to Placeford Council, ' . date ('Y')),
				'startDate' => array ('description' => 'This is the date when candidates can start to enter their responses, assuming that questions, wards, etc., are all loaded.'),
				'resultsDate' => array ('description' => 'This is the date when responses from candidates will become visible to the general public. Admis can log in and see responses before this date. Candidates can edit any existing response they have made until this date.'),
				'endDate' => array ('description' => 'This is the date of the election, and candidates will not be able to edit their responses after this date.'),
				'description' => array ('placeholder' => 'E.g. Elections to Placeford Council in May ' . date ('Y')),
				'letterheadHtml' => array ('editorToolbarSet' => 'BasicImage', 'width' => '600px'),
				'organisationIntroductionHtml' => array ('editorToolbarSet' => 'BasicNoLinks', 'width' => '600px'),
			),
		));
		#!# Need to add constraints to ensure date ordering is correct
		return $result = $form->process ($html);
	}
	
	
	# Function to add a ward
	public function addward ()
	{
		# Start the HTML
		$html = '';
		
		# Add introduction
		$html .= "\n<p>Here you can add a ward/division to the database. You only need to do this if the ward/division has not already been added in a previous election.</p>";
		
		# Get current IDs
		$currentIds = $this->databaseConnection->selectPairs ($this->settings['database'], "{$this->settings['tablePrefix']}wards", array (), array ('id'), true, $orderBy = 'id');
		
		# Create a new form
		require_once ('ultimateForm.php');
		$form = new form (array (
			'databaseConnection' => $this->databaseConnection,
			'displayRestrictions' => false,
			'picker' => true,
		));
		$form->dataBinding (array (
			'database'	=> $this->settings['database'],
			'table'		=> "{$this->settings['tablePrefix']}wards",
			'attributes' => array (
				'id' => array ('current' => $currentIds, ),
			),
		));
		if (!$result = $form->process ($html)) {
			return $html;
		}
		
		# Insert the ward
		if (!$this->databaseConnection->insert ($this->settings['database'], "{$this->settings['tablePrefix']}wards", $result)) {
			$html = "\n<p><img src=\"/images/icons/cross.png\" class=\"icon\" /> An error occurred adding the ward.</p>";
			return $html;
		}
		
		# Confirm success
		$html  = "\n<p><img src=\"/images/icons/tick.png\" class=\"icon\" /> The ward has been added.</p>";
		$html .= "\n<p>Add another?</p>";
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to add an affiliation
	public function addaffiliations ()
	{
		# Start the HTML
		$html = '';
		
		# Add introduction
		$html .= "\n<p>Here you can add a political party/group to the database. You only need to do this if the political party/group has not already been added in a previous election.</p>";
		
		# Get current IDs
		$table = "{$this->settings['tablePrefix']}affiliations";
		$currentIds = $this->databaseConnection->selectPairs ($this->settings['database'], $table, array (), array ('id'), true, $orderBy = 'id');
		
		# Create a new form
		require_once ('ultimateForm.php');
		$form = new form (array (
			'databaseConnection' => $this->databaseConnection,
			'displayRestrictions' => false,
			'picker' => true,
		));
		$form->dataBinding (array (
			'database'	=> $this->settings['database'],
			'table'		=> $table,
			'attributes' => array (
				'id' => array ('current' => $currentIds, ),
				'colour' => array ('type' => 'color', ),	#!# This should be done natively in ultimateForm
			),
		));
		if (!$result = $form->process ($html)) {
			return $html;
		}
		
		# Replace hash in colour code
		#!# This should be done natively in ultimateForm
		$result['colour'] = str_replace ('#', '', $result['colour']);
		
		# Insert the new entry
		if (!$this->databaseConnection->insert ($this->settings['database'], $table, $result)) {
			$html = "\n<p><img src=\"/images/icons/cross.png\" class=\"icon\" /> An error occurred adding the affiliation.</p>";
			return $html;
		}
		
		# Confirm success
		$html  = "\n<p><img src=\"/images/icons/tick.png\" class=\"icon\" /> The affiliation has been added.</p>";
		$html .= "\n<p><a href=\"{$this->baseUrl}/admin/addaffiliations.html\">Add another?</a></p>";
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to add candidates for an election
	public function addcandidates ()
	{
		# Start the HTML
		$html = '';
		
		# Add introduction
		$html .= "\n<p>Note that this will replace the data for the selected election.</p>";
		
		# Get all elections, including forthcoming
		$elections = $this->getElections (true);
		
		# Define the required fields
		$requiredFields = array ('forename', 'surname', 'ward', 'affiliation', 'address', 'email');
		
		# Create a new form
		require_once ('ultimateForm.php');
		$form = new form (array (
			'databaseConnection' => $this->databaseConnection,
			'picker' => true,
		));
		$form->select (array (
			'name'			=> 'election',
			'title'			=> 'Which election',
			'values'		=> $this->getElectionNames ($elections),
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
				
				# Verify ward names
				$wards = $this->getWardNames ();
				$unknownWards = array ();
				foreach ($data as $candidate) {
					if (!array_key_exists ($candidate['ward'], $wards)) {
						$unknownWards[] = $candidate['ward'];
					}
				}
				if ($unknownWards) {
					$form->registerProblem ('unknownwards', 'Not all wards were recognised: ' . htmlspecialchars (implode (', ' , array_unique ($unknownWards))) . '; please register missing wards if correct.');
				}
				
				# Verify affiliations
				$affiliations = $this->getAffiliationNames ();
				$unknownAffiliations = array ();
				foreach ($data as $candidate) {
					if (!array_key_exists ($candidate['affiliation'], $affiliations)) {
						$unknownAffiliations[] = $candidate['affiliation'];
					}
				}
				if ($unknownAffiliations) {
					$form->registerProblem ('unknownaffiliations', 'Not all affiliations were recognised: ' . htmlspecialchars (implode (', ' , array_unique ($unknownAffiliations))) . '; please register missing affiliations if correct.');
				}
			}
		}
		if (!$result = $form->process ($html)) {
			return $html;
		}
		
		# Process the data to add fixed fields
		foreach ($data as $index => $candidate) {
			
			# Add election ID
			$data[$index]['election'] = $result['election'];
			
			# Add random verification number for candidate login; note that uniqueness across the dataset is not actually required
			$data[$index]['verification'] = rand (100000, 999999);
		}
		
		# Clear any existing data
		$this->databaseConnection->delete ($this->settings['database'], "{$this->settings['tablePrefix']}candidates", array ('election' => $result['election']));
		
		# Insert the data
		if (!$this->databaseConnection->insertMany ($this->settings['database'], "{$this->settings['tablePrefix']}candidates", $data)) {
			$error = $this->databaseConnection->error ();
			$html  = "\n<p><img src=\"/images/icons/cross.png\" class=\"icon\" /> Sorry, an error occured. The database server said:</p>";
			$html .= "\n<p><tt>" . $error[2] . '</tt></p>';
			return $html;
		}
		
		# Confirm success
		$total = count ($data);
		#!# Ideally the message should make clear if this was entirely new or a replacement
		$html  = "\n<p><img src=\"/images/icons/tick.png\" class=\"icon\" /> The candidate data (total: {$total}) for this <a href=\"{$this->baseUrl}/{$result['election']}/\">election</a> has been entered.</p>";
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to process submitted TSV batch string and assemble the data from it
	private function getTsvData ($tsv, $requiredFields, &$errorMessage = '')
	{
		# Parse the TSV string
		require_once ('csv.php');
		if (!$data = csv::tsvToArray (trim ($tsv), $firstColumnIsId = false, $firstColumnIsIdIncludeInData = true, $errorMessage)) {
			return array ();
		}
		
		# Ensure headers are valid and that required headers are present
		foreach ($data as $filename => $metadata) {
			$missingRequiredFields = array_diff ($requiredFields, array_keys ($metadata));
			break;	// Only check the first row, i.e. the heading row
		}
		if ($missingRequiredFields) {
			$errorMessage = "The fields in the pasted data do not match the specification noted above. Please correct the spreadsheet and try again.";
			return array ();
		}
		
		# Trim all values, e.g. to ensure e-mails do not have spaces in
		foreach ($data as $rowNumber => $line) {
			foreach ($line as $key => $value) {
				$data[$rowNumber][$key] = trim ($value);
			}
		}
		
		# Return the data
		return $data;
	}
	
	
	# Helper function to get election names
	private function getElectionNames ($elections)
	{
		# Assemble the elections list
		$electionNames = array ();
		foreach ($elections as $key => $value) {
			$electionNames[$key] = $value['name'];
		}
		
		# Return the list
		return $electionNames;
	}
	
	
	# Function to add questions
	public function addquestions ()
	{
		# Start the HTML
		$html = '';
		
		# Define number of recent questions to show
		$mostRecent = 20;
		
		# Introductory text
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
			'table'		=> "{$this->settings['tablePrefix']}questions",
			'intelligence' => true,
			'size'	=> 80,	#!# This is here due to a bug in ultimateForm
			'attributes' => array (
				
			),
		));
		#!# Need to check that highlight text appears in the question
		if (!$result = $form->process ($html)) {
			$html .= $this->recentlyAddedQuestions ($mostRecent);
			return $html;
		}
		
		# Insert the question
		if (!$this->databaseConnection->insert ($this->settings['database'], "{$this->settings['tablePrefix']}questions", $result)) {
			$html  = "\n<p><img src=\"/images/icons/cross.png\" class=\"icon\" /> Sorry, an error occured.</p>";
			return $html;
		}
		$questionId = $this->databaseConnection->getLatestId ();
		
		# Confirm success
		$html  = "\n<p><img src=\"/images/icons/tick.png\" class=\"icon\" /> The question has been added, as shown below.</p>";
		$html .= "\n<p>Do you wish to <a href=\"{$this->baseUrl}/admin/" . __FUNCTION__ . ".html\">add another</a>?</p>";
		$html .= $this->recentlyAddedQuestions ($mostRecent);
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to delete a question
	public function deletequestions ()
	{
		# Start the HTML
		$html = '';
		
		# Obtain the questions not currently connected to any survey
		$query = "
			SELECT
				{$this->settings['tablePrefix']}questions.id,
				CONCAT({$this->settings['tablePrefix']}questions.id, ': ', SUBSTRING({$this->settings['tablePrefix']}questions.question, 1, 70), ' ...') AS question
			FROM {$this->settings['tablePrefix']}questions
			LEFT JOIN {$this->settings['tablePrefix']}surveys ON {$this->settings['tablePrefix']}questions.id = {$this->settings['tablePrefix']}surveys.question
			WHERE {$this->settings['tablePrefix']}surveys.question IS NULL
		;";
		$unusedQuestions = $this->databaseConnection->getPairs ($query);
		
		# End if all in use
		if (!$unusedQuestions) {
			$html .= "\n<p>All questions in the database are currently connected to a survey, so none can be deleted.</p>";
			return $html;
		}
		
		require_once ('ultimateForm.php');
		$form = new form (array (
			'databaseConnection' => $this->databaseConnection,
			'formCompleteText' => false,
		));
		$form->heading ('p', 'Please select a question to delete. Only those questions not currently connected to a survey can be deleted; those in use are not listed here.');
		$form->select (array (
			'name'			=> 'question',
			'title'			=> 'Question',
			'values'		=> $unusedQuestions,
			'required'		=> true,
		));
		if ($result = $form->process ($html)) {
			
			# Delete the question
			$this->databaseConnection->delete ($this->settings['database'], "{$this->settings['tablePrefix']}questions", array ('id' => $result['question']));
			
			# Confirm success
			$html .= "\n<p><img src=\"/images/icons/tick.png\" class=\"icon\" /> The question has been deleted.</p>";
			$html .= "\n<p><a href=\"{$this->baseUrl}/admin/" . __FUNCTION__ . ".html\">Delete another?</a></p>";
		}
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to create a list of questions most recently-added to the database
	private function recentlyAddedQuestions ($mostRecent)
	{
		# Get the latest data, but ordered most recent last
		$recentQuestions = $this->databaseConnection->select ($this->settings['database'], "{$this->settings['tablePrefix']}questions", array (), array ('id', 'question', 'highlight'), true, $orderBy = 'id DESC', $mostRecent);
		
		# Assemble as a list
		$list = array ();
		foreach ($recentQuestions as $id => $question) {
			$list[$id] = '<span class="comment">#' . $id . ': </span>' . $this->applyHighlighting (htmlspecialchars ($question['question']), $question['highlight']);
		}
		
		# Compile the HTML
		$html  = "\n<h3>Most recently-added questions</h3>";
		$html .= "\n<p>Here are the {$mostRecent} questions most recently added to the database:</p>";
		$html .= application::htmlUl ($list, 0, 'spaced');
		
		# Surround in a div
		$html = "\n<div class=\"graybox\">{$html}</div>";
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to create surveys
	public function addsurveys ()
	{
		# Start the HTML
		$html = '';
		
		# Define number of recent questions to show
		$mostRecent = 20;
		
		# Add introduction
		$html .= "\n<p>In this section, you can construct a survey for each area. Note that surveys have to be created one at a time.</p>";
		$html .= "\n<p>The {$mostRecent} most recently-added questions are shown below.</p>";
		
		# Get all elections, including forthcoming
		$elections = $this->getElections (true);
		
		# Create a new form
		require_once ('ultimateForm.php');
		$form = new form (array (
			'databaseConnection' => $this->databaseConnection,
		));
		$form->select (array (
			'name'			=> 'election',
			'title'			=> 'Which election',
			'values'		=> $this->getElectionNames ($elections),
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
			$html .= $this->recentlyAddedQuestions ($mostRecent);
			return $html;
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
		$this->databaseConnection->delete ($this->settings['database'], "{$this->settings['tablePrefix']}surveys", $constraints);
		
		# Insert the data
		if (!$this->databaseConnection->insertMany ($this->settings['database'], "{$this->settings['tablePrefix']}surveys", $data)) {
			$html  = "\n<p><img src=\"/images/icons/cross.png\" class=\"icon\" /> Sorry, an error occured.</p>";
			return $html;
		}
		
		# Confirm success
		$html  = "\n<p><img src=\"/images/icons/tick.png\" class=\"icon\" /> The <a href=\"{$this->baseUrl}/{$result['election']}/{$result['ward']}/\">survey</a> has been added.</p>";
		$html .= "\n<p>Do you wish to <a href=\"{$this->baseUrl}/admin/" . __FUNCTION__ . ".html\">add another</a>?</p>";
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to get a list of ward IDs and names
	private function getWardNames ()
	{
		return $this->databaseConnection->selectPairs ($this->settings['database'], "{$this->settings['tablePrefix']}wards", array (), array ('id', "CONCAT_WS(' ', prefix, ward) AS name"), true, $orderBy = 'name');
	}
	
	
	# Function to get a list of affiliation IDs and names
	private function getAffiliationNames ()
	{
		return $this->databaseConnection->selectPairs ($this->settings['database'], "{$this->settings['tablePrefix']}affiliations", array (), array ('id', 'name'), true, $orderBy = 'name');
	}
	
	
	# Function to get a list of question IDs and texts
	private function getQuestionTexts ()
	{
		return $this->databaseConnection->selectPairs ($this->settings['database'], "{$this->settings['tablePrefix']}questions", array (), array ('id', "CONCAT(id, ': ', SUBSTRING(question, 1, 70), ' ...') AS text"), true, $orderBy = 'id');
	}
	
	
	# Admin helper function to create SQL INSERTS
	public function allocations ()
	{
		# Start the HTML
		$html = '';
		
		# Get all elections, including forthcoming
		$elections = $this->getElections (true);
		
		# Create the form
		require_once ('ultimateForm.php');
		$form = new form (array (
			'formCompleteText' => false,
		));
		$form->select (array (
			'name'			=> 'election',
			'title'			=> 'Which election',
			'values'		=> $this->getElectionNames ($elections),
		));
		$form->textarea (array (
			'name'			=> 'allocations',
			'title'			=> 'Enter the allocations, as wardname[tab]q1[tab]q2, etc., per line',
			'required'		=> true,
			'cols'			=> 80,
			'rows'			=> 10,
		));
		
		# Process the result
		if (!$result = $form->process ($html)) {
			return $html;
		}
		
		# Compile the SQL
		$sql  = "INSERT INTO {$this->settings['tablePrefix']}surveys (election,ward,question) VALUES \n";
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
		$html .= nl2br (htmlspecialchars ($sql));
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to create the mailout (letters) to candidates containing the survey
	public function letters ()
	{
		# Start the HTML
		$html = '';
		
		# Obtain the HTML
		if (!$mailoutHtml = $this->compileMailout (__FUNCTION__, $statusHtml)) {
			$html .= $statusHtml;
			return $html;
		}
		$html .= $mailoutHtml;
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to create the mailout (e-mail) to candidates containing the survey
	public function mailout ()
	{
		# Start the HTML
		$html = '';
		
		# Ensure there is an election supplied
		if (!$this->election) {
			$html .= "\n<p>Please select which election:</p>";
			$html .= $this->listElections ($this->elections, true, false, 'mailout.html');
			return $html;
		}
		
		# Run the mailout routine
		$html .= $this->emailMailoutRoutine (__FUNCTION__, 'e-mails');
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to send reminder e-mails to candidates who have not yet responded to the sur
	public function reminders ()
	{
		# Start the HTML
		$html = '';
		
		# Run the mailout routine
		$html .= $this->emailMailoutRoutine (__FUNCTION__, 'reminder e-mails');
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to reissue an e-mail to a candidate
	public function reissue ()
	{
		# Start the HTML
		$html = '';
		
		# Ensure there is an election supplied
		if (!$this->election) {
			$html .= "\n<p>Please select which election:</p>";
			$html .= $this->listElections ($this->elections, true, false, 'reissue.html');
			return $html;
		}
		
		# Not available once the election is over
		if (!$this->election['active']) {
			return $html .= '<p>This is no longer available now the election is over.</p>';
		}
		
		# Get the candidates
		if (!$candidates = $this->getCandidates (true)) {
			$html .= '<p>There are no candidates at present.</p>';
			return $html;
		}
		
		# Regroup
		$candidatesByWard = application::regroup ($candidates, 'wardId', $removeGroupColumn = false);
		
		# Determine which candidates have responded
		$candidateIdsResponded = $this->getCandidateIdsResponded ($this->election['id']);
		
		# Compile a droplist of candidates, grouped by ward, skipping those that have already responded
		$wardCandidates = array ();
		foreach ($candidatesByWard as $wardId => $candidatesThisWard) {
			foreach ($candidatesThisWard as $candidateId => $candidate) {
				if (in_array ($candidateId, $candidateIdsResponded)) {continue;}
				$wardName = $candidate['ward'] . ':';
				$wardCandidates[$wardName][$candidateId] = $candidate['name'] . '  (' . $candidate['affiliation'] . ')';
			}
		}
		
		# Create a form
		require_once ('ultimateForm.php');
		$form = new form (array (
			'displayRestrictions'	=> false,
			'nullText' => false,
			'formCompleteText' => false,
		));
		$form->heading ('p', 'Use this form to reissue an e-mail to a candidate. Only those candidates that have not yet responded are shown.');
		$form->select (array (
			'name'			=> 'candidate',
			'title'			=> 'Candidate',
			'values'		=> $wardCandidates,
			'required'		=> true,
		));
		$form->email (array (
			'name'			=> 'email',
			'title'			=> 'E-mail address',
			'required'		=> true,
		));
		
		# Process the form
		if ($result = $form->process ($html)) {
			
			# Select the candidate data
			$candidateId = $result['candidate'];
			$candidate = $candidates[$candidateId];
			
			# Update the candidate's e-mail address if changed
			if ($result['email'] != $candidate['email']) {
				$this->databaseConnection->update ($this->settings['database'], "{$this->settings['tablePrefix']}candidates", array ('email' => $result['email']), array ('id' => $candidateId));
				$candidate['email'] = $result['email'];		// Update the candidate
			}
			
			# Create the e-mail
			$email = $this->createEmail ($candidate, 'mailout');
			
			# Send the e-mail
			$emails = array ($candidateId => $email);
			$html .= $this->sendEmails ($emails);
			
			# Provide a reset page link
			$html .= "<p><a href=\"{$this->baseUrl}/{$this->election['id']}/" . __FUNCTION__ . ".html\">Send another.</a></p>";
		}
		
		# Return the HTML
		return $html;
	}
	
	
	# Internal mailout routine
	private function emailMailoutRoutine ($function, $type)
	{
		# Start the HTML
		$html = '';
		
		# Not available once the election is over
		if (!$this->election['active']) {
			$html .= '<p>This is no longer available now the election is over.</p>';
			return $html;
		}
		
		# Assemble the e-mails
		$emails = $this->compileMailout ($function, $statusHtml, $emailsPreviewHtml /* returned by reference */);
		if ($emails === false) {
			$html .= $statusHtml;
			return $html;
		}
		
		# End if no e-mails for this election
		if (!$emails) {
			$html .= "\n<p>There are no candidates with e-mails for this election.</p>";
			return $html;
		}
		
		# Ask for confirmation
		$total = count ($emails);
		$message = "Are you sure you want to send the {$type}, of {$total} " . ($total == 1 ? 'e-mail' : 'e-mails') . "? (A preview of each e-mail is shown below.)";
		$confirmation = "Yes, send the {$type}";
		if (!$this->areYouSure ($message, $confirmation, $formHtml)) {
			$html .= $formHtml;
			$html .= $emailsPreviewHtml;
			return $html;
		}
		
		# Send the e-mails
		$html .= $this->sendEmails ($emails);
		
		# Provide a reset page link
		$html .= "<p><a href=\"{$this->baseUrl}/{$this->election['id']}/" . $function . ".html\">Reset page.</a></p>";
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to send the e-mails and report the outcome
	private function sendEmails ($emails)
	{
		# Prevent timeouts as the script may run for a long time if there are a lot of candidates
		set_time_limit (0);
		
		# Send each e-mail
		$sendingOutcomes = array ();
		foreach ($emails as $candidateId => $email) {
			$result = application::utf8Mail ($email['to'], $email['subject'], wordwrap ($email['message']), "From: {$this->settings['emailFrom']}\r\nCc: {$this->settings['emailCc']}");
			$sendingOutcomes[$candidateId] = ($result ? '<span class="success"><strong>Sent OK</strong></span>' : '<span class="warning"><strong>Failure</strong></span>') . ': ' . htmlspecialchars ($email['to']);
			usleep (250000);		// Wait quarter of a second between mails; note that a server running PHP under FastCGI will have default FcgidIOTimeout=40 so this would enable 40*4=160 e-mails
		}
		
		# Show the result
		$html  = "\n<p>The following e-mails were sent:</p>";
		$html .= application::htmlUl ($sendingOutcomes);
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to add an 'Are you sure?' form
	private function areYouSure ($message, $confirmation, &$html)
	{
		# Start the HTML
		$html = '';
		
		# Create the form
		require_once ('ultimateForm.php');
		$form = new form (array (
			'formCompleteText' => false,
			'nullText' => false,
			'div' => 'graybox',
			'displayRestrictions' => false,
			'requiredFieldIndicator' => false,
		));
		$form->heading ('p', $message);
		$form->checkboxes (array (
			'name'				=> 'confirmation',
			'title'				=> 'Confirm',
			'values'			=> array ($confirmation),
			'required'			=> true,	// Ensures that a submission must be ticked for the form to be successful
		));
		
		# Process the form
		$result = $form->process ($html);
		
		# Return status
		return $result;
	}
	
	
	# Function to compile a mailout, for either letters or e-mail
	private function compileMailout ($type /* =letters/mailout/reminders */, &$html, &$emailsPreviewHtml = '')
	{
		# Start the general status HTML
		$html = '';
		
		# Ensure there is an election supplied
		if (!$this->election) {
			$html .= "\n<p>Please select which election:</p>";
			$html .= $this->listElections ($this->elections, true, false, "{$type}.html");
			return false;
		}
		
		# Get the candidates
		if (!$candidates = $this->getCandidates (true)) {
			$html .= '<p>There are no candidates at present.</p>';
			return false;
		}
		
		# Get the surveys
		if (!$surveys = $this->getQuestions (false, $this->election['id'])) {
			$html .= '<p>There are no questions at present.</p>';
			return false;
		}
		
		# Determine whether candidates have responded
		if ($type == 'reminders') {
			$candidateIdsResponded = $this->getCandidateIdsResponded ($this->election['id']);
		}
		
		# Increase allowed execution time
		ini_set ('max_execution_time', 3600);
		
		# Regroup
		$surveys = application::regroup ($surveys, 'wardId', $removeGroupColumn = false);
		$candidates = application::regroup ($candidates, 'wardId', $removeGroupColumn = false);
		
		# Start an HTML output and list of e-mails
		$outputHtml = '';
		$emails = array ();
		
		# Add to the e-mail preview HTML
		$emailsPreviewHtml .= "\n<h3>Preview of each e-mail</h3>";
		$emailsPreviewHtml .= "\n<hr />";
		
		# Loop through by ward having surveys
		foreach ($surveys as $ward => $questionnaire) {
			
			# Miss out if no candidates in a ward; a warning is shown if none, in case of trailing spaces, etc.
			if (!isSet ($this->wards[$ward])) {
				$html .= "\n<p class=\"warning\">Warning: No candidates for <em>{$ward}</em> ward.</p>";
				continue;
			}
			
			# Loop through each candidate for this ward
			foreach ($candidates[$ward] as $candidateId => $candidate) {
				
				# For reminders, skip if the candidate has responded
				if ($type == 'reminders') {
					if (in_array ($candidateId, $candidateIdsResponded)) {
						continue;
					}
				}
				
				# For letters, create the HTML for this candidate
				if ($type == 'letters') {
					$outputHtml .= $this->createLetterHtml ($questionnaire, $candidate);
				}
				
				# Create the e-mail, and a preview for display on the form page, if the candidate has an e-mail address
				if ($type != 'letters') {
					if ($candidate['email']) {
						
						# Compile the e-mail data
						$emails[$candidateId] = $this->createEmail ($candidate, $type);
						
						# Create an HTML preview rendering
						$emailsPreviewHtml .= "\n\n<p>To: " . htmlspecialchars ($emails[$candidateId]['to']) . "<br />\nSubject: " . htmlspecialchars ($emails[$candidateId]['subject']) . '</p>';
						$emailsPreviewHtml .= "\n" . nl2br (htmlspecialchars ($emails[$candidateId]['message']));
						$emailsPreviewHtml .= "\n<br />\n<br />\n<hr />";
					}
				}
			}
		}
		
		# Return either the HTML or the e-mails
		return ($type == 'letters' ? $outputHtml : $emails);
	}
	
	
	# Function to get a list of candidate IDs that have responded to an election
	private function getCandidateIdsResponded ($electionId)
	{
		# Get the candidates who have responded to any questions for their survey
		$query = "SELECT
				DISTINCT candidate
			FROM {$this->settings['tablePrefix']}responses
			LEFT JOIN {$this->settings['tablePrefix']}surveys ON {$this->settings['tablePrefix']}surveys.id = {$this->settings['tablePrefix']}responses.survey
			WHERE election = :election
			ORDER BY candidate
		;";
		$candidateIds = $this->databaseConnection->getPairs ($query, false, array ('election' => $electionId));
		
		# Return the IDs
		return $candidateIds;
	}
	
	
	# Function to create an individual letter to a candidate
	private function createLetterHtml ($questionnaire, $candidate)
	{
		# Start the HTML for this survey
		$html = '';
		
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
		
		# Define the submission URL
		$submissionUrl = ((substr ($_SERVER['SERVER_NAME'], 0, 4) != 'www.') ? 'https://' : '') . "{$_SERVER['SERVER_NAME']}{$this->baseUrl}/submit/";
		
		# Show the letterhead
		$html .= "
			<table class=\"header\" cellpadding=\"0\" cellspacing=\"0\">
				<tr>
					<td class=\"address\">
						{$candidate['_nameUncolouredNoAffiliation']},<br />
						" . str_replace (',', ',<br />', htmlspecialchars ($candidate['address'])) . "
					</td>
					<td class=\"letterhead\">
						{$this->election['letterheadHtml']}
						<p>" . date ('jS F Y') . "</p>
					</td>
				</tr>
				<tr>
					<td colspan=\"2\">
						<p>&nbsp;</p>
						<p>&nbsp;</p>
						<p>Dear " . $wardName . ' ' . $this->settings['division'] . " candidate,</p>
						" . $this->election['organisationIntroductionHtml'] . "
						<p>We ask candidates to submit their responses via the automated facility on our website. Just go to: <u>{$submissionUrl}</u> and enter your verification number: <strong>{$candidate['verification']}</strong>. The website version also contains links giving further information.</p>
						" . $screenshotHtml . "
						<p>If you are unable to complete this survey online or you require any other assistance please e-mail, phone or write to us and we will be happy to make alternative arrangements.</p>
						<p>Many thanks for your time.<br />Yours sincerely,</p>
						<p>" . htmlspecialchars ($this->election['letterSignatureName']) . ",<br />" . htmlspecialchars ($this->settings['letterSignaturePosition']) . ', ' . htmlspecialchars ($this->settings['letterSignatureOrganisationName']) . "</p>
						<p>&nbsp;</p>
					</td>
				</tr>
			</table>
		";
		
		# Show the questions
		$i = 0;
		foreach ($questionnaire as $question) {
			$i++;
			$html .= '<hr />';
			$question['question'] = htmlspecialchars ($question['question']);
			$html .= "\n<p><strong>Question {$i}</strong>: {$question['question']}</p>";
			$html .= "\n" . $this->formatLinks ($question['links'], true);
			$html .= "\n<p>Your response (ideally, please submit this online - see above) :</p>";
			$html .= "<p>&nbsp;</p><p>&nbsp;</p>";
		}
		
		# Add page break and PS
		$html .= "<p>&nbsp;</p><p>&nbsp;</p><p>(End of survey)</p>";
		$html .= $this->settings['postSubmissionHtmlLetters'];
		$html .= "<div class=\"pagebreak\"></div>";
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to create an individual e-mail to a candidate
	private function createEmail ($candidate, $type)
	{
		# Assemble the ward name
		$wardName = $this->wardName ($candidate);
		
		# Define the submission URL
		$submissionUrl = "https://{$_SERVER['SERVER_NAME']}{$this->baseUrl}/submit/";
		
		# Assemble the text
		#!# Entities being shown in ward name, e.g. "Dear Sawston &amp; Shelford Division candidate,"
		$text  = "\n";
		if ($type == 'reminders') {
			$text .= "\n" . 'Dear candidate - Just a reminder of this below - thanks in advance for your time.' . "\n\n--\n\n";
		}
		$text .= "\n" . 'Dear ' . $wardName . ' ' . $this->settings['division'] . ' candidate,';
		$text .= "\n" . preg_replace ("|\n\s+|", "\n\n", strip_tags (str_replace (' www', ' https://www', $this->election['organisationIntroductionHtml'])));
		$text .= "\n";
		$text .= "\n" . 'Please access the survey and submit your responses online, here:';
		$text .= "\n";
		$text .= "\n" . "{$submissionUrl}";
		$text .= "\n";
		$text .= "\n" . "You will need to use this verification number: {$candidate['verification']} .";
		$text .= "\n";
		$text .= "\n";
		$text .= "\n" . 'Many thanks for your time.';
		$text .= "\n" . 'Yours sincerely,';
		$text .= "\n";
		$text .= "\n" . $this->election['letterSignatureName'] . ',';
		$text .= "\n" . $this->settings['letterSignaturePosition'] . ', ' . $this->settings['letterSignatureOrganisationName'];
		$text .= "\n";
		$text .= "\n";
		$text .= "\n" . preg_replace ("|\n\s+|", "\n\n", strip_tags (str_replace (' www', ' https://www', $this->settings['postSubmissionHtmlLetters'])));
		
		# Compile the e-mail
		$email = array (
			// 'to'		=> '"' . $candidate['name'] . '" ' . '<' . $candidate['email'] . '>',
			'to'		=> $candidate['email'],
			'subject'	=> ($type == 'reminders' ? 'REMINDER: ' : '') . $this->settings['emailSubject'],
			'message'	=> $text,
		);
		
		# Return the e-mail
		return $email;
	}
	
	
	# Function to specify the elected candidates
	public function elected ()
	{
		# Start the HTML
		$html = '';
		
		# Ensure there is an election supplied
		if (!$this->election) {
			$html .= "\n<p>Please select which election:</p>";
			$html .= $this->listElections ($this->elections, true, false, 'elected.html');
			return $html;
		}
		
		# Ensure the election is no longer active
		if ($this->election['active']) {
			return $html .= '<p>This cannot be done until after the election is over.</p>';
		}
		
		# Get the candidates
		if (!$candidates = $this->getCandidates (true)) {
			return $html .= '<p>There are no candidates at present.</p>';
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
			$query = "UPDATE {$this->settings['tablePrefix']}candidates SET elected = 1 WHERE id IN({$in});";
			$this->databaseConnection->query ($query);
		}
		
		# Return the HTML
		return $html;
	}
}

?>
