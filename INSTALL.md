# Installation notes

## Server requirements

- Apache (should run on IIS but you will need to provide a URL rewriting solution)
- MySQL server
- PHP5 server with PDO-mysql installed


## Installation summary

1. Download the above libraries and put them, together with this class file, into a folder that is in your include_path
1. Create the .htaccess file, and change the RewriteBase if necessary
1. Create the database structure, and a user with SELECT,INSERT,UPDATE rights
1. Create the stub launching file, index.html containing your settings; that file then just loads elections.php and runs the program with the specified settings


## Libraries

Some discrete third-parties libraries are supplied as part of the repository.


## Config file

A config launching file needs to be created, to instantiate this class.

An example is supplied as `.config.php.example`, which should be copied to `.config.php`.

It is basically an array of settings, followed by loading the class and instantiating it.

The settings noted in the class at $defaults are available, with NULL representing a required setting.


## URL rewriting

Use the .htaccess file supplied.
	

## PHP information

The application is written as a self-contained PHP5 class.

The system should be error/warning/notice-free at error_reporting 2047.

