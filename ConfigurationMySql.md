# 1. Requirements #

Mollify requires MySQL database with mysqli or PDO interface installed.

# 2. Preparations #

Before installing Mollify, you have to create MySQL user and database for Mollify, and grant the new user CREATE/INSERT/UPDATE/DELETE rights for the created database.

# 3. Configuration #

## 3.1. MySQL database interface ##

```
<?php
	$CONFIGURATION = array(
		"db" => array(
			"type" => "mysql",
			"user" => "mollify",
			"password" => "mollify",
			"host" => "localhost", // optional
			"database" => "mollify", // optional
			"table_prefix" => "mollify_", // optional
			"charset" => "utf8", // optional
			"engine" => "innodb" // optional, used only in installation
		),
		...
	);
?>
```

Values "host", "database", "table\_prefix" and "engine" are optional. If these are not defined, it is assumed that MySQL server is running on localhost, database is called "mollify" and tables are accessed without name prefix.

With localhost database, you can also provide socket for the connection:

```
	"socket" => "/tmp/mysql5.sock";
```

Default MySQL engine used is InnoDB. With option "engine" you can change this (NOTE used only on installation).

```
	"engine" => "myisam";
```

## 3.2. PDO database interface ##

Alternatively you can use PDO database interface, for example:

```
<?php
	$CONFIGURATION = array(
		"db" => array(
			"type" => "pdo",
			"user" => "mollify",
			"password" => "mollify",
			"str" => "mysql:host=localhost;dbname=mollify",
			"table_prefix" => "mollify_", // optional
			"charset" => "utf8" // optional
		),
		...
	);
?>
```

# 4. Installation #

Start installation by opening following page into your browser: "`http://[URL_TO_MOLLIFY_BACKEND_DIR]/install/`", where `[URL_TO_MOLLIFY_BACKEND_DIR]` is the mollify backend folder where, for example, configuration.php and r.php are located.

Installer will check your configuration and system, and guide you through the installation.

# 5. Finishing Installation #

After Mollify installation is over, open Mollify with admin account and select "Configuration" tab to configure users and published folders.