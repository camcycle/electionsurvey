# Installation notes

## Server requirements

- Apache (should run on IIS but you will need to provide a URL rewriting solution)
- MySQL server
- PHP server with PDO-mysql installed


## Installation summary

1. Clone this repo to a folder called elections, e.g. `git clone https://github.com/camcycle/electionsurvey/ elections`
1. Create the .htaccess file, and change the RewriteBase if your installation is not in a folder called /elections/.
1. Create a database in MySQL and a user with SELECT,INSERT,UPDATE,DELETE rights, which will be the runtime user.
1. Create the config file, as noted below, containing the system settings.
1. Access the main page within the site, e.g. /elections/ . On first run, the system will attempt to install the database structure automatically. This will need a user with CREATE rights.


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

The application is written as a self-contained PHP class.

The system should be error/warning/notice-free at error_reporting 2047.

