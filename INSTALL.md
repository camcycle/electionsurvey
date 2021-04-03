# Installation notes

## Server requirements

- Apache (should run on IIS but you will need to provide a URL rewriting solution)
- MySQL server
- PHP5 server with PDO-mysql installed


## Installation summary

1. Clone this repo to a folder called elections, e.g. `git clone https://github.com/camcycle/electionsurvey/ elections`
1. Create the .htaccess file, and change the RewriteBase if your installation is not in a folder called /elections/.
1. Create a database in MySQL and a user with SELECT,INSERT,UPDATE,DELETE rights, which will be the runtime user.
1. Create the database structure defined in databaseStructure (). This will need a user with CREATE,INDEX rights. You will need to substitute the prefix variable shown. (Shortly, this step will be removed, and the system will prompt for a password to create the structure itself.)
1. Create the config file, as noted below, containing the system settings.


## Config file

A config launching file needs to be created, to instantiate this class.

An example is supplied as `.config.php.example`, which should be copied to `.config.php`.

It is basically an array of settings, followed by loading the class and instantiating it.

The settings noted in the class at $defaults are available, with NULL representing a required setting.


## URL rewriting

Use the .htaccess file supplied.
	

## Libraries

Some discrete third-parties libraries are supplied as part of the repository.


## PHP information

The application is written as a self-contained PHP5 class.

The system should be error/warning/notice-free at error_reporting 2047.

