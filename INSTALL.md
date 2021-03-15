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

Some discrete libraries are needed. These are also PHP class files.

They need to be in your PHP installation's include_path.

For instance, if your site is at `/var/www/example.org/` and your libraries are in a folder `/libraries/`, then the include_path must have `/var/www/example.org/libraries/` within it.

Download these libraries, available freely at the links given:

- [application.php](https://download.geog.cam.ac.uk/projects/application/)
- [database.php](https://download.geog.cam.ac.uk/projects/database/)
- [userAccount.php](https://download.geog.cam.ac.uk/projects/useraccount/)
- [ultimateForm.php](https://download.geog.cam.ac.uk/projects/ultimateform/) which has dependencies:
  - [pureContent.php](https://download.geog.cam.ac.uk/projects/purecontent/)
  - [timedate.php](https://download.geog.cam.ac.uk/projects/timedate/)


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

