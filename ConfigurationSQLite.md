# 1. Preparations #

Before installing Mollify, create configuration.php as in following example:

```
	<?php
		$CONFIGURATION = array(
			"db" => array(
				"type" => "sqlite",
				"file" => "db.file",
			),
			...
		);
	?>
```

Value "file" defines the location of the SQLite database file. Using absolute path is recommended.

For SQLite version 3, use type value "sqlite3".

Alternatively you can use PDO database interface, for example:

```
<?php
	$CONFIGURATION = array(
		"db" => array(
			"type" => "pdo",
			"user" => "mollify",
			"password" => "mollify",
			"str" => "sqlite:mollify.db"
		),
		...
	);
?>
```


# 2. Installation #

After creating configuration, open the installer "`http://[URL_TO_MOLLIFY_BACKEND_DIR]/install/`", where `[URL_TO_MOLLIFY_BACKEND_DIR]` is the mollify backend folder where, for example, configuration.php and r.php are located.

Installer will check your configuration and system, and guide you through the installation.