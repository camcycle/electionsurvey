<?php

#!# Add some sort of testing candidate system

# Class to create an elections lobbying system
class elections
{
	# Class properties
	private $defaults;
	private $baseUrl;
	private $errors;
	private $settings;
	private $databaseConnection;
	private $actions;
	private $action;
	private $internalAuthClass;
	private $user;
	private $userIsAdministrator;
	private $elections;
	private $election;
	private $area;
	private $areas;
	private $candidate;
	private $candidates;
	
	
	# Defaults; NULL indicates a required argument
	public function defaults ()
	{
		# Define and return the defaults
		return $defaults = array (
			
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
			
			# Webmaster e-mail (overriden by settings, once loaded)
			'webmaster' => (isSet ($_SERVER['SERVER_ADMIN']) ? $_SERVER['SERVER_ADMIN'] : NULL),
			
			# Temporary override of admin privileges
			'overrideAdmin' => false,
			
			# Define number of recent questions to show in some admin listings
			'mostRecent' => 20,
		);
	}
	
	
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
			'area'			=> array (
				'description' => false,
				'url' => '%area/',
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
				'description' => 'Cabinet members in surveyed areas restanding in this election',
				'url' => 'cabinet.html',
				'election' => true,
			),
			'admin'			=> array (
				'description' => false,
				'url' => 'admin/',
				'administrator' => true,
			),
			'settings' => array (
				'description' => 'Settings and configuration for this system',
				'description' => 'Overall system configuration',
				'url' => 'admin/settings.html',
				'administrator' => true,
				'admingroup' => 'system',
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
			'showareas'		=> array (
				'description' => 'Show existing areas',
				'url' => 'admin/showareas.html',
				'administrator' => true,
				'admingroup' => 'areas',
			),
			'addarea'		=> array (
				'description' => 'Add an area',
				'url' => 'admin/addarea.html',
				'administrator' => true,
				'admingroup' => 'areas',
			),
			'showaffiliations'	=> array (
				'description' => 'Show existing political parties/groups',
				'url' => 'admin/showaffiliations.html',
				'administrator' => true,
				'admingroup' => 'affilations',
			),
			'addaffiliations'	=> array (
				'description' => 'Add details of a political party/group',
				'url' => 'admin/addaffiliations.html',
				'administrator' => true,
				'admingroup' => 'affilations',
			),
			#!# Not present
			'editaffiliations'	=> array (
				'description' => 'Edit details of a political party/group',
				'url' => 'admin/editaffiliations.html',
				'administrator' => true,
				'admingroup' => 'affilations',
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
			'editquestions'	=> array (
				'description' => 'Edit a question',
				'url' => 'admin/editquestions.html',
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
			'editsurvey'	=> array (
				'description' => 'Show/edit existing surveys',
				'url' => 'admin/editsurvey/',
				'administrator' => true,
				'admingroup' => 'surveys',
				'election' => true,
			),
			'allocations'	=> array (
				'description' => 'Convert an questions allocations spreadsheet into SQL (shortcut for techies only)',
				'url' => 'admin/allocations.html',
				'administrator' => true,
				'admingroup' => 'surveys',
				//'election' => true,
			),
			'addcandidate'	=> array (
				'description' => 'Add a candidate',
				'url' => 'admin/addcandidate.html',
				'administrator' => true,
				'admingroup' => 'candidates',
				'election' => true,
			),
			'addcandidates'	=> array (
				'description' => 'Mass-import candidates data',
				'url' => 'admin/addcandidates.html',
				'administrator' => true,
				'admingroup' => 'candidates',
				'election' => true,
			),
			'editcandidate'	=> array (
				'description' => 'Edit candidate',
				'url' => 'admin/editcandidate/',
				'administrator' => true,
				'admingroup' => 'candidates',
				'election' => true,
			),
			'mailout'		=> array (
				'description' => 'Send e-mail mailout to candidates containing the survey',
				'url' => 'admin/mailout.html',
				'administrator' => true,
				'admingroup' => 'issue',
				'election' => true,
			),
			'letters'		=> array (
				'description' => 'Print mailout (letters) to candidates containing the survey',
				'url' => 'admin/letters.html',
				'administrator' => true,
				'admingroup' => 'issue',
				'election' => true,
			),
			'candidates'	=> array (
				'description' => 'Manage/list candidates, including check status, get ID, and reissue e-mail',
				'url' => 'admin/candidates.html',
				'administrator' => true,
				'admingroup' => array ('during', 'candidates'),
				'election' => true,
			),
			'viewincoming'		=> array (
				'description' => 'View responses as they come in, including response rates',
				'url' => 'admin/viewincoming.html',
				'administrator' => true,
				'admingroup' => 'during',
				'election' => true,
			),
			'submit'		=> array (
				'description' => 'Candidate survey response form',
				'url' => 'submit/',
				'admingroup' => 'during',
			),
			'reissue'		=> array (
				'description' => 'Reissue an e-mail to a candidate',
				'url' => 'admin/reissue.html',
				'administrator' => true,
				'admingroup' => 'during',
				'election' => true,
			),
			'reminders'		=> array (
				'description' => 'Send reminder e-mails to candidates who have not yet responded to the survey',
				'url' => 'admin/reminders.html',
				'administrator' => true,
				'admingroup' => 'during',
				'election' => true,
			),
			'elected'		=> array (
				'description' => 'Specify the elected candidates',
				'url' => 'admin/elected.html',
				'administrator' => true,
				'admingroup' => 'postelection',
				'election' => true,
			),
			'logininternal' => array (
				'description' => 'Login',
				'url' => 'login/',
				'usetab' => 'home',
			),
			'register' => array (
				'description' => 'Create a new account',
				'url' => 'login/register/',
				'usetab' => 'home',
			),
			'resetpassword' => array (
				'description' => 'Reset a forgotten password',
				'url' => 'login/resetpassword/',
				'usetab' => 'home',
			),
			'accountdetails' => array (
				'description' => 'Change login account details',
				'url' => 'login/accountdetails/',
				'usetab' => 'home',
				'authentication' => true,
			),
			'logoutinternal' => array (
				'description' => 'Logout',
				'url' => 'login/logout/',
				'usetab' => 'home',
			),
			'loggedout' => array (
				'description' => 'Logged out',
				'url' => 'loggedout.html',
				'usetab' => 'home',
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
		
		# Load defaults
		$this->defaults = $this->defaults ();
		
		# Start the HTML
		$html = '';
		
		# Get the base URL
		$this->baseUrl = application::getBaseUrl ();
		
		# Load the local stylesheet
		$html .= "\n<style type=\"text/css\" media=\"all\">@import \"{$this->baseUrl}/css/elections.css\";</style>";
		
		# Function to merge the config-supplied arguments; note that $errors returns the errors by reference and not as a result from the method; database-supplied settings will be merged in below
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
			$errorInfo = $this->databaseConnection->error ();
			$message = 'There was a problem with initalising the election facility at the database connection stage. The database server said: ' . $errorInfo['error'] . '.';
			mail ($this->settings['webmaster'], 'Problem with election system on ' . $_SERVER['SERVER_NAME'], wordwrap ($message));
			$html .= "<p class=\"warning\">Apologies - this facility is currently unavailable, as a technical error occured. The Webmaster has been informed and will investigate.</p>";
			$html = $this->settings['headerHtml'] . $html . $this->settings['footerHtml'];
			echo $html;
			return false;
		};
		
		# Ensure the structure is present, by testing for the settings row, and if not, install the structure
		$settingsRowExists = $this->databaseConnection->selectOne ($this->settings['database'], "{$this->settings['tablePrefix']}settings", array ('id' => 1));		// A test for tableExists is not reliable as some hosting may not have sufficient database privileges
		if (!$settingsRowExists) {
			$html .= $this->databaseSetup ();
			$html = $this->settings['headerHtml'] . $html . $this->settings['footerHtml'];
			echo $html;
			return;
		}
		
		# Obtain the actions
		$this->actions = $this->actions ();
		
		# Set the action, checking that a valid page has been supplied
		if (!isSet ($_GET['action']) || !array_key_exists ($_GET['action'], $this->actions)) {
			$html = $this->pageNotFound ();
			$html = $this->settings['headerHtml'] . $html . $this->settings['footerHtml'];
			echo $html;
			return false;
		}
		$this->action = $_GET['action'];

		# Obtain the user
		$this->loadInternalAuth ();
		$this->user = $this->internalAuthClass->getUserId ();
		$this->userIsAdministrator = $this->internalAuthClass->hasPrivilege ('administrator');
		$html .= $this->internalAuthClass->getHtml ();
		
		# Get the settings from the settings table
		$this->addSettingsTableConfig ();
		
		# On pages requiring administrative credentials, ensure the user is an administrator
		if (isSet ($this->actions[$this->action]['administrator']) && $this->actions[$this->action]['administrator']) {
			if (!$this->userIsAdministrator && !$this->settings['overrideAdmin']) {
				$path = $this->baseUrl . preg_replace ('/' . addcslashes ($this->baseUrl, '/') . '/', '', $_SERVER['REQUEST_URI']);		// I.e. local path after baseUrl
				$html = "\n<p>You must be <a href=\"{$this->baseUrl}/{$this->actions['logininternal']['url']}?" . htmlspecialchars ($path) . '">logged in</a> as an administrator to access this page.</p>';
				$html = $this->settings['headerHtml'] . $html . $this->settings['footerHtml'];
				echo $html;
				return false;
			}
		}
		
		# Get the elections available
		$this->elections = $this->getElections ();
		
		# Determine which election
		$this->election = ((isSet ($_GET['election']) && isSet ($this->elections[$_GET['election']])) ? $this->elections[$_GET['election']] : false);
		
		# Get the areas available for this election (or false if no areas)
		$this->area = false;
		$this->areas = array ();
		if ($this->election) {
			$this->areas = $this->getAreasForElection ($this->election['id']);
			
			# Determine which area
			$this->area = ((isSet ($_GET['area']) && isSet ($this->areas[$_GET['area']])) ? $this->areas[$_GET['area']] : false);
		}
		
		# Get the candidates standing in this election for this area (or false if no candidates)
		$this->candidate = false;
		$this->candidates = array ();
		if ($this->area) {
			$this->candidates = $this->getCandidates ($this->election['id'], false, $this->area);
			
			# Determine which candidate
			$this->candidate = ((isSet ($_GET['candidate']) && isSet ($this->areas[$_GET['candidate']])) ? $this->candidates[$_GET['candidate']] : false);
		}
		
		# Open the div surrounding the application
		$html .= "\n<div id=\"elections\">";
		
		# Show the heading
		$html .= "\n<h1>" . htmlspecialchars ($this->settings['applicationName']) . '</h1>';
		if ($this->election) {
			$html .= $this->droplistNavigation ();
		}
		
		# Add link back to admin menu
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
	
	
	# Function to set up the database
	private function databaseSetup ()
	{
			# Load the database structure, including the user table
			$this->actions = $this->actions ();		// Pre-requisite for loadInternalAuth
			$this->loadInternalAuth ();
			$databaseStructure = $this->databaseStructure ();
			
			# Run the queries
			$this->databaseConnection->connection->setAttribute (PDO::ATTR_EMULATE_PREPARES, 1);	// Allow multiple queries per statement
			if (!$result = $this->databaseConnection->query ($databaseStructure)) {
				$html  = "\n<p>The database setup process did not complete. You will probably need to set this up manually. The database error was:</p>";
				$databaseError = $this->databaseConnection->error ();
				$html .= "\n<p><pre>" . htmlspecialchars ($databaseError[2]) . '</pre></p>';
				$html .= "\n<p><pre>" . htmlspecialchars ($databaseStructure) . '</pre></p>';
				return $html;
			}
			
			# Confirm success
			$html  = "\n<p><img src=\"{$this->baseUrl}/images/icons/tick.png\" class=\"icon\" /> The database structure has been successfully installed.</p>";
			$html .= "\n<p><a href=\"{$this->baseUrl}/{$this->actions['register']['url']}\">Now create the first user account.</a></p>";
			return $html;
	}
	
	
	# Function to define the database structure
	#!# Not currently auto-executed on first run
	/*
		# Documentation - joins in the database structure are:
		
		candidates:
			election	Election / year (join to elections)
			areaId	Area (join to areas)
			affiliation	Affiliation (join to affiliations)
		
		responses:
			candidate	Candidates (join to candidates)
			survey	Survey (join to surveys)
		
		surveys:
			election	Election / year (join to elections)
			areaId	Area (join to areas)
			question	Question (join to questions)
	*/
	private function databaseStructure ()
	{
		#!# Modifications currently have to be made to be made in the elections_areas table for areas - these should be moved to settings
		# This setup requires CREATE rights
		$sql = "
			
			CREATE TABLE IF NOT EXISTS `{$this->settings['tablePrefix']}affiliations` (
			  `id` varchar(255) NOT NULL COMMENT 'Unique key',
			  `name` varchar(255) NOT NULL COMMENT 'Party / affiliation name',
			  `colour` varchar(6) NOT NULL COMMENT 'Colour',
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Affiliations (political parties)';
			
			CREATE TABLE IF NOT EXISTS `{$this->settings['tablePrefix']}candidates` (
			  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Unique key',
			  `election` varchar(255) NOT NULL COMMENT 'Election / year',
			  `areaId` varchar(255) NOT NULL COMMENT 'Area',
			  `forename` varchar(255) NOT NULL COMMENT 'Forename',
			  `surname` varchar(255) NOT NULL COMMENT 'Surname',
			  `address` varchar(255) NOT NULL COMMENT 'Address',
			  `email` varchar(255) DEFAULT NULL COMMENT 'E-mail address',
			  `verification` varchar(6) NOT NULL COMMENT 'Verification ID',
			  `affiliation` varchar(255) NOT NULL COMMENT 'Affiliation',
			  `cabinetRestanding` varchar(255) DEFAULT NULL COMMENT 'Whether the candidate is a restanding Cabinet member, and if so, their current Cabinet post',
			  `private` int(1) DEFAULT NULL,
			  `elected` int(1) DEFAULT NULL COMMENT 'Candidate elected',
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `verification` (`verification`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Candidates';
			
			CREATE TABLE IF NOT EXISTS `{$this->settings['tablePrefix']}elections` (
			  `id` varchar(255) NOT NULL COMMENT 'Unique ID (used for the URL)',
			  `name` varchar(255) NOT NULL COMMENT 'Name of election',
			  `description` varchar(255) NOT NULL COMMENT 'Description of election',
			  `areaType` varchar(255) NOT NULL COMMENT 'Type of electoral area, e.g. ward / division / constituency / Combined Authority area / area',
			  `areaTypePlural` varchar(255) NOT NULL COMMENT 'Type of electoral area - plural, e.g. wards / divisions / constituencies / Combined Authority areas / areas',
			  `startDate` date NOT NULL COMMENT 'Survey opening date (date of official Publication of Statement of Persons Nominated, or as soon as possible after - but never before)',
			  `resultsDate` date NOT NULL COMMENT 'Date when responses become visible (e.g. 2 weeks before election day)',
			  `resultsVisibleTime` TIME NOT NULL DEFAULT '21:00:00' COMMENT 'Results visible time (hh:mm:ss)',
			  `endDate` date NOT NULL COMMENT 'Date of election (and close of survey submissions)',
			  `letterheadHtml` TEXT NOT NULL COMMENT  'Letterhead address (top-right)',
			  `organisationIntroductionHtml` TEXT NOT NULL COMMENT 'Survey letter/e-mail introduction',
			  `letterSignatureName` varchar(255) NOT NULL COMMENT 'Signature name',
			  `letterSignaturePosition` varchar(255) NOT NULL COMMENT 'Signature position',
			  `directionsUrl` varchar(255) NOT NULL default 'https://www.cyclestreets.net/' COMMENT 'Directions to cycle to polling stations',
			  PRIMARY KEY  (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Election overview';
			
			CREATE TABLE IF NOT EXISTS `{$this->settings['tablePrefix']}questions` (
			  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Unique key',
			  `question` text NOT NULL COMMENT 'Text of question',
			  `highlight` varchar(255) DEFAULT NULL COMMENT 'Piece of text within the question to highlight in bold (optional)',
			  `links` text DEFAULT NULL COMMENT 'Background links (as URL then text)',
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Available questions';

			
			CREATE TABLE IF NOT EXISTS `{$this->settings['tablePrefix']}responses` (
			  `id` int(11) NOT NULL auto_increment COMMENT 'Unique key',
			  `candidate` int(11) NOT NULL default '0' COMMENT 'Candidates (join to candidates)',
			  `survey` int(11) NOT NULL default '0' COMMENT 'Survey (join to surveys)',
			  `response` text COMMENT 'Response',
			  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP COMMENT 'Timestamp',
			  PRIMARY KEY  (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Responses';
			
			CREATE TABLE IF NOT EXISTS `{$this->settings['tablePrefix']}surveys` (
			  `id` int(11) NOT NULL auto_increment COMMENT 'Unique key',
			  `election` varchar(255) NOT NULL COMMENT 'Election / year (join to elections)',
			  `areaId` varchar(255) NOT NULL COMMENT 'Area (join to areas)',
			  `question` int(11) NOT NULL default '0' COMMENT 'Question (join to questions)',
			  `ordering` int(1) default NULL COMMENT 'Ordering',
			  PRIMARY KEY  (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Surveys';
			
			CREATE TABLE IF NOT EXISTS `{$this->settings['tablePrefix']}areas` (
			  `id` varchar(255) NOT NULL COMMENT 'Unique key',
			  `prefix` varchar(255) default NULL COMMENT 'Area name prefix',
			  `areaName` varchar(255) NOT NULL COMMENT 'Area name',
			  `districtCouncil` enum('','Cambridge City Council','South Cambridgeshire District Council','East Cambridgeshire District Council','Fenland District Council','Huntingdonshire District Council','-') NOT NULL COMMENT 'District council area',
			  PRIMARY KEY  (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Areas';
			
			CREATE TABLE `{$this->settings['tablePrefix']}settings` (
			  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Automatic key (ignored)',
			  `welcomeTextHtml` text NOT NULL COMMENT 'Welcome text on front page',
			  `introductoryTextHtml` text NOT NULL COMMENT 'Introductory text for ballots (also on main page)',
			  `imprint` text NOT NULL COMMENT 'Imprint',
			  `emailSubject` varchar(255) NOT NULL COMMENT 'E-mail subject',
			  `emailFrom` varchar(255) NOT NULL COMMENT 'E-mail ''From'' address',
			  `emailCc` varchar(255) NOT NULL COMMENT 'E-mail Cc address',
			  `organisationConstituentsType` varchar(255) NOT NULL COMMENT 'Organisation people type (e.g. members / supporters)',
			  `letterSignatureOrganisationName` varchar(255) NOT NULL COMMENT 'E-mails/letters signature - organisation name',
			  `postSubmissionHtmlLetters` text DEFAULT NULL COMMENT 'E-mails/letters P.S.',
			  `postSubmissionHtml` text DEFAULT NULL COMMENT 'Post-submission message (webpage)',
			  `recipient` varchar(255) NOT NULL COMMENT 'Survey submission receipts e-mail address',
			  `showAddresses` TINYINT(1) NULL COMMENT 'Show candidate postal addresses?',
			  `webmaster` varchar(255) NOT NULL COMMENT 'System administrator (webmaster) e-mail address',
			  `listArchived` tinyint(1) DEFAULT 1 COMMENT 'List archived elections?',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Settings';
			
			-- Create the settings row
			INSERT INTO {$this->settings['tablePrefix']}settings (id, welcomeTextHtml, introductoryTextHtml, imprint, emailSubject, emailFrom, emailCc, organisationConstituentsType, letterSignatureOrganisationName, recipient, webmaster) VALUES (1, '', '', '', '', '', '', '', '', '', '');
			
			-- Users
		";
		
		# Add users
		$sql .= $this->internalAuthClass->databaseStructure ();
		
		# Return the SQL
		return $sql;
	}
	
	
	# Function to get settings table config
	private function addSettingsTableConfig ()
	{
		# Add table name prefix if required
		$this->settings['settingsTable'] = $this->settings['tablePrefix'] . 'settings';
		
		# Ensure the settings table exists
		$tables = $this->databaseConnection->getTables ($this->settings['database']);
		if (!in_array ($this->settings['settingsTable'], $tables)) {return false;}
		
		# Get the settings
		if (!$settingsFromTable = $this->databaseConnection->selectOne ($this->settings['database'], $this->settings['settingsTable'], array ('id' => 1))) {return false;}
		
		# Merge in the settings, ignoring the id, and overwriting anything currently present
		foreach ($settingsFromTable as $key => $value) {
			if ($key == 'id') {continue;}
			$this->settings[$key] = $value;
		}
	}
	
	
	# Page not found wrapper
	private function pageNotFound ()
	{
		# Send the header
		header ('HTTP/1.1 404 Not Found');
		
		# Create the 404 page
		$html = "
		<h1>Page not found</h1>
		<p>The page you requested cannot be found. You may wish to:</p>
		<ul>
			<li>Go to the <a href=\"{$this->baseUrl}/\">front page</a> of this section, or</li>
			<li>Use the navigation menu to navigate to the information you're after.</li>
		</ul>";
		
		# Return the HTML
		return $html;
	}
	
	
	
	# Home page
	public function home ()
	{
		# Introductory text
		$html  = $this->settings['welcomeTextHtml'];
		$html .= "<p class=\"graphic\"><img src=\"{$this->baseUrl}/images/pollingstations.jpg\" width=\"89\" height=\"121\" alt=\"Ballot box\" /></p>";
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
			header ('HTTP/1.1 404 Not Found');
			$html = '<p>There is no such election. Please check the URL and try again.</p>';
			return $html;
		}
		
		# Add introduction
		$html  = $this->settings['introductoryTextHtml'];
		
		# Add the summary table and areas
		$html .= "<p class=\"graphic\"><img src=\"{$this->baseUrl}/images/pollingstations.jpg\" width=\"89\" height=\"121\" alt=\"Ballot box\" /></p>";
		$html .= $this->showOverviewDetails ($this->election);
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to show the summary table and areas
	private function showOverviewDetails ($election)
	{
		# Table of data about the election
		$html  = $this->summaryTable ($election);
		
		# List areas
		$html .= $this->areasListing ($election);
		
		# Show administrative options for this election
		if ($this->userIsAdministrator) {
			$html .= "\n<br />";
			$html .= "\n<h2>Administrative options for this election</h2>";
			$html .= "\n<p><a href=\"{$this->baseUrl}/{$election['id']}/admin/\">Administrative area for this election</a></p>";
		}
		
		# Return the HTML
		return $html;
	}
	
	
	# Area page
	public function area ()
	{
		# Start the HTML
		$html = '';
		
		# Validate the election
		if (!$this->election) {
			header ('HTTP/1.1 404 Not Found');
			$html = '<p>There is no such election. Please check the URL and try again.</p>';
			return $html;
		}
		
		# Validate the area
		if (!$this->area) {
			header ('HTTP/1.1 404 Not Found');
			$html = '<p>There is no such ' . $this->election['areaType'] . ' being contested in this election. Please check the URL and try again.</p>';
			return $html;
		}
		
		# Remind administrators
		if ($this->userIsAdministrator && !$this->election['resultsVisible']) {
			$html .= "\n<p class=\"warning\"><strong>Note: any responses shown because are only visible to you because you are an administrator.</strong> The responses will not be made public until at least {$this->election['visibilityDateTime']}.</p>";
		}
		
		# Start with a table of data
		$html .= $this->summaryTable ($this->election);
		
		# List the questions asked
		$html .= $this->showQuestions ($this->area['id']);
		
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
			header ('HTTP/1.1 404 Not Found');
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
			header ('HTTP/1.1 404 Not Found');
			return $html = '<p>The specific question number is invalid. Please check the URL and try again.</p>';
		}
		
		# Get the question
		$question = $questions[$questionNumber];
		
		# Determine the total number of questions in this survey
		$total = count ($questions);
		
		#!# This section is over-complex and involves multiple SQL lookups, for the sake of avoiding code duplication in responsesBlock (which has a certain datastructure) - ideally there would be a single OUTER JOIN that would list all candidates and show the responses where the candidate has answered, but this means duplicating lookups like candidate['_name']
		
		# Get the areas (and their associated survey IDs) where this question was asked
		$conditions = array ('question' => $question['questionId'], 'election' => $this->election['id']);
		$areas = $this->databaseConnection->selectPairs ($this->settings['database'], "{$this->settings['tablePrefix']}surveys", $conditions, array ('id', 'areaId'));
		
		# Get the candidates having this question
		$candidates = $this->getCandidates ($this->election['id'], false, false, $areas);
		
		# Get the responses
		$surveyIds = array_keys ($areas);
		$candidateIds = array_keys ($candidates);
		$responses = $this->getResponses ($surveyIds, $candidateIds);
		
		# Start the HTML with the question
		$html .= "\n<p><em>&laquo; Back to <a href=\"{$this->baseUrl}/{$this->election['id']}/questions/\">list of all {$total} questions</a> for this election</em></p>";
		$html .= "\n<h2>Question {$questionNumber} - we asked:</h2>";
		$html .= $this->questionBox ($question);
		$html .= $this->responsesBlock ($question, $candidates, $responses, $areas);
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to list all questions for a particular election
	private function listQuestionsForElection ($questions)
	{
		# Loop through each question
		$list = array ();
		foreach ($questions as $questionNumber => $question) {
			$questionHtml = nl2br (htmlspecialchars ($question['question']));
			$questionHtml = $this->applyHighlighting ($questionHtml, $question['highlight']);
			$list[] = $questionHtml . "<br /><a href=\"{$this->baseUrl}/{$this->election['id']}/questions/{$questionNumber}/\">Read all answers&hellip;</a>";
		}
		
		# Compile the HTML
		$html  = "\n<h2>List of questions</h2>";
		$html .= "\n<p>Here is a list of all the questions (across all {$this->election['areaTypePlural']}) we have asked for this election:</p>";
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
		$data = $this->getQuestions ($electionId, false, $groupByQuestionId = true);
		
		# Reindex from 1 (for the sake of nicer /question/<id>/ URLs) as the keys are effectively arbitrary, keeping only relevant fields (i.e. stripping bogus fields like areaId that have become left behind from the GROUP BY operation)
		$questions = array ();
		$i = 1;
		foreach ($data as $question) {
			$key = $i++;
			$questions[$key] = $question;
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
	
	
	# Function to create an area summary
	private function summaryTable ($election)
	{
		# Compile the HTML
		$html  = "\n<h2>{$election['name']}" . ($this->area ? ': ' . $this->areaName ($this->area) : '') . "</h2>";
		$table['Summary'] = (!$this->area ? $election['description'] : "<a href=\"{$this->baseUrl}/{$election['id']}/\">{$election['description']}</a>");
		$table['Polling date'] = $election['polling date'];
		if ($this->area) {$table[ ucfirst ($election['areaType']) ] = $this->droplistNavigation (true);}
		
		# List the candidates
		if ($this->area) {
			$table['Candidates<br />(by surname)'] = $this->showCandidates ($election);
		}
		
		# Show the respondents
		if (!$this->area) {
			$table['Questions'] = "<a href=\"{$this->baseUrl}/{$election['id']}/questions/\">" . ($election['active'] ? '' : '<strong><img src="' . $this->baseUrl . '/images/icons/bullet_go.png" class="icon" /> ') . 'Index of all questions for this election' . ($election['active'] ? '' : '</strong>') .  '</a>';
			$table['Respondents'] = "<a href=\"{$this->baseUrl}/{$election['id']}/respondents.html\">" . ($election['active'] ? '<strong><img src="' . $this->baseUrl . '/images/icons/bullet_go.png" class="icon" /> ' : '') . 'Index of all respondents' . ($election['active'] ? ' (so far)' : '') .  '</a>';
			if ($cabinetRestanding = $this->getCandidates ($election['id'], false, false, false, $requireCabinetRestanding = true)) {
				$table['Cabinet'] = "<a href=\"{$this->baseUrl}/{$election['id']}/cabinet.html\">Cabinet members in surveyed {$election['areaTypePlural']} restanding in this election</a>";
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
				IF(endDate >= CURDATE(), 1, 0) AS active,
				IF(endDate = CURDATE(), 1, 0) AS votingToday,
				IF(startDate > CURDATE(), 1, 0) AS isForthcoming,
				IF(((DATEDIFF(CURDATE(), endDate) < 28) && endDate < CURDATE()), 1, 0) AS isRecent,
				IF(NOW() < CONCAT(resultsDate,' ',resultsVisibleTime), 0, 1) AS resultsVisible,
				DATE_FORMAT(endDate,'%W %D %M %Y') AS 'polling date',
				CONCAT( LOWER( REPLACE( DATE_FORMAT( CONCAT(resultsDate,' ',resultsVisibleTime),'%l.%i%p, ' ), '.00', '') ), DATE_FORMAT(CONCAT(resultsDate,' ',resultsVisibleTime),'%W %D %M %Y') ) AS visibilityDateTime,
				IF(name LIKE '%county%', 1, 0) AS isCounty
			FROM {$this->settings['tablePrefix']}elections
			WHERE 1=1
			" . ($includeForthcoming ? '' : " AND startDate <= CURDATE()") . "
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
			$html .= "\n<p>There are no election surveys live at present. Have a look at previous surveys below.</p>";
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
		#!# This should take account of listArchived rather than just the listing itself
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
	
	
	
	# Function to get areas being contested in an election
	#!# Name/purpose needs to be clarified against getSurveyAreas ()
	private function getAreasForElection ($electionId, $includeAreaId = false, $convertEntities = true)
	{
		# Get data
		$query = "SELECT
				{$this->settings['tablePrefix']}areas.id,
				{$this->settings['tablePrefix']}areas.prefix,
				{$this->settings['tablePrefix']}areas.areaName,
				COUNT({$this->settings['tablePrefix']}areas.id) AS 'candidates'
			FROM {$this->settings['tablePrefix']}candidates
			LEFT OUTER JOIN {$this->settings['tablePrefix']}areas ON {$this->settings['tablePrefix']}candidates.areaId = {$this->settings['tablePrefix']}areas.id
			WHERE election = :election
			GROUP BY id, prefix, areaName
			ORDER BY {$this->settings['tablePrefix']}areas.areaName
		;";
		$preparedStatementValues = array ('election' => $electionId);
		$data = $this->databaseConnection->getData ($query, "{$this->settings['database']}.{$this->settings['tablePrefix']}areas", true, $preparedStatementValues);
		
		# Add in the constructed area name
		foreach ($data as $key => $area) {
			$data[$key]['_name'] = $this->areaName ($area, $convertEntities) . ($includeAreaId ? " [{$key}]" : '');
		}
		
		# Return the data
		return $data;
	}
	
	
	# Function to add a droplist navigation
	private function droplistNavigation ($areasOnly = false)
	{
		# Start the HTML
		$html = '';
		
		# In areas-only mode, if there is only one area, just return its name - no point showing the jumplist
		if ($areasOnly) {
			if (count ($this->areas) == 1) {
				$area = application::array_first_value ($this->areas);
				$html = $this->areaName ($area);
				return $html;
			}
		}
		
		# Start the list
		$list = array ();
		if (!$areasOnly) {
			$list["{$this->baseUrl}/{$this->election['id']}/"] = 'Overview page';
			$list["{$this->baseUrl}/{$this->election['id']}/questions/"] = 'Questions index';
			$list["{$this->baseUrl}/{$this->election['id']}/respondents.html"] = 'Respondents';
			if ($cabinetRestanding = $this->getCandidates ($this->election['id'], false, false, false, $requireCabinetRestanding = true)) {
				$list["{$this->baseUrl}/{$this->election['id']}/cabinet.html"] = 'Cabinet restanding';
			}
			if ($this->election['votingToday']) {
				$list[$this->election['directionsUrl']] = 'Directions to polling stations';
			}
		}
		
		# Add each area
		foreach ($this->areas as $key => $area) {
			$location = "{$this->baseUrl}/{$this->election['id']}/{$area['id']}/";
			$list[$location] = $this->areaName ($area, $convertEntities = false);
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
		$html = application::htmlJumplist ($list, $selected, $submitTo, 'jumplist', $parentTabLevel = 3, ($areasOnly ? '' : 'jumplist'), ($areasOnly ? '' : 'Jump to:'));
		
		# Show directions to polling stations if required under the main jumplist
		if ($this->election['votingToday']) {
			if (!$areasOnly) {
				$html .= "<p class=\"directionsbutton\"><a class=\"actions right\" href=\"{$this->election['directionsUrl']}\">" . '<img src="' . $this->baseUrl . '/images/icons/map.png" class="icon" /> Cycle to your polling station - get directions</a></p>';
			}
		}
		
		# Surround with a div
		if (!$areasOnly) {
			$html = "\n<div class=\"navigation\">\n" . $html . "\n</div>";
		}
		
		# Return the HTML
		return $html;
	}
	
	
	
	# Function to list areas
	private function areasListing ($election)
	{
		# Start the HTML
		$html  = "\n\n" . '<h2><img src="' . $this->baseUrl . '/images/next.jpg" width="20" height="20" alt="&gt;" border="0" /> Candidates\' responses for each ' . $election['areaType'] . '</h2>';
		$html .= "\n<p>The following " . $election['areaTypePlural'] . " being contested are those for which we have sent questions to candidates:</p>";
		
		# Get the areas for this election
		$areas = $this->getAreasForElection ($election['id']);
		
		# Get the data
		if (!$areas) {
			return $html .= "\n<p>There are no " . $election['areaTypePlural'] . " being contested.</p>";
		}
		
		# Construct the HTML
		foreach ($areas as $key => $area) {
			$areaName = $this->areaName ($area);
			$candidates = "({$area['candidates']} " . ($area['candidates'] == 1 ? 'candidate' : 'candidates') . " standing)";
			$list[$key] = "<a href=\"{$this->baseUrl}/{$election['id']}/{$area['id']}/\">{$areaName}</a> {$candidates}";
		}
		
		# Construct the HTML
		$html .= application::htmlUl ($list);
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to construct an area name
	private function areaName ($area, $convertEntities = true)
	{
		# Convert entities if required
		if ($convertEntities) {
			$area['prefix'] = htmlspecialchars ((string) $area['prefix']);
			$area['areaName'] = htmlspecialchars ($area['areaName']);
		}
		
		# Construct and return the area name
		return (!empty ($area['prefix']) ? $area['prefix'] . ' ' : '') . $area['areaName'];
	}
	
	
	# Function to get candidates in an election
	private function getCandidates ($electionId, $all = false, $onlyArea = false, $inAreas = false, $requireCabinetRestanding = false)
	{
		# Get data
		$query = "SELECT
				{$this->settings['tablePrefix']}candidates.id as id,
				{$this->settings['tablePrefix']}candidates.areaId,
				{$this->settings['tablePrefix']}candidates.elected,
				{$this->settings['tablePrefix']}candidates.cabinetRestanding,
				private, prefix,
				{$this->settings['tablePrefix']}areas.areaName,
				{$this->settings['tablePrefix']}areas.districtCouncil,
				forename, surname, verification, address,
				{$this->settings['tablePrefix']}affiliations.id AS affiliationId,
				{$this->settings['tablePrefix']}affiliations.name as affiliation,
				{$this->settings['tablePrefix']}affiliations.colour,
				CONCAT(forename,' ',UPPER(surname)) as name,
				CONCAT(forename,' ',surname) as nameNaturalCase,
				{$this->settings['tablePrefix']}candidates.email
			FROM {$this->settings['tablePrefix']}candidates
			LEFT OUTER JOIN {$this->settings['tablePrefix']}affiliations ON {$this->settings['tablePrefix']}candidates.affiliation = {$this->settings['tablePrefix']}affiliations.id
			LEFT OUTER JOIN {$this->settings['tablePrefix']}areas ON {$this->settings['tablePrefix']}candidates.areaId = {$this->settings['tablePrefix']}areas.id
			WHERE
				election = :election
				" . ($inAreas ? " AND {$this->settings['tablePrefix']}candidates.areaId IN('" . implode ("','", $inAreas) . "')" : ($onlyArea ? "AND {$this->settings['tablePrefix']}candidates.areaId = '{$onlyArea['id']}'" : '')) . "
				" . ($requireCabinetRestanding ? " AND ({$this->settings['tablePrefix']}candidates.cabinetRestanding IS NOT NULL AND {$this->settings['tablePrefix']}candidates.cabinetRestanding != '')" : '') . "
			ORDER BY " . ($inAreas ? 'affiliation,surname,forename' : ($all ? 'areaId,surname' : 'surname,forename')) . "
		;";
		$preparedStatementValues = array ('election' => $electionId);
		$data = $this->databaseConnection->getData ($query, "{$this->settings['database']}.{$this->settings['tablePrefix']}areas", true, $preparedStatementValues);
		
		# Add in the constructed complete name with affiliation
		foreach ($data as $key => $candidate) {
			$data[$key]['_name'] = "<span style=\"color: #{$candidate['colour']}; font-weight: bold;\">" . htmlspecialchars ($candidate['name']) . ' &nbsp;(' . htmlspecialchars ($candidate['affiliation']) . ')</span>';
		}
		
		# Return the data
		return $data;
	}
	
	
	# Function to show candidates in an area
	private function showCandidates ($election)
	{
		# Get the data
		if (!$this->candidates) {
			return $html = "\n<p>There are no candidates contesting this " . $election['areaType'] . '.</p>';
		}
		
		# Construct the HTML
		foreach ($this->candidates as $key => $candidate) {
			$list[$key]  = $candidate['_name'];
			if ($this->settings['showAddresses']) {
				$list[$key] .= '<br /><span class="small comment">' . htmlspecialchars ($candidate['address']) . '</span>';
			}
		}
		
		# Construct the HTML
		$html  = '';
		// $html .= "<h3>Candidates standing for " . $this->areaName ($this->area) . ' ' . $election['areaType'] . '</h3>';
		// $html .= "\n<p>The following candidates (listed in surname order) are standing for " . $this->areaName ($this->area) . ' ' . $election['areaType'] . '</p>';
		$html .= application::htmlUl ($list, 0, 'nobullet' . ($this->settings['showAddresses'] ? ' spaced' : ''));
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to show questions
	private function showQuestions ($limitToArea = false /* will be false if listing all questions across all elections */)
	{
		# Start the HTML
		$html = '';
		
		# Get the data
		$electionId = ($limitToArea ? $this->election['id'] : false);
		if (!$data = $this->getQuestions ($electionId, $limitToArea)) {
			$areaName = $this->areas[$limitToArea]['_name'];
			$html .= "\n\n<h3 class=\"area\" id=\"{$areaName}\">Questions for {$areaName} {$this->election['areaType']} candidates</h3>";
			return $html .= "\n<p>There are no questions assigned for this {$this->election['areaType']} at present.</p>";
		}
		
		# Order by most recent first if getting all
		if (!$limitToArea && !$this->election) {
			krsort ($data);
		}
		
		# Regroup by area
		$data = ((!$limitToArea && !$this->election) ? array ('_all' => $data) : application::regroup ($data, 'areaId', $removeGroupColumn = false));
		
		# Get responses from candidates if there are candidates
		$responses = false;
		if ($this->candidates) {
			$areaSurveyIds = array_keys ($data[$limitToArea]);
			$responses = $this->getResponses ($areaSurveyIds);
		}
		
		# Get all the question index numbers in use in this election across all wards, as shown on the all questions page - i.e. the public numbers 1,2,3.. (as shown on the question index page) rather than the internal IDs
		#!# This is inefficient as it runs getQuestions a second time but is not always needed
		$questionNumbersAcrossElection = $this->getQuestionsForElection ($electionId, true);
		
		# Loop through each grouping
		$questionsHtml = '';
		$areasHtml = array ();
		foreach ($data as $area => $questions) {
			
			# Miss out if no candidates in an area
			#!# Need to fix for /elections/%election/questions.html where area has no candidates, e.g. 2007may:girton
			// if ($limitToArea && !isSet ($this->areas[$area])) {continue;}
			
			# Count the questions
			$totalQuestions = count ($questions);
			
			# Show the area heading
			if ($this->election && $area != '_all') {
				#!# Area may not exist if no candidates
				$areaName = $this->areas[$area]['_name'];
				$questionsHtml .= "\n\n<h3 class=\"area\" id=\"{$area}\">Questions for {$areaName} {$this->election['areaType']} candidates ({$totalQuestions} questions)</h3>";
				$areasHtml[] = "<a href=\"#{$area}\">{$areaName} {$this->election['areaType']}</a> ({$totalQuestions} questions)";
			}
			
			# Construct the HTML
			$i = 0;
			$questionsJumplist = array ();
			$list = array ();
			foreach ($questions as $question) {
				$i++;
				
				# Add to jumplist; when listing all, this shows only most recent, to avoid a massive list covering the page after many years
				if ($limitToArea || (!$limitToArea && ($i <= $this->settings['mostRecent']))) {
					$number = ($limitToArea ? $i : $question['id']);	// Index when showing an area-specific questionnaire, but the actual internal ID when showing everything
					$link = 'question' . $number;
					$questionsJumplist[] = "<strong><a href=\"#{$link}\">&nbsp;" . ($limitToArea ? $number : ($i != $this->settings['mostRecent'] ?  "#{$number}" : 'More&hellip;')) . '&nbsp;</a></strong>';
				}
				
				# Add to list
				$list[$i]  = "\n\n<h4 class=\"question\" id=\"{$link}\"><a href=\"#{$link}\">#</a> " . ($limitToArea ? 'Question ' : 'Question ID #') . $number . '</h4>';	// In all-listing mode (i.e. admins-only), show the IDs
				$list[$i] .= $this->questionBox ($question);
				
				# Show responses
				$questionNumberAcrossElection = $questionNumbersAcrossElection[$question['questionId']];
				$list[$i] .= $this->responsesBlock ($question, $this->candidates, $responses, false, $questionNumberAcrossElection);
			}
			
			# Determine the next available question number, if on the all questions list
			$nextAvailableHtml = '';
			if (!$limitToArea) {
				$nextAvailableHtml = "\n<p>NB The next available question number is: <strong>" . $this->getNextAvailableQuestionNumber () . '</strong>. This is useful if drafting a new spreadsheet of questions.</p>';
			}
			
			# Construct the HTML
			$questionsHtml .= "\n\n<p class=\"clearall\">Jump to question: " . implode (' ', $questionsJumplist) . '</p>';
			$questionsHtml .= $nextAvailableHtml;
			$questionsHtml .= implode ($list);
		}
		
		# Add the questions HTML
		if (!$limitToArea) {
			$html .= "\n<p>Below is a list of " . ($this->election ? 'the questions allocated to each ' . $this->election['areaType'] : 'all questions available in the database, listed most recent first') . ":</p>";
			if ($this->election) {
				$html .= application::htmlUl ($areasHtml);
				if ($this->userIsAdministrator) {
					$html .= "\n<p>As an administrator you can also return to the <a href=\"{$this->baseUrl}/admin/allquestions.html\">list of all questions available in the database</a>.</p>";
				}
			}
		}
		$html .= $questionsHtml;
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to get the next available question number
	private function getNextAvailableQuestionNumber ()
	{
		$query = "SELECT MAX(id) + 1 AS nextId FROM {$this->settings['tablePrefix']}questions";
		return $nextId = $this->databaseConnection->getOneField ($query, 'nextId');
	}
	
	
	# Function to create the block of candidate responses to a question
	private function responsesBlock ($question, $candidates, $responses, $crossAreaMode = false, $questionNumberAcrossElection = false)
	{
		# Start the HTML
		$html = '';
		
		/*
		application::dumpData ($question);
		application::dumpData ($candidates);
		application::dumpData ($responses);
		*/
		
		# Determine the number of areas in this election
		$totalAreasExisting = count ($this->areas);
		
		# Add a link to all responses in other areas if required
		if ($questionNumberAcrossElection) {
			if ($totalAreasExisting > 1) {
				$html .= "\n<p class=\"allresponseslink\"><a href=\"{$this->baseUrl}/{$this->election['id']}/questions/{$questionNumberAcrossElection}/\">Responses to this question from all {$this->election['areaTypePlural']}&hellip;</a></p>";
			}
		}
		
		# End if no candidates
		if (!$candidates) {return $html;}
		
		# If the results are not yet visible, end at this point
		if (!$this->election['resultsVisible'] && !$this->userIsAdministrator) {
			return $html .= "\n<p><em>Candidates' responses are not yet visible. Please check back here from {$this->election['visibilityDateTime']}.</em></p>";
		}
		
		# State the areas and the number of responses, if in cross-area mode
		if ($crossAreaMode) {
			$areaNamesLinked = array ();
			foreach ($crossAreaMode as $area) {
				$areaName = $this->areas[$area]['areaName'];
				$areaNamesLinked[$areaName] = "<a href=\"{$this->baseUrl}/{$this->election['id']}/{$area}/\">{$this->areas[$area]['_name']}</a>";
			}
			ksort ($areaNamesLinked);
			$totalAreasAsked = count ($areaNamesLinked);
			if ($totalAreasExisting == 1) {
				$html .= "\n<p>We asked this question:</p>";
			} else {
				$everyAreaAsked = ($totalAreasExisting == $totalAreasAsked);
				$html .= "\n<p>We asked this question " . ($everyAreaAsked ? "in <strong>all {$totalAreasAsked} {$this->election['areaTypePlural']}</strong>, namely: " : ($totalAreasAsked > 1 ? "in these <strong>{$totalAreasAsked} {$this->election['areaTypePlural']}</strong>: " : 'only in ')) . implode (', ', $areaNamesLinked) . '.</p>';
			}
			$totalCandidates = count ($candidates);
			$totalResponses = count ($responses);
			$percentageReplied = round (($totalResponses / $totalCandidates) * 100);
			$html .= "\n<p><strong>{$totalResponses}</strong> of the <strong>{$totalCandidates}</strong> candidates (<strong>{$percentageReplied}%</strong>) who were asked this question responded as below.</p>";
		}
		
		# Determine if this is a election with more than one person standing per party, and if so, assign a suffix (e.g. _1, _2, _3) for each such candidate
		$affiliationSuffixes = array ();	// Indexed by party, and then within that by candidate => suffix index
		$multiPersonAreas = false;
		foreach ($candidates as $candidateKey => $candidate) {
			$affiliation = $candidate['affiliationId'];		// E.g. 'labour'
			
			# If an entry is already present, drop the flag that at least one area is multi-person
			if (isSet ($affiliationSuffixes[$affiliation])) {
				$multiPersonAreas = true;
			}
			
			# Add one to the count so far
			$affiliationSuffixes[$affiliation][$candidateKey] = (isSet ($affiliationSuffixes[$affiliation]) ? count ($affiliationSuffixes[$affiliation]) : 0) + 1;
		}
		
		# Loop through each candidate (so that all are listed, irrespective of whether they have responded)
		$responsesList = array ();
		$showsElected = 0;
		foreach ($candidates as $candidateKey => $candidate) {
			
			# If this is a multi-person area election, determine the suffix to add to the unique ID below
			$multiPersonAreasIdSuffix = '';
			if ($multiPersonAreas) {
				$multiPersonAreasIdSuffix = '_' . $affiliationSuffixes[$candidate['affiliationId']][$candidateKey];
			}
			
			# Set a unique ID for use in the table, including the flag for whether the candidate is elected
			$id = $candidate['areaId'] . '_' . $candidate['affiliationId'] . $multiPersonAreasIdSuffix . ($candidate['elected'] ? ' elected' : '');
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
			if ($crossAreaMode) {	// Here we don't know the survey ID, but there is always one entry if it exists at all; so we check for existence then retrieve the surveyId
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
		if ($crossAreaMode && $showsElected) {
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
		return "\n<div class=\"graybox\"><p>" . nl2br (htmlspecialchars ($question['question'])) . '</p>' . $this->formatLinks ($question['links']) . '</div>';
	}
	
	
	# Function to get all questions being asked
	#!# This function has become unwieldy, as it is actually getting questions OR surveys; it should be split into separate functions
	#!# Variable name groupByQuestionId is an unclear purpose - really this is saying that we want unique questions rather than surveys
	private function getQuestions ($election = false, $area = false, $groupByQuestionId = false)
	{
		# If there is not an election specified, i.e. top-level listing of all questions, retrieve all available questions
		if (!$election) {
			$query = "SELECT *, id AS questionId FROM {$this->settings['tablePrefix']}questions;";
			$preparedStatementValues = array ();
			
		# Otherwise get surveys by area
		} else {
			$query = "SELECT
					{$this->settings['tablePrefix']}surveys.id as id,
					{$this->settings['tablePrefix']}surveys.areaId,
					{$this->settings['tablePrefix']}questions.id AS questionId,
					{$this->settings['tablePrefix']}questions.question,
					{$this->settings['tablePrefix']}questions.links,
					{$this->settings['tablePrefix']}questions.highlight,
					{$this->settings['tablePrefix']}areas.prefix,
					{$this->settings['tablePrefix']}areas.areaName
				FROM {$this->settings['tablePrefix']}surveys
				LEFT OUTER JOIN {$this->settings['tablePrefix']}areas ON {$this->settings['tablePrefix']}surveys.areaId = {$this->settings['tablePrefix']}areas.id
				LEFT OUTER JOIN {$this->settings['tablePrefix']}questions ON {$this->settings['tablePrefix']}surveys.question = {$this->settings['tablePrefix']}questions.id
				WHERE election = :election
				" . ($area ? "AND {$this->settings['tablePrefix']}surveys.areaId = :areaId" : '') . "
				ORDER BY " . ($groupByQuestionId ? 'questionId' : "{$this->settings['tablePrefix']}surveys.areaId,ordering,{$this->settings['tablePrefix']}surveys.id") . "
			;";
			$preparedStatementValues = array ('election' => $election);
			if ($area) {$preparedStatementValues['areaId'] = $area;}
		}
		$data = $this->databaseConnection->getData ($query, "{$this->settings['database']}.{$this->settings['tablePrefix']}areas", true, $preparedStatementValues);
		
		# Group by question ID if required; this cannot be done within the main query due to GROUP BY limitations
		#!# This block is a working but somewhat unsatisfactory workaround for not easily being able to run a modified version of the query above, because 'id' will not be present causing the database library to throw errors
		if ($groupByQuestionId) {
			$uniqueQuestions = array ();
			$onlyFields = array ('questionId', 'question', 'links', 'highlight');
			foreach ($data as $surveyQuestion) {
				$questionId =  $surveyQuestion['questionId'];
				$uniqueQuestions[$questionId] = application::arrayFields ($surveyQuestion, $onlyFields);
				// Subsequent iterations will overwrite but the values will be the same
			}
			$data = $uniqueQuestions;
		}
		
		# Return the data
		return $data;
	}
	
	
	# Function to format links
	private function formatLinks ($links, $letterMode = false)
	{
		# End if no links
		$links = trim ((string) $links);
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
				
				# Perform a match
				if (preg_match ('@^https?://' . $_SERVER['SERVER_NAME'] . '(.*)@', $link, $matches)) {
					$link = ($letterMode ? $_SERVER['SERVER_NAME'] : '') . $matches[1];
					#!# This has hard-coded paths, and should be changed to be a fallback
					if (preg_match ('@/newsletters/([0-9]+)/article([0-9]+).html$@', $link, $newsletterMatches)) {
						$settingsFile = "newsletters/{$newsletterMatches[1]}/settings.html";
						if (is_readable ($_SERVER['DOCUMENT_ROOT'] . '/' . $settingsFile)) {
							include ($_SERVER['DOCUMENT_ROOT'] . '/' . $settingsFile);	// NOT include_once - as that would cache a previously-loaded settings file
							$index = $newsletterMatches[2] - 1;
							if (isSet ($newsletterSettings) && isSet ($newsletterSettings['articles']) && isSet ($newsletterSettings['articles'][$index])) {
								$title = "Newsletter {$newsletterMatches[1]}: {$newsletterSettings['articles'][$index]}";
							}
						}
					} else {
						
						# Extract the title
						$filename = $_SERVER['DOCUMENT_ROOT'] . str_replace ($_SERVER['SERVER_NAME'], '', $link) . (substr ($link, -1) == '/' ? 'index.html' : '');
						if (is_readable ($filename)) {
							$file = file_get_contents ($filename);
							if ($tryTitle = application::getTitleFromFileContents ($file, 200)) {
								$title = htmlspecialchars ($tryTitle);
							}
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
	
	
	# Admin function providing a simple link through to the main listings
	public function viewincoming ()
	{
		# Start the HTML
		$html = '';
		
		# Get all elections, including forthcoming
		$this->elections = $this->getElections (true);
		$this->election = ((isSet ($_GET['election']) && isSet ($this->elections[$_GET['election']])) ? $this->elections[$_GET['election']] : false);
		
		# Introduce this area
		$html .= "\n<p>As an administrator, you can view the incoming responses at any time.</p>";
		$html .= "\n<p>The links below will take you to the public pages, but as a logged-in administrator you will see the responses before members of the public can see them.</p>";
		
		# Select the list of elections
		$html .= "\n<p>Please select which election:</p>";
		$html .= $this->listElections ($this->elections, true, false, 'respondents.html');
		
		# Return the HTML
		return $html;
	}
	
	
	# Candidate response submission
	public function submit ($showIds = false)
	{
		# Start the HTML
		$html = '';
		
		# Get the list of all areas currently being surveyed
		if (!$areas = $this->getActiveAreas ()) {
			$html .= "\n<p>No areas are currently being surveyed.</p>";
			return $html;
		}
		
		# Determine whether a (validly-structured) second-stage submission has been made
		$secondStagePosted = (isSet ($_POST['questions']) && is_array ($_POST['questions']) && isSet ($_POST['questions']['verification']) && is_array ($_POST['questions']['verification']) && isSet ($_POST['questions']['verification']['number']) && isSet ($_POST['questions']['verification']['area']));
		
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
			$form->heading ('p', "<strong>Welcome</strong>, candidate. <strong>Thank you</strong> for responding to our survey.</p>\n<p>We've done this survey online so that constituents - including our {$this->settings['organisationConstituentsType']} - in each area can see what each candidate thinks. Voters can then take these views into account alongside other issues of concern to them. The questions we've posed are relevant/specific to each area.");
			$form->heading ('p', '<br />Please firstly enter the verification ID given in the letter/e-mail you received, which ensures the security of your response.');
			$form->input (array (
				'name'			=> 'number',
				'title'			=> 'Verification ID',
				'required'		=> true,
				'maxlength'		=> 6,
				'size'			=> 20,
				'regexp'		=> '^([a-f0-9]{6})$',
			));
			$form->select (array (
				'name'			=> 'area',
				'title'			=> 'Area',
				'required'		=> 1,
				'values'		=> $areas,
			));
			
			# Process the form or end
			if (!$result = $form->process ($html)) {
				return $html;
			}
		}
		
		# Determine the number and area to be checked
		$number = ($secondStagePosted ? $_POST['questions']['verification']['number'] : $result['number']);
		$area = ($secondStagePosted ? $_POST['questions']['verification']['area'] : $result['area']);
		
		# Confirm the details
		#!# Use getUnfinalised to improve the UI here
		if (!$candidate = $this->verifyCandidate ($number, $area)) {
			$html .= "\n<p>The verification/area pair you submitted does not seem to be correct. Please check the e-mail/letter we sent you and <a href=\"{$this->baseUrl}/submit/\">try again</a>.</p>";
			return $html;
		}
		
		# Retrieve and cache the election data
		$this->election = $this->elections[$candidate['electionId']];
		
		# Create a shortcut to the area name
		$areaName = $areas[$candidate['areaId']];
		
		# Start the page with a new heading
		$html  = "\n\n<h2 class=\"area\" id=\"{$areaName}\">Questions for {$areaName} {$this->election['areaType']} candidates</h2>";
		
		# End if election is over
		if (!$this->election['active']) {
			if ($this->userIsAdministrator) {
				$html .= "\n" . '<p class="warning">Note: You can only see the form below as you are logged in as an administrator, despite the election not currently being open.</p>';
			} else {
				return $html .= "<p>The election is now over, so submissions cannot be made any longer.</p>";
			}
		}
		
		# Show the candidate's data
		$table['Election'] = $this->election['name'];
		$table['Election date'] = $this->election['polling date'];
		$table[ ucfirst ($this->election['areaType']) ] = $areaName;
		$table['Name'] = $candidate['name'];
		$table['Affiliation'] = "<span style=\"color: #{$candidate['colour']};\">" . htmlspecialchars ($candidate['affiliation']) . '</span>';
		$html .= application::htmlTableKeyed ($table, array (), true, 'lines', $allowHtml = true);
		
		# Get the questions for this candidate's area
		if (!$questions = $this->getQuestions ($candidate['electionId'], $candidate['areaId'])) {
			return $html .= "\n<p>There are no questions assigned for this {$this->election['areaType']} at present.</p>";
		}
		
		# Get the responses for this candidate's questions
		if ($responses = $this->getResponses (array_keys ($questions), $candidate['id'])) {
			$responses = $responses[$candidate['id']];
		}
		
		# Prevent updates after the results are visible
		if ($responses && $this->election['resultsVisible']) {
			return $html .= "<p>You have previously submitted a set of responses, which is now <a href=\"{$this->baseUrl}/{$this->election['id']}/{$candidate['areaId']}/\">shown online</a>, so submissions cannot be made any longer. Thank you for taking part.</p>";
		}
		
		# Build up the template
		$total = count ($questions);
		$i = 0;
		$template  = '<p>There ' . ($total == 1 ? 'is 1 question' : "are {$total} questions") . " for this {$this->election['areaType']} on which we would invite your response.<br /><strong>Please kindly enter your responses in the boxes below and click the 'submit' button at the end.</strong></p>";
		if ($responses) {$template .= "<p>You are able to update your previous answers below, before they become visible online at {$this->election['visibilityDateTime']}.</p>";}
		$template .= "<p>Your answers will not be visible on this website until {$this->election['visibilityDateTime']}.</p>";
		$template .= '<p>{[[PROBLEMS]]}</p>';
		foreach ($questions as $key => $question) {
			$i++;
			$template .= "\n\n<h4 class=\"question\"> Question {$i}" . ($showIds ? " &nbsp;[survey-id#{$key}]" : '') . '</h4>';
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
			'unsavedDataProtection' => true,
		));
		$i = 0;
		foreach ($questions as $key => $question) {
			$i++;
			$fieldname = "question{$key}";
			$fields[] = $fieldname;
			$question['question'] = nl2br (htmlspecialchars ($question['question']));
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
				'area' => ($secondStagePosted ? htmlspecialchars ($_POST['questions']['verification']['area']) : $result['area']),
			),
		));
		
		# Set an e-mail backup record
		$form->setOutputEmail ($this->settings['recipient'], $this->settings['webmaster'], $subjectTitle = str_replace ('&amp;', '&', "Election submission - {$areaName} - {$candidate['name']} - {$candidate['affiliation']}") . ($responses ? ' (update)' : ''), $chosenElementSuffix = NULL, $replyToField = NULL, $displayUnsubmitted = true);
		
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
		#!# Change to insertMany/updateMany
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
			$html .= "\n<p><img src=\"{$this->baseUrl}/images/icons/tick.png\" class=\"icon\" /> <strong>Thank you for {$action} your responses.</strong> They are now <a href=\"{$this->baseUrl}/{$this->election['id']}/{$candidate['areaId']}/\">shown online</a>, along with those of other candidates.</p>";
		} else {
			$html .= "\n<p><img src=\"{$this->baseUrl}/images/icons/tick.png\" class=\"icon\" /> <strong>Thank you for {$action} your responses.</strong> They will be <a href=\"{$this->baseUrl}/{$this->election['id']}/{$candidate['areaId']}/\">shown online</a>, along with those of other candidates, at {$this->election['visibilityDateTime']}.</p>";
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
	
	
	# Function to get active areas across all current elections
	private function getActiveAreas ()
	{
		# Get data
		$query = "SELECT
				{$this->settings['tablePrefix']}areas.id,
				{$this->settings['tablePrefix']}areas.prefix,
				{$this->settings['tablePrefix']}areas.areaName
			FROM {$this->settings['tablePrefix']}candidates
			LEFT OUTER JOIN {$this->settings['tablePrefix']}elections ON {$this->settings['tablePrefix']}candidates.election = {$this->settings['tablePrefix']}elections.id
			LEFT OUTER JOIN {$this->settings['tablePrefix']}areas ON {$this->settings['tablePrefix']}candidates.areaId = {$this->settings['tablePrefix']}areas.id
			WHERE
				{$this->settings['tablePrefix']}elections.endDate >= CURDATE()
			ORDER BY {$this->settings['tablePrefix']}areas.areaName
		;";
		if (!$data = $this->databaseConnection->getData ($query, "{$this->settings['database']}.{$this->settings['tablePrefix']}areas")) {
			return false;
		}
		
		# Rearrange as key=>value, assembling the area names from the prefix and name
		$areas = array ();
		foreach ($data as $area) {
			$areas[$area['id']] = $this->areaName ($area, $convertEntities = false);
		}
		
		# Return the data
		return $areas;
	}
	
	
	# Function to verify the candidate and return their details
	private function verifyCandidate ($number, $area)
	{
		# Get the data
		$query = "SELECT
				{$this->settings['tablePrefix']}candidates.*,
				areaId,
				{$this->settings['tablePrefix']}affiliations.id as affiliationId,
				{$this->settings['tablePrefix']}affiliations.name as affiliation,
				{$this->settings['tablePrefix']}affiliations.colour,
				election as electionId,
				CONCAT(forename,' ',UPPER(surname)) as name
			FROM {$this->settings['tablePrefix']}candidates
			LEFT OUTER JOIN {$this->settings['tablePrefix']}affiliations ON {$this->settings['tablePrefix']}candidates.affiliation = {$this->settings['tablePrefix']}affiliations.id
			WHERE
				    verification = :verification
				AND areaId = :areaId
		;";
		$preparedStatementValues = array (
			'verification'	=> $number,
			'areaId'	=> $area,
		);
		if (!$data = $this->databaseConnection->getOne ($query, false, true, $preparedStatementValues)) {
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
			header ('HTTP/1.1 404 Not Found');
			$html = '<p>There is no such election. Please check the URL and try again.</p>';
			return $html;
		}
		
		# Title
		$html  = "\n<h2>List of respondents" . ($this->election['active'] ? ' (so far)' : '') .  '</h2>';
		
		# Ensure there are candidates loaded
		if (!$allCandidates = $this->getCandidates ($this->election['id'], true)) {
			$html .= "\n<p>The candidate list has not yet been loaded for this election. Please check back later.</p>";
			return $html;
		}
		
		# Get the data
		$query = "SELECT
				{$this->settings['tablePrefix']}candidates.id,
				CONCAT({$this->settings['tablePrefix']}candidates.forename,' ',UPPER({$this->settings['tablePrefix']}candidates.surname)) as name,
				{$this->settings['tablePrefix']}areas.id as areaId,
				{$this->settings['tablePrefix']}areas.prefix,
				{$this->settings['tablePrefix']}areas.areaName,
				{$this->settings['tablePrefix']}areas.districtCouncil,
				{$this->settings['tablePrefix']}affiliations.name as affiliation,
				{$this->settings['tablePrefix']}affiliations.colour
			FROM {$this->settings['tablePrefix']}responses
			LEFT OUTER JOIN {$this->settings['tablePrefix']}candidates ON {$this->settings['tablePrefix']}responses.candidate = {$this->settings['tablePrefix']}candidates.id
			LEFT OUTER JOIN {$this->settings['tablePrefix']}areas ON {$this->settings['tablePrefix']}candidates.areaId = {$this->settings['tablePrefix']}areas.id
			LEFT OUTER JOIN {$this->settings['tablePrefix']}affiliations ON {$this->settings['tablePrefix']}candidates.affiliation = {$this->settings['tablePrefix']}affiliations.id
			WHERE
				election = :election
			ORDER BY areaId,surname
		;";
		$preparedStatementValues = array ('election' => $this->election['id']);
		$respondents = $this->databaseConnection->getData ($query, "{$this->settings['database']}.{$this->settings['tablePrefix']}responses", true, $preparedStatementValues);
		
		# Count the responses
		$total = count ($respondents);
		
		# Regroup the data by area
		$areas = application::regroup ($respondents, 'areaId', false);
		
		# Determine the total number of candidates standing and the response rate
		$totalCandidates = count ($allCandidates);
		$percentageReplied = round (($total / $totalCandidates) * 100);
		
		# Construct a table of response rates by district Council area
		$responseRatesByDistrictTable = $this->responseRatesByAspectTable ($allCandidates, $respondents, 'districtCouncil', 'district area');
		$responseRatesByDistrictTable = str_replace (' Council', '', $responseRatesByDistrictTable);	// These aren't always actual councils, e.g. County elections incorporates the areas but it is misleading to show the council name itself
		
		# Construct a table of response rates by affiliation (party)
		$colours = array ();
		foreach ($allCandidates as $candidate) {
			$colours[$candidate['affiliation']] = $candidate['colour'];
		}
		$responseRatesByPartyTable = $this->responseRatesByAspectTable ($allCandidates, $respondents, 'affiliation', 'affiliation (party)', $colours, true);
		
		# Construct the HTML
		$html .= "\n<p>The following is an index to all candidates " . ($responseRatesByDistrictTable ? '' : "({$total}, out of {$totalCandidates} standing, i.e. {$percentageReplied}%)") . " who have submitted public responses. Click on the {$this->election['areaType']} name to see them.</p>";
		$html .= $responseRatesByDistrictTable;
		$html .= $responseRatesByPartyTable;
		$html .= "\n<p><em>This list is ordered by {$this->election['areaType']} and then surname.</em></p>";
		foreach ($this->areas as $area => $attributes) {
			$html .= "<h4><a href=\"{$this->baseUrl}/{$this->election['id']}/{$area}/\">{$this->areas[$area]['_name']} <span>[view responses]</span></a>:</h4>";
			if (!isSet ($areas[$area])) {
				$html .= "\n<p class=\"noresponse faded\"><em>No candidate for {$this->areas[$area]['_name']} has yet submitted a response.</em></p>";
			} else {
				$candidates = $areas[$area];
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
			header ('HTTP/1.1 404 Not Found');
			$html .= '<p>There is no such election. Please check the URL and try again.</p>';
			return $html;
		}
		
		# End if no Cabinet members restanding in this election
		if (!$cabinetRestanding = $this->getCandidates ($this->election['id'], false, false, false, $requireCabinetRestanding = true)) {
			header ('HTTP/1.1 404 Not Found');
			$html .= "\n<p>There are no Cabinet members in {$this->election['areaTypePlural']} we are surveying restanding in this election. Please check the URL and try again.</p>";
			return $html;
		}
		
		# Get the responses
		$candidateIds = array_keys ($cabinetRestanding);
		$responses = $this->getResponses (false, $candidateIds);
		
		# Create a table
		$cabinetMembers = array ();
		foreach ($cabinetRestanding as $candidateId => $candidate) {
			$surveyLink = "{$this->baseUrl}/{$this->election['id']}/{$candidate['areaId']}/";
			$cabinetMembers[] = array (
				'Candidate' => str_replace (' &nbsp;(', '<br />(', $candidate['_name']),
				'Responded?' => (isSet ($responses[$candidateId]) ? "<a href=\"{$surveyLink}\"><strong>Yes - view responses</strong></a>" : '<span class="warning"><strong>No</strong>, the candidate ' . ($this->election['active'] ? 'has not (yet) responded' : 'did not respond') . '</span>'),
				'Post' => $candidate['cabinetRestanding'],
				ucfirst ($this->election['areaType']) => "<a href=\"{$surveyLink}\">" . $candidate['areaName'] . '</a>',
			);
		}
		
		# Compile the HTML
		$html .= "\n<p>The <strong>Cabinet</strong> is the Executive of the Council, formed of members of the political party in power. They implement and drive the Council's policy. As such, their views arguably have greater effect than any other Councillors.</p>";
		$html .= "\n<p>The listing below shows all the Cabinet members in {$this->election['areaTypePlural']} we are surveying who are restanding in this election, and whether they have responded to our survey or not.</p>";
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
			
			# Exit if an aspect name is missing
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
		$html = "\n<p class=\"right\"><a href=\"{$this->baseUrl}/\">Public home</a> | <a href=\"{$this->baseUrl}/{$this->actions['logoutinternal']['url']}\">Logout</a></p>";
		$html .= "\n<h2>Administrative functions" . ($this->election ? ' for this election' : '') . '</h2>';
		
		# Add introduction
		$html .= "\n<p>To set up an election survey, work through the numbered sections below, adding the relevant information via the links provided.</p>";
		$html .= "\n<p>This <a href=\"{$this->baseUrl}/admin/electionstemplate.xlsx\">elections survey template spreadsheet</a> will help you gather all the required data together, which you can then copy into the forms below.</p>";
		$html .= "\n<p><em>This section is accessible only to Administrators.</em></p>";
		
		# Define the groups
		$groups = array (
			'system' => array (
				'title' => 'Overall system configuration',
				'icon' => 'cog',
				'introduction' => 'This section sets the overall system configuration that applies to all elections, e.g. main page text, e-mail settings and labelling.',
			),
			'election' => array (
				'title' => '1. Overall election details',
				'icon' => 'application_view_list',
				'introduction' => 'To run a survey for an election, you need to create settings for that election, defining its name, date, and other details.',
			),
			'areas' => array (
				'title' => '2. Geographical areas',
				'icon' => 'map',
				'introduction' => 'In order to create a survey, you need to ensure that each geographical area exists in the database. Once added, these areas are then available for any survey you create for any election.',
			),
			'affilations' => array (
				'title' => '3. Party details',
				'icon' => 'medal_bronze_3',
				'introduction' => 'The party affiliation of all the candidates needs to exist in the database. Once added, these party names are available for any survey you create for any election.',
			),
			'questions' => array (
				'title' => '4. Questions available for surveys',
				'icon' => 'help',
				'introduction' => 'In order to create surveys for each geographical area, you must add each available question to the database. Once added, a question can be used for any surveys, e.g. across multiple geographical areas or in future elections.',
			),
			'surveys' => array (
				'title' => '5. Surveys for each area',
				'icon' => 'script',
				'introduction' => 'Having defined an election, ensured that the geographical areas and party names are in the database, and that the questions are available, you can now create the survey for each area.',
			),
			'candidates' => array (
				'title' => '6. Candidates standing in each area',
				'icon' => 'group',
				'introduction' => 'The last part of the data to be added is the candidates standing in each area.',
			),
			'issue' => array (
				'title' => '7. Issue/manage the surveys',
				'icon' => 'email_go',
				'introduction' => 'At the start of the election, you can either e-mail or print out letters for each candidate, inviting them to contribute their answers.',
			),
			'during' => array (
				'title' => 'During the election',
				'icon' => 'clock',
				'introduction' => 'Candidates can submit responses at any time from the opening of the survey until the date of the election; they can also edit an existing response until the responses are made live. You can also send reminder e-mails, or reissue an e-mail. You can also check what a candidate sees when they visit the submission form.',
			),
			'postelection' => array (
				'title' => 'After the election',
				'icon' => 'award_star_gold_3',
				'introduction' => 'After the election, you can specify the winning candidates, so that their answers are shown highlighted.',
			),
		);
		
		# Construct the page
		foreach ($groups as $groupId => $group) {
			$html .= "\n<div class=\"graybox\">";
			$html .= "\n<h3><img src=\"{$this->baseUrl}/images/icons/{$group['icon']}.png\" class=\"icon\" /> " . htmlspecialchars ($group['title']) . '</h3>';
			$html .= "\n\t<p>" . htmlspecialchars ($group['introduction']) . '</p>';
			$html .= "\n\t<ul>";
			foreach ($this->actions as $actionId => $action) {
				if (isSet ($action['admingroup']) && (is_string ($action['admingroup']) ? $action['admingroup'] == $groupId : in_array ($groupId, $action['admingroup']))) {
					if ($this->election && isSet ($action['election']) && !$action['election']) {continue;}	// Skip if explicitly false
					if (method_exists ($this, $actionId)) {
						$url = "{$this->baseUrl}/" . ((isSet ($action['election']) && $this->election) ? str_replace ('admin/', "{$this->election['id']}/", $action['url']) : $action['url']);
						$html .= "\n\t\t<li><a href=\"{$url}\">" . htmlspecialchars ($action['description']) . '</a></li>';
					} else {
						$html .= "\n\t\t<li><span class=\"comment\">" . htmlspecialchars ($action['description']) . '</span> (screen not yet created)</li>';
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
			header ('HTTP/1.1 404 Not Found');
			return $html = '<p>This listing is not election-specific. Please check the URL and try again.</p>';
		}
		
		# List the questions
		$html .= "\n<p class=\"alignright\"><a href=\"{$this->baseUrl}/admin/addquestions.html\">+ Add a question</a></p>";
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
			$html .= "\n<p><img src=\"{$this->baseUrl}/images/icons/tick.png\" class=\"icon\" /> The <a href=\"{$this->baseUrl}/{$result['id']}/\">election</a> has been added.</p>";
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
			$html .= $this->listElections ($this->elections, true, false, __FUNCTION__ . '.html');
			return $html;
		}
		
		# Process the form
		if ($result = $this->electionForm ($this->election, array (), $html /* returned by reference */)) {
			
			# Update the election
			$this->databaseConnection->update ($this->settings['database'], "{$this->settings['tablePrefix']}elections", $result, array ('id' => $this->election['id']));
			
			# Confirm success
			$html .= "\n<p><img src=\"{$this->baseUrl}/images/icons/tick.png\" class=\"icon\" /> The <a href=\"{$this->baseUrl}/{$result['id']}/editelection.html\">settings</a> for this <a href=\"{$this->baseUrl}/{$result['id']}/\">election</a> have been updated.</p>";
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
			'richtextEditorBasePath' => $this->baseUrl . '/js/libraries/ckeditor/',
			'richtextEditorFileBrowser' => false,
			'autofocus' => true,
		));
		$form->dataBinding (array (
			'database'	=> $this->settings['database'],
			'table'		=> "{$this->settings['tablePrefix']}elections",
			'data'		=> $data,
			'intelligence' => true,
			'attributes' => array (
				'id' => array ('editable' => (!$data), 'current' => $currentIds, 'regexp' => '^[a-z0-9]+$', 'placeholder' => 'E.g. ' . date ('Y') . 'election', ),
				'name' => array ('placeholder' => 'E.g. Elections to Placeford Council, ' . date ('Y')),
				'startDate' => array ('description' => 'This is the date when candidates can start to enter their responses, assuming that questions, areas, etc., are all loaded. This must not be before the start date of the election, to avoid accusations of unfairness from undeclared candidates.'),
				'resultsDate' => array ('description' => 'This is the date when responses from candidates will become visible to the general public. Admins can log in and see responses before this date. Candidates can edit any existing response they have made until this date.'),
				'endDate' => array ('description' => 'This is the date of the election, and candidates will not be able to edit their responses after this date.'),
				'description' => array ('placeholder' => 'E.g. Elections to Placeford Council in May ' . date ('Y')),
				'letterheadHtml' => array ('editorToolbarSet' => 'BasicImage', 'width' => '600px'),
				'organisationIntroductionHtml' => array ('editorToolbarSet' => 'BasicNoLinks', 'width' => '600px'),
			),
		));
		#!# Need to add constraints to ensure date ordering is correct
		return $result = $form->process ($html);
	}
	
	
	# Function to add an area
	public function addarea ()
	{
		# Start the HTML
		$html = '';
		
		# Add introduction
		$html .= "\n<p>Here you can add an area to the database.</p>";
		$html .= "\n<p>You should <strong>only</strong> add a new area if it is not already listed <a href=\"#existing\">below</a>.</p>";
		
		# Get current IDs
		$currentIds = $this->databaseConnection->selectPairs ($this->settings['database'], "{$this->settings['tablePrefix']}areas", array (), array ('id'), true, $orderBy = 'id');
		
		# Create a new form
		require_once ('ultimateForm.php');
		$form = new form (array (
			'databaseConnection' => $this->databaseConnection,
			'displayRestrictions' => false,
			'picker' => true,
		));
		$form->dataBinding (array (
			'database'	=> $this->settings['database'],
			'table'		=> "{$this->settings['tablePrefix']}areas",
			'attributes' => array (
				'id' => array ('current' => $currentIds, ),
			),
		));
		if ($result = $form->process ($html)) {
			
			# Insert the area
			if (!$this->databaseConnection->insert ($this->settings['database'], "{$this->settings['tablePrefix']}areas", $result)) {
				$html = "\n<p><img src=\"{$this->baseUrl}/images/icons/cross.png\" class=\"icon\" /> An error occurred adding the area.</p>";
				return $html;
			}
			
			# Confirm success
			$html  = "\n<p><img src=\"{$this->baseUrl}/images/icons/tick.png\" class=\"icon\" /> The area has been added.</p>";
			$html .= "\n<p><a href=\"{$this->baseUrl}/admin/" . __FUNCTION__ . '.html">Add another?</a></p>';
		}
		
		# Show existing areas
		$html .= "\n<h3 id=\"existing\">Existing areas</h3>";
		$html .= $this->showareas ();
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to show existing areas
	public function showareas ()
	{
		# Get the data for all areas in the database
		$areas = $this->getAllAreas ();
		
		# Start the HTML with the total
		$totalAreas = count ($areas);
		$html = "\n<p>There are {$totalAreas} areas in the database:</p>";
		
		# Render as HTML
		$headings = $this->databaseConnection->getHeadings ($this->settings['database'], "{$this->settings['tablePrefix']}areas");
		$html .= application::htmlTable ($areas, $headings, 'showareas lines compressed', $keyAsFirstColumn = false, false, false, false, $addCellClasses = true);
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to get all areas in the database
	private function getAllAreas ()
	{
		# Get and return the data
		$showFields = array ('id', 'prefix', 'areaName', 'districtCouncil');
		return $this->databaseConnection->select ($this->settings['database'], "{$this->settings['tablePrefix']}areas", array (), $showFields, true, $orderBy = 'areaName');
	}
	
	
	# Function to add an affiliation
	public function addaffiliations ()
	{
		# Start the HTML
		$html = '';
		
		# Add introduction
		$html .= "\n<p>Here you can add a political party/group to the database.</p>";
		$html .= "\n<p>You should <strong>only</strong> add a new political party/group if it is not already listed <a href=\"#existing\">below</a>.</p>";
		
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
				'colour' => array ('type' => 'color', 'disallow' => array ('#000000' => 'Pure black colour is not permitted, due to incompatibility with night mode'), ),	#!# This should be done natively in ultimateForm
			),
		));
		if ($result = $form->process ($html)) {
			
			# Replace hash in colour code
			#!# This should be done natively in ultimateForm
			$result['colour'] = str_replace ('#', '', $result['colour']);
			
			# Insert the new entry
			if (!$this->databaseConnection->insert ($this->settings['database'], $table, $result)) {
				$html = "\n<p><img src=\"{$this->baseUrl}/images/icons/cross.png\" class=\"icon\" /> An error occurred adding the affiliation.</p>";
				return $html;
			}
			
			# Confirm success
			$html  = "\n<p><img src=\"{$this->baseUrl}/images/icons/tick.png\" class=\"icon\" /> The affiliation has been added.</p>";
			$html .= "\n<p><a href=\"{$this->baseUrl}/admin/" . __FUNCTION__ . '.html">Add another?</a></p>';
		}
		
		# Show existing parties/groups
		$html .= "\n<h3 id=\"existing\">Existing parties/groups</h3>";
		$html .= $this->showaffiliations ();
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to show exiting affiliations
	public function showaffiliations ()
	{
		# Get the data for all affiliations in the database
		$affiliations = $this->getAllAffiliations ();
		
		# Start the HTML with the total
		$totalAffiliations = count ($affiliations);
		$html = "\n<p>There are {$totalAffiliations} parties/groups in the database:</p>";
		
		# Colourise colour codes
		foreach ($affiliations as $id => $affiliation) {
			$hexColour = '#' . $affiliation['colour'];
			$affiliations[$id]['colour'] = "<span style=\"color: {$hexColour};\">{$hexColour}</style>";
		}
		
		# Render as HTML
		$headings = $this->databaseConnection->getHeadings ($this->settings['database'], "{$this->settings['tablePrefix']}affiliations");
		$html .= application::htmlTable ($affiliations, $headings, 'lines compressed', $keyAsFirstColumn = false, false, $allowHtml = array ('colour'));
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to get all affiliations in the database
	private function getAllAffiliations ()
	{
		# Get and return the data
		return $this->databaseConnection->select ($this->settings['database'], "{$this->settings['tablePrefix']}affiliations", array (), array (), true, $orderBy = 'name');
	}
	
	
	# Function to add a candidate for an election
	public function addcandidate ()
	{
		# Start the HTML
		$html = '';
		
		# Get all elections, including forthcoming
		#!# This reloading should be done generically - several places now require this
		$this->elections = $this->getElections (true);
		$this->election = ((isSet ($_GET['election']) && isSet ($this->elections[$_GET['election']])) ? $this->elections[$_GET['election']] : false);
		
		# Ensure there is an election supplied
		if (!$this->election) {
			$html .= "\n<p>Please select which election:</p>";
			$html .= $this->listElections ($this->elections, true, false, __FUNCTION__ . '.html');
			return $html;
		}
		
		# Add introduction
		$html .= "\n<p>Here you can add an individual candidate standing for election.</p>";
		$html .= "\n<p>Adding a candidate on this page will not result in an e-mail being sent immediately. E-mails/letters are only distributed upon completion of the survey, where you will explicitly start them sending.</p>";
		$html .= "\n<p>Note: It is usually easier to <a href=\"{$this->baseUrl}/{$this->actions['addcandidates']['url']}\">mass-import the candidates</a> data. But this screen may be useful if you need to add afterwards a candidate who was accidentally omitted.</p>";
		
		# Get the elections, as key-value pairs, though only the currently-selected one (see above) will get selected (as a fixed value)
		$elections = array ();
		foreach ($this->elections as $electionId => $election) {
			$elections[$electionId] = $election['name'] . " [{$electionId}]";
		}
		
		# Get the areas for this election, as key-value pairs
		if (!$areasById = $this->getSurveyAreas ($this->election['id'])) {
			$html .= "\n<p>There are not yet any surveys loaded, so we cannot yet list the available areas - please <a href=\"{$this->baseUrl}/{$this->actions['addsurveys']['url']}\">create a survey for an area</a> first.</p>";
			return $html;
		}
		
		# Create a new form
		require_once ('ultimateForm.php');
		$form = new form (array (
			'div' => 'ultimateform ' . __FUNCTION__,
			'databaseConnection' => $this->databaseConnection,
			'autofocus' => true,
		));
		$form->dataBinding (array (
			'database'		=> $this->settings['database'],
			'table'			=> "{$this->settings['tablePrefix']}candidates",
			'includeOnly'	=> array ('election', 'forename', 'surname', 'areaId', 'affiliation', 'address', 'email'),
			'attributes' => array (
				'election'		=> array ('type' => 'select', 'values' => $elections, 'default' => $this->election['id'], 'editable' => false, ),
				'areaId'		=> array ('type' => 'select', 'values' => $areasById),
				'affiliation'	=> array ('type' => 'select', 'values' => $this->getAffiliationNames ()),
				'address'		=> array ('description' => 'Use a comma-space between each part of the address. Enter a dash (-) if the address is not known.', ),
				'email'		=> array ('description' => 'This page will <strong>not</strong> send an e-mail at this point.', ),
			),
		));
		if ($result = $form->process ($html)) {
			
			# Generate a verification ID
			$result['verification'] = $this->generateVerificationId ();
			
			# Insert the candidate
			if (!$this->databaseConnection->insert ($this->settings['database'], "{$this->settings['tablePrefix']}candidates", $result)) {
				$html = "\n<p><img src=\"{$this->baseUrl}/images/icons/cross.png\" class=\"icon\" /> An error occurred adding the candidate.</p>";
				return $html;
			}
			
			# Confirm success
			$html  = "\n<p><img src=\"{$this->baseUrl}/images/icons/tick.png\" class=\"icon\" /> The candidate has been added.</p>";
			$html .= "\n<p><a href=\"{$this->baseUrl}/{$this->election['id']}/" . __FUNCTION__ . '.html">Add another?</a></p>';
		}
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to add a candidate for an election
	public function candidates ()
	{
		# Start the HTML
		$html = '';
		
		# Get all elections, including forthcoming
		#!# This reloading should be done generically - several places now require this
		$this->elections = $this->getElections (true);
		$this->election = ((isSet ($_GET['election']) && isSet ($this->elections[$_GET['election']])) ? $this->elections[$_GET['election']] : false);
		
		# Ensure there is an election supplied
		if (!$this->election) {
			$html .= "\n<p>Please select which election:</p>";
			$html .= $this->listElections ($this->elections, true, false, __FUNCTION__ . '.html');
			return $html;
		}
		
		# Get candidates, or end
		if (!$allCandidates = $this->getCandidates ($this->election['id'], true)) {
			$html .= "\n<p>The candidate list has not yet been loaded for this election. Please check back later.</p>";
			return $html;
		}
		
		# Get the responses
		$candidateIds = array_keys ($allCandidates);
		$responses = $this->getResponses (false, $candidateIds);
		
		# Compile table
		$table = array ();
		foreach ($allCandidates as $candidate) {
			$candidateResponded = (array_key_exists ($candidate['id'], $responses));
			$table[] = array (
				'Area' => "<a href=\"{$this->baseUrl}/{$this->election['id']}/{$candidate['areaId']}/\">" . htmlspecialchars ($candidate['areaName']) . '</a>',
				'Name' => $candidate['_name'],		// Already HTML
				'Verification ID' => $candidate['verification'],
				'Responded?' => ($candidateResponded ? 'Yes' : ''),
				'Reissue e-mail?' => ($candidateResponded ? '' : "<a href=\"{$this->baseUrl}/{$this->election['id']}/reissue.html?candidate={$candidate['id']}\">Reissue&hellip;</a>"),
			);
		}
		
		# Render as table
		$html  = "\n<p>This table of candidates, available to administrations only, lists all candidates for this election and provides links to reissue an e-mail, so they can then <a href=\"{$this->baseUrl}/submit/\">submit</a> a response.</p>";
		$html .= application::htmlTable ($table, array (), 'lines', $keyAsFirstColumn = false, false, true);
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to add (mass-import) candidates for an election
	public function addcandidates ()
	{
		# Start the HTML
		$html = '';
		
		# Get all elections, including forthcoming
		#!# This reloading should be done generically - several places now require this
		$this->elections = $this->getElections (true);
		
		# Filter only to elections that are forthcoming, so that only those are shown in the selection list; this is to prevent answers becoming misconnected to candidates who would have new IDs
		foreach ($this->elections as $id => $election) {
			if (!$election['isForthcoming']) {
				unset ($this->elections[$id]);
			}
		}
		
		# End if no elections forthcoming
		if (!$this->elections) {
			$html .= "\n<p>There are no elections forthcoming.</p>";
			$html .= "\n<p>If you have recently set up an election, make sure that the start date is tomorrow or later, as mass-importing data for an election that has already started is not supported.</p>";
			return $html;
		}
		
		# Select the election
		$this->election = ((isSet ($_GET['election']) && isSet ($this->elections[$_GET['election']])) ? $this->elections[$_GET['election']] : false);
		
		# Ensure there is an election supplied
		if (!$this->election) {
			$html .= "\n<p>Please select which election:</p>";
			$html .= $this->listElections ($this->elections, true, false, __FUNCTION__ . '.html');
			return $html;
		}
		
		# Define the required fields
		$requiredFields = array ('forename', 'surname', 'areaId', 'affiliation', 'address', 'email');
		
		# Add introduction
		$html .= "\n<p>On this page you can mass-import the candidate data.</p>";
		$html .= "\n<p>You may instead wish to <a href=\"{$this->baseUrl}/{$this->actions['addcandidate']['url']}\">add in a single candidate</a>.</p>";
		$html .= "\n<p>To import candidate data, you will need to prepare a spreadsheet containing it. This must include the following headings (in order): <strong>" . implode ('</strong>, <strong>', $requiredFields) . "</strong>. These headings must be present in the box below as the first line, so that the system knows which column is which.</p>";
		$html .= "\n<p><strong>Please use this form carefully.</strong> Note that:";
		$html .= "\n<ul>";
		$html .= "\n\t<li>The 'Replace' option below will disconnect any existing responses from candidates for the selected election. You should never use that option while an election is in progress.</p>";
		$html .= "\n\t<li>If you are adding new candidates to the existing data, you must sure you do not add candidates already loaded, as that will cause duplication.</p>";
		$html .= "\n</ul>";
		$html .= "\n<p>This form will not do any e-mailing.</p>";
		
		# Create a new form
		require_once ('ultimateForm.php');
		$form = new form (array (
			'databaseConnection' => $this->databaseConnection,
			'picker' => true,
		));
		$form->select (array (
			'name'			=> 'election',
			'title'			=> 'Which election',
			'values'		=> $this->getElectionNames ($this->elections),
			'required'		=> true,
			'default'		=> $this->election['id'],
			'editable'		=> false,
		));
		$form->radiobuttons (array (
			'name'			=> 'action',
			'title'			=> 'Action (please choose carefully)',
			'values'		=> array (
				'replace'	=> '<strong>Replace all candidates</strong>: This will replace the existing candidate data for this election. NEVER use this option when e-mails to candidates have already gone out, as it will break the link with existing answers.',
				'add'		=> '<strong>Add additional candidates</strong>: This will add additional candidates, so should be used when the initial import was not complete. It is safe to do once the election in progress, as it will not delete existing candidates. Make sure you do not add candidates already loaded, as that will cause duplication.',
			),
			'required'		=> true,
			'entities'		=> false,
		));
		$form->textarea (array (
			'name'			=> 'data',
			'title'			=> 'Enter the candidate data, pasted from your spreadsheet - see details above. Make sure it includes the headings in the first line.',
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
				
				# Verify area names
				$areas = $this->getAreaNames ();
				$unknownAreas = array ();
				foreach ($data as $candidate) {
					if (!array_key_exists ($candidate['areaId'], $areas)) {
						$unknownAreas[] = $candidate['areaId'];
					}
				}
				if ($unknownAreas) {
					$form->registerProblem ('unknownareas', 'Not all areas were recognised: <em>' . htmlspecialchars (implode (', ' , array_unique ($unknownAreas))) . '</em>; please register missing areas on the areas page if correct.');
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
			
			# Add random verification ID for candidate login; note that uniqueness across the dataset is not actually required
			$data[$index]['verification'] = $this->generateVerificationId ();
		}
		
		# In replace mode, clear any existing data
		if ($result['action'] == 'replace') {
			$this->databaseConnection->delete ($this->settings['database'], "{$this->settings['tablePrefix']}candidates", array ('election' => $result['election']));
		}
		
		# Insert the data; note that this will result in new candidate IDs if replacing
		if (!$this->databaseConnection->insertMany ($this->settings['database'], "{$this->settings['tablePrefix']}candidates", $data)) {
			$error = $this->databaseConnection->error ();
			$html  = "\n<p><img src=\"{$this->baseUrl}/images/icons/cross.png\" class=\"icon\" /> Sorry, an error occured. The database server said:</p>";
			$html .= "\n<p><tt>" . $error[2] . '</tt></p>';
			return $html;
		}
		
		# Confirm success
		$total = count ($data);
		#!# Ideally the message should make clear if this was entirely new or a replacement
		$html = "\n<p><img src=\"{$this->baseUrl}/images/icons/tick.png\" class=\"icon\" /> The candidate data you entered (total: {$total}) for this <a href=\"{$this->baseUrl}/{$result['election']}/\">election</a> has been entered.</p>";
		
		# Return the HTML
		return $html;
	}
	
	
	# Helper function to generate a verification ID
	#!# Needs to do uniqueness check to avoid (unlikely) clashes
	private function generateVerificationId ()
	{
		return application::generatePassword (6, $numeric = false);
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
	
	
	# Function to edit details of a candidate
	public function editcandidate ()
	{
		# Start the HTML
		$html = '';
		
		# Get all elections, including forthcoming
		#!# This reloading should be done generically - several places now require this
		$this->elections = $this->getElections (true);
		$this->election = ((isSet ($_GET['election']) && isSet ($this->elections[$_GET['election']])) ? $this->elections[$_GET['election']] : false);

		# Ensure there is an election supplied
		if (!$this->election) {
			$html .= "\n<p>Please select which election:</p>";
			$html .= $this->listElections ($this->elections, true, false, __FUNCTION__ . '/');
			return $html;
		}
		
		# Not available once the election is over
		if (!$this->election['active']) {
			return $html .= '<p>This is no longer available now the election is over.</p>';
		}
		
		# Get the candidates
		if (!$candidates = $this->getCandidates ($this->election['id'], true)) {
			$html .= '<p>There are no candidates at present.</p>';
			return $html;
		}
		
		# Determine if there is a valid ID supplied
		$id = false;
		if (isSet ($_GET['id'])) {
			if (!array_key_exists ($_GET['id'], $candidates)) {
				$html = $this->pageNotFound ();
				return $html;
			}
			$id = $_GET['id'];
		}
		
		# Introduction
		$html .= "\n<p>This form can be used to fix up data for a candidate, e.g. to correct a name.</p>";
		$html .= "\n<p>Making changes will retain links between the candidate and any answers they have submitted.</p>";
		$html .= "\n<p>You should not use this to mutate a candidate to be a different person.</p>";
		
		# Assemble candidate names by area, for a drop-down list
		$candidatesByArea = array ();
		foreach ($candidates as $candidateId => $candidate) {
			$area = $candidate['areaName'] . ':';
			$candidatesByArea[$area][$candidateId] = $candidate['name'] . ' (' . $candidate['affiliation'] . ')';
		}
		
		# If no ID, start with the candidate selection drop-down
		if (!$id) {
			require_once ('ultimateForm.php');
			$form = new form (array (
				'databaseConnection' => $this->databaseConnection,
				'submitButtonText' => 'Edit candidate details &gt;',
				'autofocus' => true,
			));
			$form->select (array (
				'name'		=> 'id',
				'title'		=> 'Candidate',
				'values'	=> $candidatesByArea,
			));
			if ($result = $form->process ($html)) {
				
				# Redirect to same URL with ID
				$redirectTo = $this->baseUrl . '/' . $this->election['id'] . '/' . $this->action . '/' . $result['id'] . '/';
				$html = application::sendHeader (301, $redirectTo);
			}
			
			# End, returning the HTML
			return $html;
		}
		
		# Show the candidate details editing form, now that a valid ID is known
		require_once ('ultimateForm.php');
		$form = new form (array (
			'databaseConnection' => $this->databaseConnection,
			'submitButtonText' => 'Update candidate details',
			'autofocus' => true,
			'displayRestrictions' => false,
		));
		$candidate = $candidates[$id];
		$data = array (
			'id'			=> $candidate['id'],
			'forename'		=> $candidate['forename'],
			'surname'		=> $candidate['surname'],
			'address'		=> $candidate['address'],
			'email'			=> $candidate['email'],
			'affiliation'	=> $candidate['affiliationId'],
		);
		$form->dataBinding (array (
			'database'		=> $this->settings['database'],
			'table'			=> "{$this->settings['tablePrefix']}candidates",
			'includeOnly'	=> array_keys ($data),
			'data'			=> $data,
			//'intelligence'	=> true,	// Intelligence can't be used as it forces e-mail type, handled below
			'attributes'	=> array (
				'data'			=> $data,
				'id'			=> array ('type' => 'select', 'title' => 'Candidate', 'values' => $candidatesByArea, 'editable' => false, ),
				'affiliation'	=> array ('type' => 'select', 'values' => $this->getAffiliationNames ()),
				'email'			=> array ('type' => ($data['email'] == '-' ? 'text' : 'email'), ),	// Tolerate - in e-mail field
			),
		));
		if ($result = $form->process ($html)) {
			
			# Update the database
			if (!$this->databaseConnection->update ($this->settings['database'], "{$this->settings['tablePrefix']}candidates", $result, array ('id' => $id))) {
				$html = "\n<p class=\"warning\">There was a problem updating the database.</p>";
			}
			
			# Confirm success
			$html  = "\n<p>✓ The candidate has now been updated.</p>";
			$html .= "\n<p><a href=\"{$this->baseUrl}/admin/\">Return to the admin section.</a></p>";
		}
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to add questions
	public function addquestions ()
	{
		# Start the HTML
		$html = '';
		
		# Introductory text
		$html .= "\n<p>In this section, you can add questions that can then be used in a survey. Note that questions have to be added one at a time.</p>";
		$html .= "\n<p>The {$this->settings['mostRecent']} most recently-added questions are shown below.</p>";
		
		# Create the form
		if (!$result = $this->questionForm (array (), $html)) {
			#!# Need to check that highlight text appears in the question
			$html .= $this->recentlyAddedQuestions ($this->settings['mostRecent']);
			return $html;
		}
		
		# Insert the question
		if (!$this->databaseConnection->insert ($this->settings['database'], "{$this->settings['tablePrefix']}questions", $result)) {
			$html  = "\n<p><img src=\"{$this->baseUrl}/images/icons/cross.png\" class=\"icon\" /> Sorry, an error occured.</p>";
			return $html;
		}
		$questionId = $this->databaseConnection->getLatestId ();
		
		# Confirm success
		$html  = "\n<p><img src=\"{$this->baseUrl}/images/icons/tick.png\" class=\"icon\" /> The question has been added, as ID <strong>#{$questionId}</strong>, as shown below. It is now available to use when constructing surveys.</p>";
		$html .= "\n<p>Do you wish to <a href=\"{$this->baseUrl}/admin/" . __FUNCTION__ . '.html">+ add another</a>?</p>';
		$html .= $this->recentlyAddedQuestions ($this->settings['mostRecent']);
		
		# Return the HTML
		return $html;
	}
	
	
	# Helper function to create a question creation form
	public function questionForm ($question = array (), &$html = '')
	{
		# Links explanation text
		$linksExplanation  = "\n" . '<p>Here, you can enter one or more links that will appear after the question. This is helpful to give candidates some context or further reading matter.</p>';
		$linksExplanation .= "\n" . '<p>If you have two or more links, enter each on a separate line.</p>';
		$linksExplanation .= "\n" . '<p>You can enter each such link in one of two ways:</p>';
		$linksExplanation .= "\n" . '<ul class="spaced">';
		$linksExplanation .= "\n\t" . '<li>The the web address followed by a label:<br /><tt>https://www.example.com/ My example link</tt><br />which will show as:<br /><a href="https://www.example.com/">My example link</a></p>';
		$linksExplanation .= "\n\t" . '<li>Multiple: on each line, the web address followed by a label:<br /><tt>https://www.example.com/ My example link<br />https://www.example.org/ Another example link</tt><br />which will show as:<br /><a href="https://www.example.com/">My example link</a><br /><a href="https://www.example.org/">Another example link</a></p>';
		$linksExplanation .= "\n\t" . '<li>[Not recommended] Just the web address:<br /><tt>https://www.example.com/</tt><br />which will show as a bare URL:<br /><a href="https://www.example.com/">https://www.example.com/</a></li>';
		$linksExplanation .= "\n" . '</ul>';
		
		# Create a new form
		require_once ('ultimateForm.php');
		$form = new form (array (
			'databaseConnection' => $this->databaseConnection,
			'display' => 'paragraphs',
			'picker' => true,
			'size' => 80,
			'rows' => 7,
			'cols' => 80,
		));
		$form->dataBinding (array (
			'database'	=> $this->settings['database'],
			'table'		=> "{$this->settings['tablePrefix']}questions",
			'data'		=> $question,
			'intelligence' => true,
			'size'	=> 80,	#!# This is here due to a bug in ultimateForm
			'attributes' => array (
				'question' => array ('heading' => array (3 => 'The question')),
				'links' => array ('heading' => array (3 => 'Optional link(s) (to appear under the question text)', '' => $linksExplanation, )),
			),
		));
		$result = $form->process ($html);
		
		# Return the result
		return $result;
	}
	
	
	# Function to edit a question
	public function editquestions ()
	{
		# Start the HTML
		$html = '';
		
		# Get the questions
		$questionTexts = $this->getQuestionTexts ();
		
		# Determine if a question ID is supplied
		$id = false;
		if (isSet ($_GET['id'])) {
			if (!strlen ($_GET['id']) || !isSet ($questionTexts[$_GET['id']])) {
				return $this->pageNotFound ();
			}
			$id = $_GET['id'];
		}
		
		# Require an ID
		if (!$id) {
			
			# Create the form
			require_once ('ultimateForm.php');
			$form = new form (array (
				'databaseConnection' => $this->databaseConnection,
				'formCompleteText' => false,
			));
			$form->heading ('p', '<strong>IMPORTANT:</strong> You should <strong>not</strong> be editing a question that is currently live, as candidates may already have answered (or even just read) it, which would cause unfairness.');
			$form->heading ('p', 'Please select a question to edit.');
			$form->select (array (
				'name'			=> 'id',
				'title'			=> 'Question',
				'values'		=> $questionTexts,
				'required'		=> true,
			));
			if (!$result = $form->process ($html)) {
				return $html;
			}
			
			# Extract the ID
			$id = $result['id'];
			
			# Redirect to add the ID to the URL
			$url = $_SERVER['_SITE_URL'] . $this->baseUrl . '/' . $this->actions[$this->action]['url'] . '?id=' . $id;
			$html .= application::sendHeader (302, $url);
			return $html;
		}
		
		# Get the full question data
		$questionOriginal = $this->getQuestion ($id);
		
		# Show back link to cancel
		$html .= "\n\n<p class=\"alignright\"><a href=\"{$this->baseUrl}/{$this->actions[$this->action]['url']}\">&laquo; Cancel editing</a> if required.</p>";
		
		# Create the form
		if (!$question = $this->questionForm ($questionOriginal, $html)) {
			return $html;
		}
		
		# Update the question
		$this->updateQuestion ($id, $question);
		
		# Confirm success
		$html = "\n<p><img src=\"{$this->baseUrl}/images/icons/tick.png\" class=\"icon\" /> The question has now been updated as shown below:</p>";
		$html .= "\n" . '<div class="graybox">';
		$html .= "\n" . $this->questionBox ($question);
		$html .= "\n" . '</div>';
		
		# Provide ongoing links
		$html .= "\n\n<p>You can: <a href=\"{$this->baseUrl}/{$this->actions[$this->action]['url']}\">edit another question</a> or <a href=\"{$this->baseUrl}/admin/\">return to admin area</a>.</p>";
		
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
			ORDER BY id DESC
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
			$html .= "\n<p><img src=\"{$this->baseUrl}/images/icons/tick.png\" class=\"icon\" /> The question has been deleted.</p>";
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
			$list[$id] = '<span class="comment">#' . $id . ': </span>' . $this->applyHighlighting (nl2br (htmlspecialchars ($question['question'])), $question['highlight']);
		}
		
		# Compile the HTML
		$html  = "\n<h3>Most recently-added questions</h3>";
		$html .= "\n<p>Below are the {$mostRecent} questions most recently added to the database. You can also <a href=\"{$this->baseUrl}/admin/allquestions.html\">view all questions</a>.</p>";
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
		
		# Add introduction
		$html .= "\n<p>In this section, you can construct a survey for each area. Note that surveys have to be created one at a time.</p>";
		$html .= "\n<p>The {$this->settings['mostRecent']} most recently-added questions are shown below.</p>";
		
		# Show the form (and recently-added questions for reference) and process the data
		if (!$result = $this->surveyForm ($html)) {
			return $html;
		}
		
		# Confirm success
		$html  = "\n<p><img src=\"{$this->baseUrl}/images/icons/tick.png\" class=\"icon\" /> The <a href=\"{$this->baseUrl}/{$result['electionId']}/{$result['areaId']}/\">survey</a> has been added.</p>";
		$html .= "\n<p>Do you wish to <a href=\"{$this->baseUrl}/admin/" . __FUNCTION__ . '.html">add another</a>?</p>';
		
		# Return the HTML
		return $html;
	}
	
	
	# Survey form, to present and process the data
	private function surveyForm (&$html, $electionId = false, $areaId = false, $questionIds = array ())
	{
		# Get all elections, including forthcoming
		$elections = $this->getElections (true);
		
		# Create a new form
		require_once ('ultimateForm.php');
		$form = new form (array (
			'databaseConnection' => $this->databaseConnection,
		));
		$form->select (array (
			'name'			=> 'electionId',
			'title'			=> 'Which election',
			'values'		=> $this->getElectionNames ($elections),
			'required'		=> true,
			'default'		=> $electionId,
			'editable'		=> (!$electionId),
		));
		$form->select (array (
			'name'			=> 'areaId',
			'title'			=> 'Which area',
			'values'		=> $this->getAreaNames (),
			'required'		=> true,
			'default'		=> $areaId,
			'editable'		=> (!$areaId),
		));
		#!# Ordering of outputted $result['questions'] gets broken - when using multiple expandable, ultimateForm loops through the available options, thus providing back that order, rather than validating selected items, retaining their order
		$form->select (array (
			'name'			=> 'questions',
			'title'			=> 'Questions, in order, for this survey',
			'values'		=> $this->getQuestionTexts (),
			'required'		=> true,
			'multiple'		=> true,
			'expandable'	=> true,
			'output'		=> array ('processing' => 'compiled'),
			'default'		=> $questionIds,
		));
		
		# If adding a survey, prevent duplicate survey area creation
		if (!$electionId) {
			if ($data = $form->getUnfinalisedData ()) {
				if (isSet ($data['electionId']) && isSet ($data['areaId'])) {
					
					# If the area is already present for this election, prevent addition
					$surveyAreas = $this->getSurveyAreas ($data['electionId']);
					if (array_key_exists ($data['areaId'], $surveyAreas)) {
						$form->registerProblem ('areaexists', 'There is already a survey for ' . htmlspecialchars ($surveyAreas[$data['areaId']]) . ' for this election.', 'areaId');
					}
				}
			}
		}
		
		# Process the form, or end
		if (!$result = $form->process ($html)) {
			$html .= $this->recentlyAddedQuestions ($this->settings['mostRecent']);
			return false;
		}
		
		# Post-process the multiple select output format in ultimateForm
		#!# This annoyance in ultimateForm really needs to be fixed - 'rawcomponents' is usually wrong, and compiled is in an unhelpful format for processing
		$result['questions'] = explode (",\n", $result['questions']);
		
		# Define standard data for each entry in the survey
		$constraints = array (
			'election'	=> $result['electionId'],
			'areaId'	=> $result['areaId'],
		);
		
		# Construct the list of entries for the survey
		$questionsProcessed = array ();
		foreach ($result['questions'] as $index => $question) {
			$questionsProcessed[$index] = $constraints;
			$questionsProcessed[$index]['question'] = $question;
		}
		
		# Clear any existing surveys data
		$this->databaseConnection->delete ($this->settings['database'], "{$this->settings['tablePrefix']}surveys", $constraints);
		
		# Insert the questions
		if (!$this->databaseConnection->insertMany ($this->settings['database'], "{$this->settings['tablePrefix']}surveys", $questionsProcessed)) {
			$html  = "\n<p><img src=\"{$this->baseUrl}/images/icons/cross.png\" class=\"icon\" /> Sorry, an error occured.</p>";
			return false;
		}
		
		# Return success
		return $result;
	}
	
	
	# Function to edit an existing survey
	public function editsurvey ()
	{
		# Start the HTML
		$html = '';
		
		# Get all elections, including forthcoming
		#!# This reloading should be done generically - several places now require this
		$this->elections = $this->getElections (true);
		$this->election = ((isSet ($_GET['election']) && isSet ($this->elections[$_GET['election']])) ? $this->elections[$_GET['election']] : false);

		# Ensure there is an election supplied
		if (!$this->election) {
			$html .= "\n<p>Please select which election:</p>";
			$html .= $this->listElections ($this->elections, true, false, __FUNCTION__ . '/');
			return $html;
		}
		
		# Not available once the election has started
		if (date ('Y-m-d') >= $this->election['startDate']) {
			return $html .= "\n<p>Editing surveys for this election (" . htmlspecialchars ($this->election['name']) . ") is not now available, as the surveys cannot be edited after the start date (" . date ('jS F, Y', strtotime ($this->election['startDate'] . ' 12:00:00')) . ') for this election.</p>';
		}
		
		# Get the survey areas
		if (!$surveyAreas = $this->getSurveyAreas ($this->election['id'])) {
			$html .= '<p>There are no surveys at present.</p>';
			return $html;
		}
		
		# Determine if there is a valid area name supplied
		$areaId = false;
		if (isSet ($_GET['id'])) {
			if (!array_key_exists ($_GET['id'], $surveyAreas)) {
				$html = $this->pageNotFound ();
				return $html;
			}
			$areaId = $_GET['id'];
		}
		
		# Introduction
		$html .= "\n<p>This form can be used to amend a survey.</p>";
		
		# If no area, start with the survey selection drop-down
		if (!$areaId) {
			require_once ('ultimateForm.php');
			$form = new form (array (
				'databaseConnection' => $this->databaseConnection,
				'submitButtonText' => 'Edit survey &gt;',
				'autofocus' => true,
			));
			$form->select (array (
				'name'		=> 'id',
				'title'		=> 'Survey',
				'values'	=> $surveyAreas,
			));
			if ($result = $form->process ($html)) {
				
				# Redirect to same URL with ID
				$redirectTo = $this->baseUrl . '/' . $this->election['id'] . '/' . $this->action . '/' . $result['id'] . '/';
				$html = application::sendHeader (301, $redirectTo);
			}
			
			# End, returning the HTML
			return $html;
		}
		
		# Get the question IDs for this survey
		$questions = $this->getQuestions ($this->election['id'], $areaId, $groupByQuestionId = true);
		$questionIds = array_keys ($questions);
		
		# Show the form (and recently-added questions for reference) and process the data
		if (!$result = $this->surveyForm ($html, $this->election['id'], $areaId, $questionIds)) {
			return $html;
		}
		
		# Confirm success
		$html  = "\n<p>✓ The survey for " . htmlspecialchars ($surveyAreas[$areaId]) . ' has now been updated.</p>';
		$html .= "\n<p><a href=\"{$this->baseUrl}/admin/editsurvey/\">Return to the survey editing section.</a></p>";
		$html .= "\n<p><a href=\"{$this->baseUrl}/admin/\">Or, return to the admin area.</a></p>";
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to get the survey areas for an election
	private function getSurveyAreas ($electionId)
	{
		# Get the surveys
		$query = "SELECT
				DISTINCT areaId,
				areaName
			FROM {$this->settings['tablePrefix']}surveys
			JOIN {$this->settings['tablePrefix']}areas ON {$this->settings['tablePrefix']}surveys.areaId = {$this->settings['tablePrefix']}areas.id
			WHERE election = :electionId
			ORDER BY areaName
		;";
		$surveys = $this->databaseConnection->getPairs ($query, false, array ('electionId' => $electionId));
		
		# Return the data
		return $surveys;
	}
	
	
	# Function to get a list of area IDs and names
	private function getAreaNames ()
	{
		return $this->databaseConnection->selectPairs ($this->settings['database'], "{$this->settings['tablePrefix']}areas", array (), array ('id', "CONCAT_WS(' ', prefix, areaName) AS name"), true, $orderBy = 'areaName');
	}
	
	
	# Function to get a list of affiliation IDs and names
	private function getAffiliationNames ()
	{
		return $this->databaseConnection->selectPairs ($this->settings['database'], "{$this->settings['tablePrefix']}affiliations", array (), array ('id', 'name'), true, $orderBy = 'name');
	}
	
	
	# Function to get a list of question IDs and texts, most recent first
	private function getQuestionTexts ()
	{
		return $this->databaseConnection->selectPairs ($this->settings['database'], "{$this->settings['tablePrefix']}questions", array (), array ('id', "CONCAT(id, ': ', SUBSTRING(question, 1, 70), ' ...') AS text"), true, $orderBy = 'id DESC');
	}
	
	
	# Function to get a single questions
	private function getQuestion ($id)
	{
		return $this->databaseConnection->selectOne ($this->settings['database'], "{$this->settings['tablePrefix']}questions", array ('id' => $id));
	}
	
	
	# Function to update a question
	private function updateQuestion ($id, $question)
	{
		return $this->databaseConnection->update ($this->settings['database'], "{$this->settings['tablePrefix']}questions", $question, array ('id' => $id));
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
			'title'			=> 'Enter the allocations, as areaId[tab]q1[tab]q2, etc., per line',
			'required'		=> true,
			'cols'			=> 80,
			'rows'			=> 10,
		));
		
		# Process the result
		if (!$result = $form->process ($html)) {
			return $html;
		}
		
		# Compile the SQL
		$sql  = "INSERT INTO {$this->settings['tablePrefix']}surveys (election,areaId,question) VALUES \n";
		$areas = explode ("\n", trim ($result['allocations']));
		$set = array ();
		foreach ($areas as $area) {
			list ($areaId, $questions) = preg_split ("/\s+/", trim ($area), 2);
			$allocations = preg_split ("/\s+/", trim ($questions));
			foreach ($allocations as $allocation) {
				$set[] .= "\t('{$result['election']}', '{$areaId}', {$allocation})";
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
			$html .= $this->listElections ($this->elections, true, false, __FUNCTION__ . '.html');
			return $html;
		}
		
		# Run the mailout routine
		$html .= $this->emailMailoutRoutine (__FUNCTION__, 'e-mails');
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to send reminder e-mails to candidates who have not yet responded to the survey
	public function reminders ()
	{
		# Start the HTML
		$html = '';
		
		# Ensure there is an election supplied
		if (!$this->election) {
			$html .= "\n<p>Please select which election:</p>";
			$html .= $this->listElections ($this->elections, true, false, __FUNCTION__ . '.html');
			return $html;
		}
		
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
			$html .= $this->listElections ($this->elections, true, false, __FUNCTION__ . '.html');
			return $html;
		}
		
		# Not available once the election is over
		if (!$this->election['active']) {
			return $html .= '<p>This is no longer available now the election is over.</p>';
		}
		
		# Get the candidates
		if (!$candidates = $this->getCandidates ($this->election['id'], true)) {
			$html .= '<p>There are no candidates at present.</p>';
			return $html;
		}
		
		# Regroup
		$candidatesByArea = application::regroup ($candidates, 'areaId', $removeGroupColumn = false);
		
		# Determine which candidates have responded
		$candidateIdsResponded = $this->getCandidateIdsResponded ($this->election['id']);
		
		# Compile a droplist of candidates, grouped by area, skipping those that have already responded
		$areaCandidates = array ();
		foreach ($candidatesByArea as $areaId => $candidatesThisArea) {
			foreach ($candidatesThisArea as $candidateId => $candidate) {
				if (in_array ($candidateId, $candidateIdsResponded)) {continue;}
				$areaName = $candidate['areaName'] . ':';
				$areaCandidates[$areaName][$candidateId] = $candidate['name'] . '  (' . $candidate['affiliation'] . ')';
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
		$form->heading ('p', 'Note: If you update the e-mail address in the form, this will be saved in the database and will replace any address previously recorded for this candidate.');
		$form->select (array (
			'name'			=> 'candidate',
			'title'			=> 'Candidate',
			'values'		=> $areaCandidates,
			'required'		=> true,
			'get'			=> 'candidate',
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
	
	
	# Function to send the e-mails and report the outcome; this currently sends plain text only, but e-mail clients will hyperlink addresses/e-mails in the text
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
		# Start the HTML
		$html = '';
		
		# Get all elections, including forthcoming
		#!# This reloading should be done generically - several places now require this
		$this->elections = $this->getElections (true);
		$this->election = ((isSet ($_GET['election']) && isSet ($this->elections[$_GET['election']])) ? $this->elections[$_GET['election']] : false);
		$this->areas = $this->getAreasForElection ($this->election['id']);
		
		# Ensure there is an election supplied
		if (!$this->election) {
			$html .= "\n<p>Please select which election:</p>";
			$html .= $this->listElections ($this->elections, true, false, "{$type}.html");
			return false;
		}
		
		# Get the candidates
		if (!$candidates = $this->getCandidates ($this->election['id'], true)) {
			$html .= '<p>There are no candidates at present.</p>';
			return false;
		}
		
		# Get the surveys
		if (!$surveys = $this->getQuestions ($this->election['id'], false)) {
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
		$surveys = application::regroup ($surveys, 'areaId', $removeGroupColumn = false);
		$candidates = application::regroup ($candidates, 'areaId', $removeGroupColumn = false);
		
		# Start an HTML output and list of e-mails
		$outputHtml = '';
		$emails = array ();
		
		# Add to the e-mail preview HTML
		$emailsPreviewHtml .= "\n<h3>Preview of each e-mail</h3>";
		$emailsPreviewHtml .= "\n<hr />";
		
		# Loop through by area having surveys
		foreach ($surveys as $area => $questionnaire) {
			
			# Miss out if no candidates in an area; a warning is shown if none, in case of trailing spaces, etc.
			if (!isSet ($this->areas[$area])) {
				$html .= "\n<p class=\"warning\">Warning: No candidates for <em>{$area}</em> {$this->election['areaType']}.</p>";
				continue;
			}
			
			# Loop through each candidate for this area
			foreach ($candidates[$area] as $candidateId => $candidate) {
				
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
		
		# Assemble the area name
		$areaName = $this->areaName ($candidate);
		
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
						" . htmlspecialchars ($candidate['name']) . ',<br />
						' . str_replace (',', ',<br />', htmlspecialchars ($candidate['address'])) . "
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
						<p>Dear candidate,</p>
						<p>We are writing to you as one of the {$areaName} {$this->election['areaType']} candidates in the {$this->election['name']}.</p>
						" . $this->election['organisationIntroductionHtml'] . "
						<p>We ask candidates to submit their responses via the automated facility on our website. Just go to: <u>{$submissionUrl}</u> and enter your verification ID: <strong>{$candidate['verification']}</strong>. The website version also contains links giving further information.</p>
						" . $screenshotHtml . "
						<p>If you are unable to complete this survey online or you require any other assistance please e-mail, phone or write to us and we will be happy to make alternative arrangements.</p>
						<p>Many thanks for your time.<br />Yours sincerely,</p>
						<p>" . htmlspecialchars ($this->election['letterSignatureName']) . ",<br />" . htmlspecialchars ($this->election['letterSignaturePosition']) . ', ' . htmlspecialchars ($this->settings['letterSignatureOrganisationName']) . "</p>
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
			$question['question'] = nl2br (htmlspecialchars ($question['question']));
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
		# Assemble the area name
		$areaName = $this->areaName ($candidate, $convertEntities = false);
		
		# Define the submission URL
		$submissionUrl = "https://{$_SERVER['SERVER_NAME']}{$this->baseUrl}/submit/";
		
		# Assemble the text
		$text  = "\n";
		if ($type == 'reminders') {
			$text .= "\n" . 'Dear candidate - Just a reminder of this below - thanks in advance for your time.' . "\n\n--\n\n";
		}
		$text .= "\n" . "Dear {$candidate['nameNaturalCase']},";
		$text .= "\n\n" . "We are writing to you as one of the {$areaName} {$this->election['areaType']} candidates in the {$this->election['name']}.";
		$text .= "\n\n" . preg_replace ("|\n\s+|", "\n\n", strip_tags (str_replace ("\n", "\n\n", str_replace (' www', ' https://www', str_replace ('&#64;', '@', $this->election['organisationIntroductionHtml'])))));
		$text .= "\n";
		$text .= "\n" . 'Please access the survey and submit your responses online, here:';
		$text .= "\n" . "{$submissionUrl}";
		$text .= "\n";
		$text .= "\n" . "You will need to use this verification ID: {$candidate['verification']} .";
		$text .= "\n";
		$text .= "\n";
		$text .= "\n" . 'Many thanks for your time.';
		$text .= "\n" . 'Yours sincerely,';
		$text .= "\n";
		$text .= "\n" . $this->election['letterSignatureName'] . ',';
		$text .= "\n" . $this->election['letterSignaturePosition'] . ', ' . $this->settings['letterSignatureOrganisationName'];
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
			$html .= $this->listElections ($this->elections, true, false, __FUNCTION__ . '.html');
			return $html;
		}
		
		# Ensure the election is no longer active
		if ($this->election['active']) {
			return $html .= '<p>This cannot be done until after the election is over.</p>';
		}
		
		# Get the candidates
		if (!$candidates = $this->getCandidates ($this->election['id'], true)) {
			return $html .= '<p>There are no candidates at present.</p>';
		}
		
		# Arrange the candidates by area name
		$candidates = application::regroup ($candidates, 'areaName', false);
		
		# Arrange to be added to a multi-select
		$candidatesByArea = array ();
		$elected = array ();	// From a previous import - helpful to maintain this to avoid re-entry of every area if there was a mistake
		foreach ($candidates as $areaName => $candidatesThisArea) {
			$elected[$areaName] = array ();
			foreach ($candidatesThisArea as $candidateId => $candidate) {
				$candidatesByArea[$areaName][$candidateId] = $candidate['name'] . ' (' . $candidate['affiliation'] . ')';
				if ($candidate['elected']) {
					$elected[$areaName][] = $candidateId;
				}
			}
		}
		
		# Create a form
		require_once ('ultimateForm.php');
		$form = new form (array (
			'displayRestrictions'	=> false,
			'nullText' => false,
			'formCompleteText' => 'The results have been saved. These are now visible on the area and question pages.',
			'unsavedDataProtection' => true,
		));
		$form->heading ('p', 'Use this form to specify the elected candidates, which will be marked in the listings as having been elected.');
		$i = 0;
		foreach ($candidatesByArea as $areaName => $candidates) {
			$form->select (array (
				'name'			=> 'area' . $i++,
				'title'			=> $areaName,
				'values'		=> $candidates,
				'default'		=> $elected[$areaName],
				// 'required'		=> true,
				'multiple'		=> true,
				'expandable'	=> true,
			));
		}
		
		# Process the form
		if ($result = $form->process ($html)) {
			
			# Compile into a list
			$electedCandidates = array ();
			foreach ($result as $area => $candidates) {
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
	
	
	# Function to provide cookie-based login internally
	private function loadInternalAuth ()
	{
		# Determine if a local redirect path is provided
		#!# This is necessary because userAccount.php doesn't auto-add baseUrl properly or avoiding redirecting /login/ back to /login/ - these should be added
		$redirectToAfterLogin = '/' . $this->actions['admin']['url'];	// Set admin page as default, otherwise /login/ will redirect back to /login/, which is not helpful
		$parameters = $_GET;
		unset ($parameters['action']);
		if (count ($parameters) == 1) {
			$firstKey = array_key_first ($parameters);
			if (substr ($firstKey, 0, 1) == '/') {
				$redirectToAfterLogin = preg_replace ('/_html$/', '.html', $firstKey);
			}
		}
		
		# Assemble the settings to use
		$internalAuthSettings = array (
			'applicationName'	=> $this->settings['applicationName'],
			'baseUrl'		=> $this->baseUrl,
			'imagesLocation'	=> $this->baseUrl . '/images/icons/',
			'database'		=> $this->settings['database'],
			'table'			=> $this->settings['tablePrefix'] . 'users',
			'administratorEmail'	=> $this->settings['webmaster'],
			'usernames'		=> true,
			'privileges'		=> true,
			'redirectToAfterLogin'	=> $redirectToAfterLogin,
		);
		
		# Load the user account system
		require_once ('userAccount.php');
		$this->internalAuthClass = new userAccount ($internalAuthSettings, $this->databaseConnection);
	}
	
	
	# Login function, only available if internalAuth is enabled
	public function logininternal (&$status = false)
	{
		# Run the validation and return the supplied e-mail
		$this->user = $this->internalAuthClass->login ($showStatus = true);
		
		# Set the status
		$status = ($this->user);
		
		# Assemble the HTML
		$html = $this->internalAuthClass->getHtml ();
		
		# Return the HTML
		return $html;
	}
	
	
	# Logout message, only available if internalAuth is enabled
	public function logoutinternal ()
	{
		# Log out and confirm this status
		$this->internalAuthClass->logout ();
		
		# Assemble the HTML
		$html  = $this->internalAuthClass->getHtml ();
		
		# Return the HTML
		return $html;
	}
	
	
	# Register page
	public function register ()
	{
		# Run the registration page
		$this->internalAuthClass->register ();
		
		# Assemble the HTML
		$html  = $this->internalAuthClass->getHtml ();
		
		# Return the HTML
		return $html;
	}
	
	
	# Reset password page
	public function resetpassword ()
	{
		# Log out and confirm this status
		$this->internalAuthClass->resetpassword ();
		
		# Assemble the HTML
		$html  = $this->internalAuthClass->getHtml ();
		
		# Return the HTML
		return $html;
	}
	
	
	# Login account details page
	public function accountdetails ()
	{
		# Log out and confirm this status
		$this->internalAuthClass->accountdetails ();
		
		# Assemble the HTML
		$html  = $this->internalAuthClass->getHtml ();
		
		# Return the HTML
		return $html;
	}
	
	
	# Settings form
	public function settings ()
	{
		# Start the HTML
		$html = '';
		
		# Define default dataBinding settings
		$dataBindingSettings = array (
			'database' => $this->settings['database'],
			'table' => $this->settings['settingsTable'],
			'intelligence' => true,
			'int1ToCheckbox' => true,
			'data' => $this->settings,
			'attributes' => array (
				'welcomeTextHtml' => array ('heading' => array (3 => 'In-page text'), ),
				'emailSubject' => array ('type' => 'input', 'heading' => array (3 => 'E-mails/letters to candidates'), ),
				'postSubmissionHtml' => array ('heading' => array (3 => 'Post-submission message'), ),
				'showAddresses' => array ('heading' => array (3 => 'Candidate addresses visibility'), ),
				'recipient' => array ('heading' => array (3 => 'Survey submission receipts'), ),
				'webmaster' => array ('heading' => array (3 => 'Webmaster'), ),
				'listArchived' => array ('heading' => array (3 => 'Listings'), ),
			),
		);
		
		# Databind a form
		require_once ('ultimateForm.php');
		$form = new form (array (
			'databaseConnection'	=> $this->databaseConnection,
			'div' => 'ultimateform settings horizontalonly',
			'reappear' => true,
			'formCompleteText' => false,
			'displayRestrictions' => false,
			'unsavedDataProtection' => true,
			'cols' => 80,
			'richtextEditorBasePath' => $this->baseUrl . '/js/libraries/ckeditor/',
			'richtextEditorToolbarSet' => 'BasicLonger',	// Settings tend to be simple text, such as a paragraph with minimal formatting
			'richtextEditorFileBrowser' => false,
			'richtextHeight' => 150,
		));
		$form->dataBinding ($dataBindingSettings);
		
		# Add getUnfinalised post-processing if such a function is defined in the calling class
		if (method_exists ($this, 'settingsGetUnfinalised')) {
			$this->settingsGetUnfinalised ($form);	// Needs to be received by reference
		}
		
		# Process the form
		if ($result = $form->process ($html)) {
			
			# Add in fixed data
			$result['id'] = 1;
			
			# Insert/update the data
			$this->databaseConnection->insert ($this->settings['database'], $this->settings['settingsTable'], $result, $onDuplicateKeyUpdate = true);
			
			# Confirm success
			$html = "\n<p><img src=\"{$this->baseUrl}/images/icons/tick.png\" class=\"icon\" alt=\"\" /> The settings have been updated.</p>" . $html;
		}
		
		# Return the HTML
		return $html;
	}
}

?>
